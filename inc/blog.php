<?php
/**
 * Blog: the "Articles" post type, its listing (/blog/) and single pages
 * (/blog/{slug}/), SEO output, sitemap entries and first-run content.
 *
 * Articles are ordinary WordPress content, so they can be edited, added and
 * deleted under Articles in the admin menu. The 15 starter articles live in
 * inc/articles/*.php and are copied into the database once (see
 * naja_blog_seed()); later edits are never overwritten.
 *
 * Article text is written in English and marked lang="en" in the templates.
 * Interface text (navigation, labels, buttons) uses the site's translation
 * table (bl.* keys in js/translations.js).
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NAJA_BLOG_VERSION', '1' );

/* ---------------------------------------------------------------------------
 * Post type + categories
 * ------------------------------------------------------------------------ */

function naja_register_blog() {
	register_taxonomy(
		'naja_topic',
		'naja_article',
		array(
			'labels'             => array(
				'name'          => __( 'Article categories', 'naja' ),
				'singular_name' => __( 'Article category', 'naja' ),
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'hierarchical'       => true,
			'rewrite'            => false,
		)
	);

	register_post_type(
		'naja_article',
		array(
			'labels'        => array(
				'name'          => __( 'Articles', 'naja' ),
				'singular_name' => __( 'Article', 'naja' ),
				'add_new_item'  => __( 'Add new article', 'naja' ),
				'edit_item'     => __( 'Edit article', 'naja' ),
				'all_items'     => __( 'All articles', 'naja' ),
				'menu_name'     => __( 'Articles', 'naja' ),
			),
			'public'        => true,
			'has_archive'   => 'blog',
			'rewrite'       => array( 'slug' => 'blog', 'with_front' => false ),
			'menu_icon'     => 'dashicons-media-document',
			'menu_position' => 25,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'taxonomies'    => array( 'naja_topic' ),
		)
	);
}
add_action( 'init', 'naja_register_blog' );

function naja_is_blog_archive() {
	return is_post_type_archive( 'naja_article' );
}

function naja_is_article() {
	return is_singular( 'naja_article' );
}

function naja_blog_url() {
	$url = get_post_type_archive_link( 'naja_article' );
	return $url ? $url : home_url( '/blog/' );
}

/** Blog link attributes for the navigation (carries the language like the Menu link). */
function naja_blog_link() {
	$url     = naja_blog_url();
	$current = ( naja_is_blog_archive() || naja_is_article() ) ? ' aria-current="page"' : '';
	return 'href="' . esc_url( $url ) . '" data-page-link="' . esc_url( $url ) . '"' . $current;
}

/** The listing shows every article, in the order set by the starter content. */
function naja_blog_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'naja_article' ) ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
}
add_action( 'pre_get_posts', 'naja_blog_query' );

/* ---------------------------------------------------------------------------
 * Article data
 * ------------------------------------------------------------------------ */

/** Starter category labels (Arabic labels live in js/translations.js as bl.cat.*). */
function naja_blog_categories() {
	return array(
		'guides' => 'Dining Guides',
		'saudi'  => 'Saudi & Middle Eastern',
		'grills' => 'Grills & BBQ',
		'family' => 'Family & Casual',
		'tips'   => 'Planning & Tips',
	);
}

/** Category of an article: array( slug, name ) or empty. */
function naja_article_category( $post_id = 0 ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), 'naja_topic' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array( 'slug' => '', 'name' => '' );
	}
	$term = reset( $terms );
	return array( 'slug' => $term->slug, 'name' => wp_specialchars_decode( $term->name ) );
}

function naja_unsplash_url( $photo_id, $width ) {
	return 'https://images.unsplash.com/photo-' . $photo_id . '?auto=format&fit=crop&w=' . (int) $width . '&q=80';
}

/**
 * Featured image of an article: the Featured image if one is set in the admin,
 * otherwise the Unsplash placeholder stored with the article.
 *
 * @return array { src, srcset, alt, width, height }
 */
function naja_article_image( $post_id = 0, $width = 1200 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$alt     = (string) get_post_meta( $post_id, '_naja_image_alt', true );

	$thumb = get_post_thumbnail_id( $post_id );
	if ( $thumb ) {
		$image = wp_get_attachment_image_src( $thumb, 'full' );
		$wp_alt = trim( (string) get_post_meta( $thumb, '_wp_attachment_image_alt', true ) );
		return array(
			'src'    => $image[0],
			'srcset' => (string) wp_get_attachment_image_srcset( $thumb, 'full' ),
			'alt'    => '' !== $wp_alt ? $wp_alt : $alt,
			'width'  => (int) $image[1],
			'height' => (int) $image[2],
		);
	}

	$photo = (string) get_post_meta( $post_id, '_naja_image_id', true );
	if ( '' === $photo ) {
		return array( 'src' => '', 'srcset' => '', 'alt' => $alt, 'width' => 1200, 'height' => 800 );
	}
	$set = array();
	foreach ( array( 480, 800, 1200, 1600 ) as $w ) {
		$set[] = naja_unsplash_url( $photo, $w ) . ' ' . $w . 'w';
	}
	return array(
		'src'    => naja_unsplash_url( $photo, $width ),
		'srcset' => implode( ', ', $set ),
		'alt'    => $alt,
		'width'  => 1200,
		'height' => 800,
	);
}

/** FAQ of an article: list of array( q, a ). */
function naja_article_faq( $post_id = 0 ) {
	$faq = get_post_meta( $post_id ? $post_id : get_the_ID(), '_naja_faq', true );
	return is_array( $faq ) ? $faq : array();
}

function naja_article_reading_minutes( $post_id = 0 ) {
	$text  = wp_strip_all_tags( get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() ) );
	$words = str_word_count( $text );
	return max( 1, (int) ceil( $words / 200 ) );
}

function naja_article_description( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$desc    = trim( (string) get_post_meta( $post_id, '_naja_description', true ) );
	return '' !== $desc ? $desc : wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post_id ) ), 30 );
}

function naja_article_seo_title( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$title   = trim( (string) get_post_meta( $post_id, '_naja_seo_title', true ) );
	return '' !== $title ? $title : get_the_title( $post_id );
}

/** Articles related to the given one: the ones it lists, topped up from the same category. */
function naja_article_related( $post_id, $limit = 3 ) {
	$slugs = get_post_meta( $post_id, '_naja_related', true );
	$ids   = array();
	if ( is_array( $slugs ) ) {
		foreach ( $slugs as $slug ) {
			$found = get_page_by_path( $slug, OBJECT, 'naja_article' );
			if ( $found && 'publish' === $found->post_status && (int) $found->ID !== (int) $post_id ) {
				$ids[] = $found->ID;
			}
		}
	}
	if ( count( $ids ) < $limit ) {
		$more = get_posts(
			array(
				'post_type'      => 'naja_article',
				'post_status'    => 'publish',
				'posts_per_page' => $limit * 2,
				'post__not_in'   => array_merge( $ids, array( $post_id ) ),
				'orderby'        => array( 'menu_order' => 'ASC' ),
				'fields'         => 'ids',
			)
		);
		$ids = array_merge( $ids, $more );
	}
	return array_slice( array_unique( $ids ), 0, $limit );
}

/* ---------------------------------------------------------------------------
 * @@post/slug@@ links in article text → real URLs (works with any permalink setup)
 * ------------------------------------------------------------------------ */

function naja_blog_expand_links( $content ) {
	if ( false === strpos( $content, '@@' ) ) {
		return $content;
	}
	return preg_replace_callback(
		'/@@(post|page)\/([a-z0-9\-]+)@@/',
		function ( $m ) {
			if ( 'post' === $m[1] ) {
				$found = get_page_by_path( $m[2], OBJECT, 'naja_article' );
				return $found ? esc_url( get_permalink( $found ) ) : esc_url( naja_blog_url() );
			}
			$pages = array(
				'home'       => home_url( '/' ),
				'blog'       => naja_blog_url(),
				'menu'       => naja_menu_url(),
				'contact'    => naja_contact_url(),
				'gallery'    => naja_gallery_url(),
				'experience' => naja_experience_url(),
			);
			return esc_url( isset( $pages[ $m[2] ] ) ? $pages[ $m[2] ] : home_url( '/' ) );
		},
		$content
	);
}
add_filter( 'the_content', 'naja_blog_expand_links', 9 );

/* ---------------------------------------------------------------------------
 * Admin: article details box
 * ------------------------------------------------------------------------ */

function naja_article_meta_box() {
	add_meta_box( 'naja_article_details', __( 'SEO & article details', 'naja' ), 'naja_article_meta_box_html', 'naja_article', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'naja_article_meta_box' );

function naja_article_meta_box_html( $post ) {
	wp_nonce_field( 'naja_article_save', 'naja_article_nonce' );
	$get = function ( $key ) use ( $post ) {
		return get_post_meta( $post->ID, $key, true );
	};
	$faq_lines = '';
	foreach ( naja_article_faq( $post->ID ) as $row ) {
		$faq_lines .= $row['q'] . ' | ' . $row['a'] . "\n";
	}
	$related = $get( '_naja_related' );
	?>
	<p class="description"><?php esc_html_e( 'The title above is the H1. Everything here is optional; empty fields fall back to the article title and excerpt.', 'naja' ); ?></p>
	<table class="form-table" role="presentation">
		<tr><th><label for="naja_seo_title"><?php esc_html_e( 'SEO title', 'naja' ); ?></label></th>
			<td><input type="text" id="naja_seo_title" name="naja_seo_title" class="large-text" maxlength="70" value="<?php echo esc_attr( $get( '_naja_seo_title' ) ); ?>"></td></tr>
		<tr><th><label for="naja_description"><?php esc_html_e( 'Meta description', 'naja' ); ?></label></th>
			<td><textarea id="naja_description" name="naja_description" class="large-text" rows="2" maxlength="170"><?php echo esc_textarea( $get( '_naja_description' ) ); ?></textarea></td></tr>
		<tr><th><label for="naja_keywords"><?php esc_html_e( 'Keywords (for blog search)', 'naja' ); ?></label></th>
			<td><input type="text" id="naja_keywords" name="naja_keywords" class="large-text" value="<?php echo esc_attr( $get( '_naja_keywords' ) ); ?>"></td></tr>
		<tr><th><label for="naja_image_alt"><?php esc_html_e( 'Image alt text', 'naja' ); ?></label></th>
			<td><input type="text" id="naja_image_alt" name="naja_image_alt" class="large-text" value="<?php echo esc_attr( $get( '_naja_image_alt' ) ); ?>">
			<p class="description"><?php esc_html_e( 'Set a Featured image on the right to replace the placeholder photo. Describe what the photo really shows.', 'naja' ); ?></p></td></tr>
		<tr><th><label for="naja_faq"><?php esc_html_e( 'FAQ', 'naja' ); ?></label></th>
			<td><textarea id="naja_faq" name="naja_faq" class="large-text" rows="5" placeholder="Question? | Answer"><?php echo esc_textarea( $faq_lines ); ?></textarea>
			<p class="description"><?php esc_html_e( 'One question per line: Question | Answer. Shown at the end of the article and added to its structured data.', 'naja' ); ?></p></td></tr>
		<tr><th><label for="naja_related"><?php esc_html_e( 'Related article slugs', 'naja' ); ?></label></th>
			<td><input type="text" id="naja_related" name="naja_related" class="large-text" value="<?php echo esc_attr( is_array( $related ) ? implode( ', ', $related ) : '' ); ?>">
			<p class="description"><?php esc_html_e( 'Comma separated. Empty = other articles from the same category.', 'naja' ); ?></p></td></tr>
	</table>
	<?php
}

function naja_article_save( $post_id ) {
	if ( ! isset( $_POST['naja_article_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['naja_article_nonce'] ) ), 'naja_article_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$text = function ( $key ) {
		return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
	};
	update_post_meta( $post_id, '_naja_seo_title', $text( 'naja_seo_title' ) );
	update_post_meta( $post_id, '_naja_description', $text( 'naja_description' ) );
	update_post_meta( $post_id, '_naja_keywords', $text( 'naja_keywords' ) );
	update_post_meta( $post_id, '_naja_image_alt', $text( 'naja_image_alt' ) );

	$faq = array();
	$raw = isset( $_POST['naja_faq'] ) ? sanitize_textarea_field( wp_unslash( $_POST['naja_faq'] ) ) : '';
	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$faq[] = array( 'q' => $parts[0], 'a' => $parts[1] );
		}
	}
	update_post_meta( $post_id, '_naja_faq', $faq );

	$related = array_filter( array_map( 'sanitize_title', explode( ',', $text( 'naja_related' ) ) ) );
	update_post_meta( $post_id, '_naja_related', array_values( $related ) );
}
add_action( 'save_post_naja_article', 'naja_article_save' );

/* ---------------------------------------------------------------------------
 * First-run content: copy inc/articles/*.php into the database, once
 * ------------------------------------------------------------------------ */

function naja_blog_seed() {
	$files = glob( get_template_directory() . '/inc/articles/*.php' );
	if ( ! $files ) {
		return;
	}
	sort( $files );

	$categories = naja_blog_categories();
	foreach ( $categories as $slug => $name ) {
		if ( ! term_exists( $slug, 'naja_topic' ) ) {
			wp_insert_term( $name, 'naja_topic', array( 'slug' => $slug ) );
		}
	}

	// Article text contains plain HTML written by the theme; keep it exactly as written.
	kses_remove_filters();
	foreach ( $files as $file ) {
		$a = include $file;
		if ( ! is_array( $a ) || empty( $a['slug'] ) || get_page_by_path( $a['slug'], OBJECT, 'naja_article' ) ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'naja_article',
				'post_status'  => 'publish',
				'post_name'    => $a['slug'],
				'post_title'   => $a['title'],
				'post_excerpt' => $a['excerpt'],
				'post_content' => $a['content'],
				'menu_order'   => (int) $a['order'],
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_naja_seo_title', $a['seo_title'] );
		update_post_meta( $id, '_naja_description', $a['description'] );
		update_post_meta( $id, '_naja_keywords', $a['keywords'] );
		update_post_meta( $id, '_naja_image_id', $a['image']['id'] );
		update_post_meta( $id, '_naja_image_alt', $a['image']['alt'] );
		update_post_meta( $id, '_naja_faq', $a['faq'] );
		update_post_meta( $id, '_naja_related', $a['related'] );
		wp_set_object_terms( $id, $a['category'], 'naja_topic' );
	}
	kses_init_filters();
}

function naja_blog_maybe_seed() {
	if ( NAJA_BLOG_VERSION !== get_option( 'naja_blog_seeded' ) ) {
		naja_blog_seed();
		update_option( 'naja_blog_seeded', NAJA_BLOG_VERSION );
		delete_option( 'naja_blog_rewrite' ); // new URLs: refresh the rewrite rules below
	}
}
add_action( 'init', 'naja_blog_maybe_seed', 30 );

/** Make /blog/ and /blog/{slug}/ work as soon as the theme is updated. */
function naja_blog_maybe_flush() {
	if ( NAJA_BLOG_VERSION !== get_option( 'naja_blog_rewrite' ) ) {
		flush_rewrite_rules( false );
		update_option( 'naja_blog_rewrite', NAJA_BLOG_VERSION );
	}
}
add_action( 'init', 'naja_blog_maybe_flush', 99 );

/* ---------------------------------------------------------------------------
 * SEO: structured data, canonical, sitemap
 * ------------------------------------------------------------------------ */

function naja_blog_logo_url() {
	return get_template_directory_uri() . '/assets/images/naja-sign.webp';
}

function naja_blog_jsonld_print( $data ) {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . "</script>\n";
}

function naja_blog_head() {
	if ( naja_seo_plugin_active() ) {
		return; // the SEO plugin writes the schema and canonical itself
	}

	if ( naja_is_blog_archive() ) {
		$url = naja_blog_url();
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

		$items = array();
		$pos   = 1;
		foreach ( $GLOBALS['wp_query']->posts as $post ) {
			$items[] = array( '@type' => 'ListItem', 'position' => $pos++, 'url' => get_permalink( $post ) );
		}
		naja_blog_jsonld_print(
			array(
				'@context'    => 'https://schema.org',
				'@type'       => 'CollectionPage',
				'name'        => 'Naja Restaurant Blog',
				'url'         => $url,
				'description' => 'Guides and tips on restaurants and food in Dammam.',
				'mainEntity'  => array( '@type' => 'ItemList', 'itemListElement' => $items ),
			)
		);
		naja_blog_jsonld_print(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => array(
					array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ),
					array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $url ),
				),
			)
		);
		return;
	}

	if ( ! naja_is_article() ) {
		return;
	}

	$id    = get_queried_object_id();
	$title = html_entity_decode( wp_strip_all_tags( get_the_title( $id ) ), ENT_QUOTES, 'UTF-8' ); // plain text, not the texturized HTML entities
	$url   = get_permalink( $id );
	$image = naja_article_image( $id, 1200 );
	$cat   = naja_article_category( $id );
	$keys  = (string) get_post_meta( $id, '_naja_keywords', true );

	$article = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => $title,
		'description'      => naja_article_description( $id ),
		'image'            => array( $image['src'] ),
		'datePublished'    => get_the_date( 'c', $id ),
		'dateModified'     => get_the_modified_date( 'c', $id ),
		'author'           => array( '@type' => 'Organization', 'name' => 'Naja Restaurant', 'url' => home_url( '/' ) ),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => 'Naja Restaurant',
			'logo'  => array( '@type' => 'ImageObject', 'url' => naja_blog_logo_url() ),
		),
		'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => $url ),
		'inLanguage'       => 'en',
	);
	if ( $cat['name'] ) {
		$article['articleSection'] = $cat['name'];
	}
	if ( '' !== $keys ) {
		$article['keywords'] = $keys;
	}
	naja_blog_jsonld_print( $article );

	naja_blog_jsonld_print(
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => naja_blog_url() ),
				array( '@type' => 'ListItem', 'position' => 3, 'name' => $title, 'item' => $url ),
			),
		)
	);

	$faq = naja_article_faq( $id );
	if ( $faq ) {
		$entities = array();
		foreach ( $faq as $row ) {
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $row['q'],
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $row['a'] ),
			);
		}
		naja_blog_jsonld_print( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $entities ) );
	}
}
add_action( 'wp_head', 'naja_blog_head', 3 );

/**
 * Core WordPress sitemap (/wp-sitemap.xml): list the blog page first, then every
 * published article with its last-modified date. Only published, public pages appear.
 */
function naja_blog_sitemap_urls( $url_list, $post_type, $page_number ) {
	if ( 'naja_article' !== $post_type ) {
		return $url_list;
	}
	$max   = wp_sitemaps_get_max_urls( 'post' );
	$posts = get_posts(
		array(
			'post_type'      => 'naja_article',
			'post_status'    => 'publish',
			'posts_per_page' => $max,
			'paged'          => $page_number,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
	$list = array();
	if ( 1 === (int) $page_number ) {
		$list[] = array( 'loc' => naja_blog_url() );
	}
	foreach ( $posts as $post ) {
		$list[] = array(
			'loc'     => get_permalink( $post ),
			'lastmod' => get_post_modified_time( 'c', true, $post ),
		);
	}
	return $list;
}
add_filter( 'wp_sitemaps_posts_pre_url_list', 'naja_blog_sitemap_urls', 10, 3 );

/* ---------------------------------------------------------------------------
 * Templates: card used by the listing and the "related articles" block
 * ------------------------------------------------------------------------ */

/**
 * Print one article card.
 *
 * @param int  $id       Article ID.
 * @param bool $filters  Add the data attributes the listing's search/filter use.
 */
function naja_blog_card( $id, $filters = false ) {
	$image   = naja_article_image( $id, 800 );
	$cat     = naja_article_category( $id );
	$title   = get_the_title( $id );
	$excerpt = get_the_excerpt( $id );
	$data    = '';
	if ( $filters ) {
		$haystack = function_exists( 'mb_strtolower' )
			? mb_strtolower( $title . ' ' . get_post_meta( $id, '_naja_keywords', true ) . ' ' . $excerpt )
			: strtolower( $title . ' ' . get_post_meta( $id, '_naja_keywords', true ) . ' ' . $excerpt );
		$data = ' data-cat="' . esc_attr( $cat['slug'] ) . '" data-search="' . esc_attr( $haystack ) . '"';
	}
	?>
	<li class="bl-card"<?php echo $data; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<a class="bl-card__media img-frame" href="<?php echo esc_url( get_permalink( $id ) ); ?>" tabindex="-1" aria-hidden="true">
			<?php if ( $image['src'] ) : ?>
				<img src="<?php echo esc_url( $image['src'] ); ?>"<?php echo $image['srcset'] ? ' srcset="' . esc_attr( $image['srcset'] ) . '" sizes="(min-width: 1100px) 31vw, (min-width: 640px) 46vw, 92vw"' : ''; ?> alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo (int) $image['width']; ?>" height="<?php echo (int) $image['height']; ?>" loading="lazy" decoding="async">
			<?php endif; ?>
		</a>
		<div class="bl-card__body">
			<?php if ( $cat['slug'] ) : ?>
				<p class="bl-card__cat" data-i18n="bl.cat.<?php echo esc_attr( $cat['slug'] ); ?>"><?php echo esc_html( $cat['name'] ); ?></p>
			<?php endif; ?>
			<h3 class="bl-card__title" lang="en" dir="ltr"><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( $title ); ?></a></h3>
			<p class="bl-card__excerpt" lang="en" dir="ltr"><?php echo esc_html( $excerpt ); ?></p>
			<div class="bl-card__meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c', $id ) ); ?>" lang="en" dir="ltr"><?php echo esc_html( get_the_date( 'j M Y', $id ) ); ?></time>
				<span class="bl-card__more"><span data-i18n="bl.readMore">اقرأ المقال</span><svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg></span>
			</div>
		</div>
	</li>
	<?php
}
