<?php
/**
 * GitHub Sync: shows which commit of the theme is on the site, tells the
 * admin when a newer commit lands on GitHub (admin notice, admin bar, email)
 * and can update the theme from GitHub with one click.
 *
 * Appearance → GitHub Sync.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NAJA_SYNC_REPO', 'najadine26-coder/naja-wordpress-website' );
define( 'NAJA_SYNC_BRANCH', 'main' );
define( 'NAJA_SYNC_CACHE_TTL', 10 * MINUTE_IN_SECONDS );

/**
 * GET a GitHub API path for the repo.
 *
 * @param string $path Path after /repos/{owner}/{repo}, e.g. "/commits/main".
 * @return array|WP_Error { body: mixed, link: string }
 */
function naja_sync_api( $path ) {
	$headers = array(
		'Accept'     => 'application/vnd.github+json',
		'User-Agent' => 'naja-theme-sync',
	);
	$token = get_option( 'naja_sync_token', '' );
	if ( $token ) {
		$headers['Authorization'] = 'Bearer ' . $token;
	}

	$response = wp_remote_get(
		'https://api.github.com/repos/' . NAJA_SYNC_REPO . $path,
		array(
			'headers' => $headers,
			'timeout' => 15,
		)
	);
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( 200 !== $code ) {
		$message = is_array( $body ) && ! empty( $body['message'] ) ? $body['message'] : 'HTTP ' . $code;
		return new WP_Error( 'naja_sync_api', 'GitHub: ' . $message );
	}

	return array(
		'body' => $body,
		'link' => (string) wp_remote_retrieve_header( $response, 'link' ),
	);
}

/** Commit data from a GitHub commit object. */
function naja_sync_commit_fields( $commit ) {
	$message = isset( $commit['commit']['message'] ) ? $commit['commit']['message'] : '';
	return array(
		'sha'     => $commit['sha'],
		'short'   => substr( $commit['sha'], 0, 7 ),
		'message' => strtok( $message, "\n" ),
		'date'    => isset( $commit['commit']['committer']['date'] ) ? $commit['commit']['committer']['date'] : '',
		'author'  => isset( $commit['commit']['author']['name'] ) ? $commit['commit']['author']['name'] : '',
		'url'     => isset( $commit['html_url'] ) ? $commit['html_url'] : '',
	);
}

/**
 * Latest commit on the branch, with its commit number (1 = first commit).
 *
 * @return array|WP_Error
 */
function naja_sync_fetch_latest() {
	$result = naja_sync_api( '/commits?sha=' . rawurlencode( NAJA_SYNC_BRANCH ) . '&per_page=1' );
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	if ( empty( $result['body'][0]['sha'] ) ) {
		return new WP_Error( 'naja_sync_empty', 'GitHub-এ কোনো commit পাওয়া যায়নি।' );
	}

	// With one commit per page, the last page number is the total commit count.
	$number = 1;
	if ( preg_match( '/[?&]page=(\d+)>;\s*rel="last"/', $result['link'], $m ) ) {
		$number = (int) $m[1];
	}

	$latest           = naja_sync_commit_fields( $result['body'][0] );
	$latest['number'] = $number;
	return $latest;
}

/**
 * Commit SHA from a .git folder in the theme (Hostinger Git deploys keep one).
 *
 * @return string SHA or "".
 */
function naja_sync_git_head() {
	$git  = get_template_directory() . '/.git';
	$head = @file_get_contents( $git . '/HEAD' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
	if ( ! $head ) {
		return '';
	}
	$head = trim( $head );
	if ( preg_match( '/^[0-9a-f]{40}$/', $head ) ) {
		return $head;
	}
	if ( 0 !== strpos( $head, 'ref: ' ) ) {
		return '';
	}

	$ref = substr( $head, 5 );
	$sha = @file_get_contents( $git . '/' . $ref ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
	if ( $sha && preg_match( '/^[0-9a-f]{40}/', trim( $sha ), $m ) ) {
		return $m[0];
	}

	$packed = @file_get_contents( $git . '/packed-refs' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
	if ( $packed && preg_match( '/^([0-9a-f]{40}) ' . preg_quote( $ref, '/' ) . '$/m', $packed, $m ) ) {
		return $m[1];
	}
	return '';
}

/**
 * The commit that is on this site: from .git if present, else the one
 * recorded by the last sync or "mark as installed".
 *
 * @return array|null { sha, source }
 */
function naja_sync_installed() {
	$sha = naja_sync_git_head();
	if ( $sha ) {
		return array(
			'sha'    => $sha,
			'source' => 'git',
		);
	}
	$saved = get_option( 'naja_sync_installed' );
	if ( is_array( $saved ) && ! empty( $saved['sha'] ) ) {
		return array(
			'sha'    => $saved['sha'],
			'source' => 'sync',
		);
	}
	return null;
}

/**
 * Compare the installed commit with GitHub. Cached for NAJA_SYNC_CACHE_TTL.
 *
 * @param bool $force Skip the cache.
 * @return array {
 *     state: up_to_date|behind|unknown|error,
 *     latest: array|null, installed: array|null,
 *     behind: int, commits: array, error: string, checked: int
 * }
 */
function naja_sync_status( $force = false ) {
	$installed = naja_sync_installed();
	$cached    = get_transient( 'naja_sync_status' );
	$key       = $installed ? $installed['sha'] : '';

	if ( ! $force && is_array( $cached ) && $cached['key'] === $key ) {
		return $cached;
	}

	$status = array(
		'key'       => $key,
		'state'     => 'unknown',
		'latest'    => null,
		'installed' => $installed,
		'behind'    => 0,
		'commits'   => array(),
		'error'     => '',
		'checked'   => time(),
	);

	$latest = naja_sync_fetch_latest();
	if ( is_wp_error( $latest ) ) {
		$status['state'] = 'error';
		$status['error'] = $latest->get_error_message();
		set_transient( 'naja_sync_status', $status, 2 * MINUTE_IN_SECONDS );
		return $status;
	}
	$status['latest'] = $latest;

	if ( $installed ) {
		$status['installed']['short'] = substr( $installed['sha'], 0, 7 );

		if ( $installed['sha'] === $latest['sha'] ) {
			$status['state']                = 'up_to_date';
			$status['installed']['number']  = $latest['number'];
			$status['installed']['message'] = $latest['message'];
			$status['installed']['date']    = $latest['date'];
		} else {
			$compare = naja_sync_api( '/compare/' . $installed['sha'] . '...' . $latest['sha'] );
			if ( is_wp_error( $compare ) ) {
				// The installed commit may not exist on GitHub any more.
				$status['state'] = 'behind';
			} else {
				$body             = $compare['body'];
				$status['state']  = 0 === (int) $body['ahead_by'] ? 'up_to_date' : 'behind';
				$status['behind'] = (int) $body['ahead_by'];
				if ( 0 === (int) $body['behind_by'] ) {
					$status['installed']['number'] = $latest['number'] - (int) $body['ahead_by'];
				}
				if ( ! empty( $body['base_commit'] ) ) {
					$base                           = naja_sync_commit_fields( $body['base_commit'] );
					$status['installed']['message'] = $base['message'];
					$status['installed']['date']    = $base['date'];
				}
				$commits = isset( $body['commits'] ) ? array_reverse( $body['commits'] ) : array();
				$number  = $latest['number'];
				foreach ( array_slice( $commits, 0, 20 ) as $commit ) {
					$fields           = naja_sync_commit_fields( $commit );
					$fields['number'] = $number--;
					$status['commits'][] = $fields;
				}
			}
		}
	}

	set_transient( 'naja_sync_status', $status, NAJA_SYNC_CACHE_TTL );
	return $status;
}

/** Version string for enqueued assets, so browsers fetch new CSS/JS after a sync. */
function naja_sync_asset_version() {
	$installed = naja_sync_installed();
	return $installed ? NAJA_VERSION . '-' . substr( $installed['sha'], 0, 7 ) : NAJA_VERSION;
}

/** Commit label like "#12 · a1b2c3d". */
function naja_sync_label( $commit ) {
	$label = isset( $commit['number'] ) ? '#' . $commit['number'] . ' · ' : '';
	return $label . $commit['short'];
}

/** Local date/time for an ISO date from GitHub. */
function naja_sync_date( $iso ) {
	$time = $iso ? strtotime( $iso ) : 0;
	return $time ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $time ) : '';
}

/* ---------------------------------------------------------------------------
 * Admin page
 * ------------------------------------------------------------------------ */

function naja_sync_admin_menu() {
	$count = naja_sync_pending_count();
	$badge = $count ? ' <span class="awaiting-mod">' . (int) $count . '</span>' : '';
	add_theme_page( 'GitHub Sync', 'GitHub Sync' . $badge, 'manage_options', 'naja-github-sync', 'naja_sync_admin_page' );
}
add_action( 'admin_menu', 'naja_sync_admin_menu' );

/** New commits waiting, from the cache only (never calls GitHub). */
function naja_sync_pending_count() {
	$status = get_transient( 'naja_sync_status' );
	return is_array( $status ) && 'behind' === $status['state'] ? max( 1, (int) $status['behind'] ) : 0;
}

function naja_sync_page_url( $args = array() ) {
	return add_query_arg( $args, admin_url( 'themes.php?page=naja-github-sync' ) );
}

function naja_sync_action_button( $action, $label, $primary = false ) {
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;margin:0 8px 8px 0">
		<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
		<?php wp_nonce_field( $action ); ?>
		<button type="submit" class="button<?php echo $primary ? ' button-primary' : ''; ?>"><?php echo esc_html( $label ); ?></button>
	</form>
	<?php
}

function naja_sync_admin_page() {
	$status = naja_sync_status();
	$notice = isset( $_GET['naja_sync'] ) ? sanitize_key( $_GET['naja_sync'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$error  = get_transient( 'naja_sync_last_error' );

	$messages = array(
		'checked'  => array( 'success', 'GitHub থেকে নতুন করে চেক করা হয়েছে।' ),
		'synced'   => array( 'success', 'থিম GitHub-এর সর্বশেষ commit-এ আপডেট হয়েছে।' ),
		'marked'   => array( 'success', 'সর্বশেষ commit সাইটে আছে বলে চিহ্নিত করা হয়েছে।' ),
		'saved'    => array( 'success', 'সেটিংস সেভ হয়েছে।' ),
		'failed'   => array( 'error', 'আপডেট হয়নি: ' . ( $error ? $error : 'অজানা সমস্যা' ) ),
	);
	?>
	<div class="wrap">
		<h1>GitHub Sync</h1>
		<p>
			Repository: <a href="<?php echo esc_url( 'https://github.com/' . NAJA_SYNC_REPO ); ?>" target="_blank" rel="noopener"><?php echo esc_html( NAJA_SYNC_REPO ); ?></a>
			· Branch: <code><?php echo esc_html( NAJA_SYNC_BRANCH ); ?></code>
		</p>

		<?php if ( isset( $messages[ $notice ] ) ) : ?>
			<div class="notice notice-<?php echo esc_attr( $messages[ $notice ][0] ); ?> is-dismissible"><p><?php echo esc_html( $messages[ $notice ][1] ); ?></p></div>
		<?php endif; ?>

		<?php if ( 'error' === $status['state'] ) : ?>
			<div class="notice notice-error"><p><?php echo esc_html( 'GitHub-এর সাথে যোগাযোগ হয়নি: ' . $status['error'] ); ?></p></div>
		<?php elseif ( 'up_to_date' === $status['state'] ) : ?>
			<div class="notice notice-success inline"><p><strong>✅ সাইট আপ-টু-ডেট।</strong> GitHub-এর সর্বশেষ commit সাইটে আছে।</p></div>
		<?php elseif ( 'behind' === $status['state'] ) : ?>
			<div class="notice notice-warning inline"><p><strong>🔔 নতুন আপডেট এসেছে।</strong> GitHub-এ <?php echo (int) max( 1, $status['behind'] ); ?>টি নতুন commit আছে যা সাইটে নেই।</p></div>
		<?php else : ?>
			<div class="notice notice-info inline"><p>সাইটে কোন commit আছে জানা যাচ্ছে না। সাইট যদি GitHub-এর সর্বশেষ অবস্থায় থাকে, নিচে "এটাই সাইটে আছে" চাপুন; না হলে "এখনই Sync করুন" চাপুন।</p></div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:900px;margin-top:16px">
			<thead><tr><th></th><th>Commit</th><th>বার্তা</th><th>সময়</th></tr></thead>
			<tbody>
				<tr>
					<td><strong>সাইটে আছে</strong></td>
					<?php if ( $status['installed'] ) : ?>
						<td><code><?php echo esc_html( naja_sync_label( $status['installed'] ) ); ?></code></td>
						<td><?php echo esc_html( isset( $status['installed']['message'] ) ? $status['installed']['message'] : '' ); ?></td>
						<td><?php echo esc_html( naja_sync_date( isset( $status['installed']['date'] ) ? $status['installed']['date'] : '' ) ); ?></td>
					<?php else : ?>
						<td colspan="3">অজানা</td>
					<?php endif; ?>
				</tr>
				<tr>
					<td><strong>GitHub-এ সর্বশেষ</strong></td>
					<?php if ( $status['latest'] ) : ?>
						<td><a href="<?php echo esc_url( $status['latest']['url'] ); ?>" target="_blank" rel="noopener"><code><?php echo esc_html( naja_sync_label( $status['latest'] ) ); ?></code></a></td>
						<td><?php echo esc_html( $status['latest']['message'] ); ?></td>
						<td><?php echo esc_html( naja_sync_date( $status['latest']['date'] ) ); ?></td>
					<?php else : ?>
						<td colspan="3">—</td>
					<?php endif; ?>
				</tr>
			</tbody>
		</table>

		<?php if ( $status['commits'] ) : ?>
			<h2>নতুন commit গুলো</h2>
			<table class="widefat striped" style="max-width:900px">
				<thead><tr><th>Commit</th><th>বার্তা</th><th>কে</th><th>সময়</th></tr></thead>
				<tbody>
				<?php foreach ( $status['commits'] as $commit ) : ?>
					<tr>
						<td><a href="<?php echo esc_url( $commit['url'] ); ?>" target="_blank" rel="noopener"><code><?php echo esc_html( naja_sync_label( $commit ) ); ?></code></a></td>
						<td><?php echo esc_html( $commit['message'] ); ?></td>
						<td><?php echo esc_html( $commit['author'] ); ?></td>
						<td><?php echo esc_html( naja_sync_date( $commit['date'] ) ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<p style="margin-top:20px">
			<?php
			naja_sync_action_button( 'naja_sync_check', 'আবার চেক করুন' );
			if ( current_user_can( 'update_themes' ) && $status['latest'] && 'up_to_date' !== $status['state'] ) {
				naja_sync_action_button( 'naja_sync_update', 'এখনই Sync করুন', true );
			}
			if ( $status['latest'] && 'up_to_date' !== $status['state'] && ( ! $status['installed'] || 'sync' === $status['installed']['source'] ) ) {
				naja_sync_action_button( 'naja_sync_mark', 'এটাই সাইটে আছে' );
			}
			?>
		</p>
		<p class="description">
			শেষ চেক: <?php echo esc_html( wp_date( get_option( 'time_format' ), $status['checked'] ) ); ?>.
			প্রতি ঘণ্টায় নিজে থেকে চেক হয়।
			"এখনই Sync করুন" GitHub থেকে থিম নামিয়ে এই থিম ফোল্ডারটা পুরো বদলে দেয়।
		</p>

		<h2>নোটিফিকেশন সেটিংস</h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="naja_sync_settings">
			<?php wp_nonce_field( 'naja_sync_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">ইমেইল</th>
					<td>
						<label>
							<input type="checkbox" name="naja_sync_email" value="1" <?php checked( get_option( 'naja_sync_email', '1' ), '1' ); ?>>
							নতুন commit এলে <code><?php echo esc_html( get_option( 'admin_email' ) ); ?></code>-এ ইমেইল পাঠাও
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="naja_sync_token">GitHub token (ঐচ্ছিক)</label></th>
					<td>
						<input type="password" id="naja_sync_token" name="naja_sync_token" class="regular-text" autocomplete="off" placeholder="<?php echo get_option( 'naja_sync_token' ) ? esc_attr( '•••••••• (সেভ করা আছে)' ) : ''; ?>">
						<p class="description">শুধু repository private হলে বা GitHub "rate limit" দেখালে দরকার। খালি রাখলে আগের token থাকবে; মুছতে <code>-</code> লিখুন।</p>
					</td>
				</tr>
			</table>
			<?php submit_button( 'সেভ করুন' ); ?>
		</form>
	</div>
	<?php
}

/* ---------------------------------------------------------------------------
 * Actions
 * ------------------------------------------------------------------------ */

function naja_sync_handle_check() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'naja_sync_check' );
	naja_sync_status( true );
	wp_safe_redirect( naja_sync_page_url( array( 'naja_sync' => 'checked' ) ) );
	exit;
}
add_action( 'admin_post_naja_sync_check', 'naja_sync_handle_check' );

function naja_sync_handle_mark() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'naja_sync_mark' );
	$latest = naja_sync_fetch_latest();
	if ( ! is_wp_error( $latest ) ) {
		naja_sync_record_installed( $latest );
	}
	wp_safe_redirect( naja_sync_page_url( array( 'naja_sync' => 'marked' ) ) );
	exit;
}
add_action( 'admin_post_naja_sync_mark', 'naja_sync_handle_mark' );

function naja_sync_handle_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'naja_sync_settings' );
	update_option( 'naja_sync_email', empty( $_POST['naja_sync_email'] ) ? '0' : '1' );

	$token = isset( $_POST['naja_sync_token'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['naja_sync_token'] ) ) ) : '';
	if ( '-' === $token ) {
		delete_option( 'naja_sync_token' );
	} elseif ( '' !== $token ) {
		update_option( 'naja_sync_token', $token, false );
	}
	delete_transient( 'naja_sync_status' );

	wp_safe_redirect( naja_sync_page_url( array( 'naja_sync' => 'saved' ) ) );
	exit;
}
add_action( 'admin_post_naja_sync_settings', 'naja_sync_handle_settings' );

/** Remember the commit now on the site and clear the "update available" state. */
function naja_sync_record_installed( $commit ) {
	update_option(
		'naja_sync_installed',
		array(
			'sha'  => $commit['sha'],
			'time' => time(),
		)
	);
	update_option( 'naja_sync_notified', $commit['sha'] );
	delete_transient( 'naja_sync_status' );
}

/**
 * Download the latest commit from GitHub and replace this theme's folder with it.
 */
function naja_sync_handle_update() {
	if ( ! current_user_can( 'update_themes' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'naja_sync_update' );

	$latest = naja_sync_fetch_latest();
	if ( is_wp_error( $latest ) ) {
		naja_sync_fail( $latest->get_error_message() );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/theme.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	if ( ! WP_Filesystem() ) {
		naja_sync_fail( 'সার্ভারে ফাইল লেখার অনুমতি নেই (WP_Filesystem)।' );
	}

	$package = 'https://github.com/' . NAJA_SYNC_REPO . '/archive/' . $latest['sha'] . '.zip';

	add_filter( 'upgrader_source_selection', 'naja_sync_rename_source', 10, 2 );
	$skin     = new WP_Ajax_Upgrader_Skin();
	$upgrader = new Theme_Upgrader( $skin );
	$result   = $upgrader->install( $package, array( 'overwrite_package' => true ) );
	remove_filter( 'upgrader_source_selection', 'naja_sync_rename_source', 10 );

	if ( is_wp_error( $result ) ) {
		naja_sync_fail( $result->get_error_message() );
	}
	if ( ! $result ) {
		$errors = $skin->get_errors();
		naja_sync_fail( $errors->has_errors() ? $errors->get_error_message() : implode( ' ', $skin->get_upgrade_messages() ) );
	}

	naja_sync_record_installed( $latest );
	wp_clean_themes_cache();
	wp_safe_redirect( naja_sync_page_url( array( 'naja_sync' => 'synced' ) ) );
	exit;
}
add_action( 'admin_post_naja_sync_update', 'naja_sync_handle_update' );

/**
 * GitHub zips unpack to "{repo}-{sha}/"; rename it to this theme's folder name
 * so the upgrader replaces the installed theme.
 */
function naja_sync_rename_source( $source, $remote_source ) {
	global $wp_filesystem;
	$target = trailingslashit( $remote_source ) . get_template() . '/';
	if ( untrailingslashit( $source ) === untrailingslashit( $target ) ) {
		return $source;
	}
	if ( ! $wp_filesystem->move( $source, $target, true ) ) {
		return new WP_Error( 'naja_sync_rename', 'ডাউনলোড করা ফোল্ডারের নাম বদলানো যায়নি।' );
	}
	return $target;
}

function naja_sync_fail( $message ) {
	set_transient( 'naja_sync_last_error', $message, 5 * MINUTE_IN_SECONDS );
	wp_safe_redirect( naja_sync_page_url( array( 'naja_sync' => 'failed' ) ) );
	exit;
}

/* ---------------------------------------------------------------------------
 * Notifications: admin notice, admin bar, hourly email check
 * ------------------------------------------------------------------------ */

function naja_sync_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_naja-github-sync' === $screen->id ) {
		return;
	}
	$status = get_transient( 'naja_sync_status' );
	if ( ! is_array( $status ) || 'behind' !== $status['state'] || empty( $status['latest'] ) ) {
		return;
	}
	?>
	<div class="notice notice-warning">
		<p>
			<strong>🔔 থিমের নতুন আপডেট এসেছে:</strong>
			<code><?php echo esc_html( naja_sync_label( $status['latest'] ) ); ?></code>
			<?php echo esc_html( $status['latest']['message'] ); ?>
			— <a href="<?php echo esc_url( naja_sync_page_url() ); ?>">GitHub Sync দেখুন</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'naja_sync_admin_notice' );

function naja_sync_admin_bar( $bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$count = naja_sync_pending_count();
	if ( ! $count ) {
		return;
	}
	$bar->add_node(
		array(
			'id'    => 'naja-github-sync',
			'title' => '🔔 থিম আপডেট (' . (int) $count . ')',
			'href'  => naja_sync_page_url(),
		)
	);
}
add_action( 'admin_bar_menu', 'naja_sync_admin_bar', 100 );

/** Keep the cache warm in wp-admin so notices appear without opening the page. */
function naja_sync_admin_refresh() {
	if ( current_user_can( 'manage_options' ) && ! wp_doing_ajax() ) {
		naja_sync_status();
	}
}
add_action( 'admin_init', 'naja_sync_admin_refresh' );

function naja_sync_schedule() {
	if ( ! wp_next_scheduled( 'naja_sync_cron' ) ) {
		wp_schedule_event( time() + MINUTE_IN_SECONDS, 'hourly', 'naja_sync_cron' );
	}
}
add_action( 'init', 'naja_sync_schedule' );

function naja_sync_unschedule() {
	wp_clear_scheduled_hook( 'naja_sync_cron' );
}
add_action( 'switch_theme', 'naja_sync_unschedule' );

/** Hourly: check GitHub and email the admin once per new commit. */
function naja_sync_cron() {
	$status = naja_sync_status( true );
	if ( 'behind' !== $status['state'] || empty( $status['latest'] ) ) {
		return;
	}

	$latest   = $status['latest'];
	$notified = get_option( 'naja_sync_notified', '' );
	if ( $notified === $latest['sha'] ) {
		return;
	}
	update_option( 'naja_sync_notified', $latest['sha'] );

	if ( '1' !== get_option( 'naja_sync_email', '1' ) ) {
		return;
	}

	$lines = array();
	foreach ( $status['commits'] as $commit ) {
		$lines[] = naja_sync_label( $commit ) . '  ' . $commit['message'] . ' (' . $commit['author'] . ')';
	}
	if ( ! $lines ) {
		$lines[] = naja_sync_label( $latest ) . '  ' . $latest['message'];
	}

	$subject = sprintf( '[%s] থিমের নতুন আপডেট: %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), naja_sync_label( $latest ) );
	$body    = "GitHub-এ থিমের নতুন commit এসেছে যা সাইটে এখনো নেই:\n\n"
		. implode( "\n", $lines )
		. "\n\nদেখতে ও Sync করতে: " . naja_sync_page_url() . "\n";

	wp_mail( get_option( 'admin_email' ), $subject, $body );
}
add_action( 'naja_sync_cron', 'naja_sync_cron' );
