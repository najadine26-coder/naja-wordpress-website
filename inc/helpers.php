<?php
/**
 * Small helpers shared by the templates.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Is the current view the Menu page template? */
function naja_is_menu_page() {
	return is_page_template( 'page-templates/menu.php' );
}

/** ID of the first published page that uses the Menu template (0 if none). */
function naja_menu_page_id() {
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value' => 'page-templates/menu.php', // phpcs:ignore WordPress.DB.SlowDBQuery
			'number'     => 1,
		)
	);
	return $pages ? (int) $pages[0]->ID : 0;
}

/** URL of the Menu page. */
function naja_menu_url() {
	$id = naja_menu_page_id();
	return $id ? get_permalink( $id ) : home_url( '/menu/' );
}

/**
 * Link to a homepage section. On the homepage itself it stays an in-page anchor;
 * elsewhere it points at the homepage and carries data-page-link so the
 * language switch can add ?lang=en to it.
 *
 * @param string $hash Section anchor such as "#gallery", or "" for the top.
 * @return string Attributes ready to print inside an <a> tag.
 */
function naja_section_link( $hash = '' ) {
	if ( is_front_page() ) {
		return 'href="' . esc_attr( $hash ? $hash : '#home' ) . '"';
	}
	$url = home_url( '/' ) . $hash;
	return 'href="' . esc_url( $url ) . '" data-page-link="' . esc_url( $url ) . '"';
}

/** Attributes for a link to the Menu page. */
function naja_menu_link() {
	$url     = naja_menu_url();
	$current = naja_is_menu_page() ? ' aria-current="page"' : '';
	return 'href="' . esc_url( $url ) . '" data-page-link="' . esc_url( $url ) . '"' . $current;
}

/** Digits of a phone number ("+966 58…" → "96658…"). */
function naja_digits( $value ) {
	$d = preg_replace( '/\D+/', '', (string) $value );
	return 0 === strpos( $d, '00' ) ? substr( $d, 2 ) : $d;
}

/** "966583815633" → "+966 58 381 5633" (same format as main.js). */
function naja_display_phone( $digits ) {
	if ( preg_match( '/^9665\d{8}$/', $digits ) ) {
		return '+966 ' . substr( $digits, 3, 2 ) . ' ' . substr( $digits, 5, 3 ) . ' ' . substr( $digits, 8 );
	}
	return $digits ? '+' . $digits : '';
}

/** Contact number shown in copy: the phone, falling back to WhatsApp. */
function naja_contact_number() {
	$d = naja_digits( naja_mod( 'phone' ) );
	if ( ! $d ) {
		$d = naja_digits( naja_mod( 'whatsapp' ) );
	}
	return naja_display_phone( $d );
}
