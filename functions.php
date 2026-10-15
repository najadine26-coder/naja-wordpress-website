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
	$ver = NAJA_VERSION;

	wp_enqueue_style( 'naja-fonts', $uri . '/css/fonts.css', array(), $ver );
	wp_enqueue_style( 'naja-style', $uri . '/css/style.css', array( 'naja-fonts' ), $ver );
	if ( naja_is_menu_page() ) {
		wp_enqueue_style( 'naja-menu', $uri . '/css/menu.css', array( 'naja-style' ), $ver );
	}
	wp_enqueue_style( 'naja-wp', $uri . '/css/wp.css', array( 'naja-style' ), $ver );

	wp_enqueue_script( 'naja-config', $uri . '/js/config.js', array(), $ver, true );
	wp_add_inline_script( 'naja-config', naja_config_overrides_js(), 'after' );

	wp_enqueue_script( 'naja-menu-data', $uri . '/js/menu-data.js', array( 'naja-config' ), $ver, true );
	$dishes_js = naja_dishes_js();
	if ( $dishes_js ) {
		wp_add_inline_script( 'naja-menu-data', $dishes_js, 'after' );
	}

	wp_enqueue_script( 'naja-translations', $uri . '/js/translations.js', array(), $ver, true );
	wp_enqueue_script( 'gsap', $uri . '/js/vendor/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-scrolltrigger', $uri . '/js/vendor/ScrollTrigger.min.js', array( 'gsap' ), '3.12.5', true );
	wp_enqueue_script( 'naja-main', $uri . '/js/main.js', array( 'naja-config', 'naja-menu-data', 'naja-translations' ), $ver, true );
	wp_enqueue_script( 'naja-animations', $uri . '/js/animations.js', array( 'gsap', 'gsap-scrolltrigger', 'naja-main' ), $ver, true );
}
add_action( 'wp_enqueue_scripts', 'naja_enqueue_assets' );

/**
 * On activation, create the Menu page (once) so the "Menu" link works straight away.
 */
function naja_create_menu_page() {
	if ( naja_menu_page_id() ) {
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
	}
}
add_action( 'after_switch_theme', 'naja_create_menu_page' );
