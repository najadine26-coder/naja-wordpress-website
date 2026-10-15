<?php
/**
 * Naja Restaurant theme setup.
 *
 * The front end is the same HTML/CSS/JS as the static site (css/, js/, assets/).
 * WordPress adds: the Customizer (contact details, photos), a Dishes manager
 * that replaces the sample menu, and page templates for the homepage and menu.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NAJA_VERSION', '1.0.0' );

require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/dishes.php';
require get_template_directory() . '/inc/head.php';
require get_template_directory() . '/inc/github-sync.php';

/**
 * Theme supports and menus.
 */
function naja_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_image_size( 'naja-dish', 1100, 825, true );
}
add_action( 'after_setup_theme', 'naja_setup' );

/**
 * Styles and scripts. Order matters: config → menu data → translations → GSAP → main → animations.
 */
function naja_enqueue_assets() {
	$uri = get_template_directory_uri();
	$ver = naja_sync_asset_version();

	wp_enqueue_style( 'naja-fonts', $uri . '/css/fonts.css', array(), $ver );
	wp_enqueue_style( 'naja-style', $uri . '/css/style.css', array( 'naja-fonts' ), $ver );
	if ( naja_is_menu_page() ) {
		wp_enqueue_style( 'naja-menu', $uri . '/css/menu.css', array( 'naja-style' ), $ver );
	}
	if ( naja_is_gallery_page() ) {
		wp_enqueue_style( 'naja-gallery', $uri . '/css/gallery.css', array( 'naja-style' ), $ver );
	}
	wp_enqueue_style( 'naja-wp', $uri . '/css/wp.css', array( 'naja-style' ), $ver );

	wp_enqueue_script( 'naja-config', $uri . '/js/config.js', array(), $ver, true );
	wp_add_inline_script( 'naja-config', naja_config_overrides_js(), 'after' );

	wp_enqueue_script( 'naja-menu-data', $uri . '/js/menu-data.js', array( 'naja-config' ), $ver, true );
	$dishes_js = naja_dishes_js();
	if ( $dishes_js ) {
		wp_add_inline_script( 'naja-menu-data', $dishes_js, 'after' );
	}

	$main_deps = array( 'naja-config', 'naja-menu-data', 'naja-translations' );
	if ( naja_is_gallery_page() ) {
		wp_enqueue_script( 'naja-gallery-data', $uri . '/js/gallery-data.js', array( 'naja-config' ), $ver, true );
		$main_deps[] = 'naja-gallery-data';
	}

	wp_enqueue_script( 'naja-translations', $uri . '/js/translations.js', array(), $ver, true );
	wp_enqueue_script( 'gsap', $uri . '/js/vendor/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-scrolltrigger', $uri . '/js/vendor/ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );
	wp_enqueue_script( 'naja-main', $uri . '/js/main.js', $main_deps, $ver, true );
	wp_enqueue_script( 'naja-animations', $uri . '/js/animations.js', array( 'gsap', 'gsap-scrolltrigger', 'naja-main' ), $ver, true );
}
add_action( 'wp_enqueue_scripts', 'naja_enqueue_assets' );

/**
 * On activation, create the Menu page (once) so the "Menu" link works straight away.
 */
function naja_create_menu_page() {
	if ( naja_menu_page_id() ) {
		update_option( 'naja_menu_page_ready', '1' );
		return;
	}
	$id = wp_insert_post(
		array(
			'post_title'  => 'القائمة',
			'post_name'   => 'menu',
			'post_status' => 'publish',
			'post_type'   => 'page',
		)
	);
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_wp_page_template', 'page-templates/menu.php' );
		update_option( 'naja_menu_page_ready', '1' );
	}
}
add_action( 'after_switch_theme', 'naja_create_menu_page' );

/**
 * Sites that got the theme without a fresh activation (Git deploy, GitHub Sync)
 * never ran the hook above, so /menu/ would 404. Create the page once on the
 * first request instead; it is not recreated if someone deletes it later.
 */
function naja_maybe_create_menu_page() {
	if ( '1' !== get_option( 'naja_menu_page_ready' ) ) {
		naja_create_menu_page();
	}
}
add_action( 'init', 'naja_maybe_create_menu_page', 20 );

/**
 * Create the Gallery page (once) so the "Gallery" link works straight away.
 * Runs on activation and, for sites that already had the theme active, on the
 * first request after the update. A page that already uses the Gallery template
 * is reused; if another page owns /gallery/, WordPress gives the new one a free address.
 */
function naja_create_gallery_page() {
	if ( naja_gallery_page_id() ) {
		update_option( 'naja_gallery_page_ready', '1' );
		return;
	}
	$id = wp_insert_post(
		array(
			'post_title'  => 'المعرض',
			'post_name'   => 'gallery',
			'post_status' => 'publish',
			'post_type'   => 'page',
		)
	);
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_wp_page_template', 'page-templates/gallery.php' );
		update_option( 'naja_gallery_page_ready', '1' );
	}
}
add_action( 'after_switch_theme', 'naja_create_gallery_page' );

function naja_maybe_create_gallery_page() {
	if ( '1' !== get_option( 'naja_gallery_page_ready' ) ) {
		naja_create_gallery_page();
	}
}
add_action( 'init', 'naja_maybe_create_gallery_page', 20 );
