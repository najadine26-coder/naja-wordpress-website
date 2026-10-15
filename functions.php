<?php
/**
 * Naja Theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function naja_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'naja-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'naja_theme_setup' );

function naja_theme_assets() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'naja-theme-style', get_stylesheet_uri(), array(), $version );
	wp_enqueue_style( 'naja-theme-main', get_template_directory_uri() . '/assets/css/main.css', array(), $version );
}
add_action( 'wp_enqueue_scripts', 'naja_theme_assets' );
