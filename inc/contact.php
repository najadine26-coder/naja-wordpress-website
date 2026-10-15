<?php
/**
 * Contact form backend.
 *
 * Messages sent from the Contact page are validated on the server, saved as
 * "Messages" in the admin menu (so none is lost even if email delivery fails)
 * and emailed to the address set in Appearance → Customize → Naja Restaurant →
 * Contact & location (default: the site admin email).
 *
 * The success reply is only sent once the message has really been saved or
 * emailed. There is deliberately no nonce: the page is often served from a
 * cache, which would make a nonce expire. Spam is handled with a honeypot
 * field, a minimum fill-in time and a per-visitor rate limit instead.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NAJA_CONTACT_MIN_SECONDS', 2 );
define( 'NAJA_CONTACT_MAX_PER_HOUR', 5 );

/** Admin menu: Messages (read-only list of what visitors sent). */
function naja_register_messages() {
	register_post_type(
		'naja_message',
		array(
			'labels'          => array(
				'name'          => __( 'Messages', 'naja' ),
				'singular_name' => __( 'Message', 'naja' ),
				'menu_name'     => __( 'Messages', 'naja' ),
				'edit_item'     => __( 'Message', 'naja' ),
				'search_items'  => __( 'Search messages', 'naja' ),
				'not_found'     => __( 'No messages yet.', 'naja' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'naja_register_messages' );

function naja_message_columns( $columns ) {
	return array(
		'cb'     => isset( $columns['cb'] ) ? $columns['cb'] : '',
		'title'  => __( 'Subject', 'naja' ),
		'sender' => __( 'From', 'naja' ),
		'phone'  => __( 'Phone', 'naja' ),
		'email'  => __( 'Email', 'naja' ),
		'date'   => __( 'Date', 'naja' ),
	);
}
add_filter( 'manage_naja_message_posts_columns', 'naja_message_columns' );

function naja_message_column( $column, $post_id ) {
	$map = array(
		'sender' => '_naja_name',
		'phone'  => '_naja_phone',
		'email'  => '_naja_email',
	);
	if ( isset( $map[ $column ] ) ) {
		echo esc_html( get_post_meta( $post_id, $map[ $column ], true ) );
	}
}
add_action( 'manage_naja_message_posts_custom_column', 'naja_message_column', 10, 2 );

/** Where messages are emailed. */
function naja_contact_recipient() {
	$to = sanitize_email( naja_mod( 'contact_email' ) );
	return is_email( $to ) ? $to : get_option( 'admin_email' );
}

/** Send a JSON error and stop. */
function naja_contact_fail( $code, $status = 400, $fields = array() ) {
	wp_send_json(
		array(
			'success' => false,
			'code'    => $code,
			'fields'  => $fields,
		),
		$status
	);
}

/** Visitor key for the rate limit: hashed so no IP address is stored. */
function naja_contact_visitor_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return 'naja_ct_' . md5( $ip . wp_salt( 'nonce' ) );
}

function naja_contact_submit() {
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) {
		naja_contact_fail( 'method', 405 );
	}

	// phpcs:disable WordPress.Security.NonceVerification
	$get = function ( $key ) {
		return isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
	};

	// Honeypot: real visitors never see or fill this field. Bots get a quiet "success".
	if ( '' !== trim( (string) $get( 'website' ) ) ) {
		wp_send_json( array( 'success' => true ) );
	}

	$name    = sanitize_text_field( $get( 'name' ) );
	$phone   = strtr( sanitize_text_field( $get( 'phone' ) ), array( '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9' ) );
	$email   = trim( sanitize_text_field( $get( 'email' ) ) );
	$subject = sanitize_text_field( $get( 'subject' ) );
	$message = sanitize_textarea_field( $get( 'message' ) );
	$token   = preg_replace( '/[^a-zA-Z0-9\-]/', '', (string) $get( 'token' ) );
	$lang    = 'en' === $get( 'lang' ) ? 'en' : 'ar';
	$started = absint( $get( 'ts' ) );
	// phpcs:enable

	// Same fields the browser checks; the server never trusts the browser.
	$errors = array();
	if ( '' === $name || mb_strlen( $name ) > 100 ) {
		$errors['name'] = 'required';
	}
	$digits = preg_replace( '/\D+/', '', $phone );
	if ( '' === $phone ) {
		$errors['phone'] = 'required';
	} elseif ( ! preg_match( '/^[0-9+\-()\s.]{7,25}$/', $phone ) || strlen( $digits ) < 7 || strlen( $digits ) > 15 ) {
		$errors['phone'] = 'phone';
	}
	if ( '' !== $email && ( ! is_email( $email ) || mb_strlen( $email ) > 120 ) ) {
		$errors['email'] = 'email';
	}
	if ( '' === $subject || mb_strlen( $subject ) > 150 ) {
		$errors['subject'] = 'required';
	}
	if ( '' === $message ) {
		$errors['message'] = 'required';
	} elseif ( mb_strlen( $message ) < 10 ) {
		$errors['message'] = 'short';
	} elseif ( mb_strlen( $message ) > 2000 ) {
		$errors['message'] = 'long';
	}
	if ( $errors ) {
		naja_contact_fail( 'invalid', 422, $errors );
	}

	// Submitted faster than a person can type.
	if ( $started && ( time() - $started ) < NAJA_CONTACT_MIN_SECONDS ) {
		naja_contact_fail( 'too_fast', 429 );
	}

	// The same submission arriving twice (double click, retry) is stored once.
	if ( '' !== $token ) {
		$dupe_key = 'naja_ct_tok_' . md5( $token );
		if ( get_transient( $dupe_key ) ) {
			wp_send_json( array( 'success' => true, 'duplicate' => true ) );
		}
	}

	$rate_key = naja_contact_visitor_key();
	$count    = (int) get_transient( $rate_key );
	if ( $count >= NAJA_CONTACT_MAX_PER_HOUR ) {
		naja_contact_fail( 'rate', 429 );
	}

	// 1) Save it, so it is never lost.
	$saved   = false;
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'naja_message',
			'post_status'  => 'private',
			'post_title'   => $subject,
			'post_content' => $message,
			'meta_input'   => array(
				'_naja_name'  => $name,
				'_naja_phone' => $phone,
				'_naja_email' => $email,
				'_naja_lang'  => $lang,
			),
		),
		true
	);
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		$saved = true;
	}

	// 2) Email it.
	$site    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( '' !== $email ) {
		$headers[] = 'Reply-To: ' . $email;
	}
	$body = "New message from the Contact page\n\n"
		. "Name: {$name}\n"
		. "Phone: {$phone}\n"
		. 'Email: ' . ( '' !== $email ? $email : '-' ) . "\n"
		. "Language: {$lang}\n"
		. "Subject: {$subject}\n\n"
		. $message . "\n";
	$mailed = wp_mail( naja_contact_recipient(), '[' . $site . '] ' . $subject, $body, $headers );

	if ( ! $saved && ! $mailed ) {
		naja_contact_fail( 'server', 500 );
	}

	set_transient( $rate_key, $count + 1, HOUR_IN_SECONDS );
	if ( '' !== $token ) {
		set_transient( 'naja_ct_tok_' . md5( $token ), 1, 10 * MINUTE_IN_SECONDS );
	}

	wp_send_json(
		array(
			'success' => true,
			'saved'   => $saved,
			'mailed'  => (bool) $mailed,
		)
	);
}
add_action( 'wp_ajax_nopriv_naja_contact', 'naja_contact_submit' );
add_action( 'wp_ajax_naja_contact', 'naja_contact_submit' );
