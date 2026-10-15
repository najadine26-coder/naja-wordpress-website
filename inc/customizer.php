<?php
/**
 * Customizer: Appearance → Customize → Naja Restaurant.
 *
 * Every value here is passed to the front end by overriding window.restaurantConfig
 * and window.najaImages (see naja_config_overrides_js). Empty values hide or
 * disable the related buttons, exactly as on the static site.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Defaults: the details already confirmed by the restaurant. */
function naja_mod_defaults() {
	return array(
		'whatsapp'           => '+966583815633',
		'phone'              => '+966583815633',
		'name_ar'            => 'مطعم نجا',
		'address_ar'         => '',
		'address_en'         => '',
		'maps_url'           => 'https://www.google.com/maps/search/?api=1&query=Dammam%2C%20Saudi%20Arabia',
		'hours_ar'           => '',
		'hours_en'           => '',
		'social_instagram'   => '',
		'social_tiktok'      => '',
		'social_snapchat'    => '',
		'social_x'           => '',
		'show_sample_prices' => true,
	);
}

/** Read a theme setting with its default. */
function naja_mod( $key ) {
	$defaults = naja_mod_defaults();
	return get_theme_mod( 'naja_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/** Replaceable photos: key in window.najaImages => label. */
function naja_image_slots() {
	return array(
		'logoSign'     => __( 'Logo sign (hero frame + order section)', 'naja' ),
		'hero'         => __( 'Homepage hero', 'naja' ),
		'presentation' => __( 'Menu page hero / sharing platter', 'naja' ),
		'grill'        => __( 'Grill', 'naja' ),
		'burger'       => __( 'Burger', 'naja' ),
		'pizza'        => __( 'Pizza / oven', 'naja' ),
		'plated'       => __( 'Plated dish', 'naja' ),
		'interior'     => __( 'Dining room', 'naja' ),
	);
}

/** URL of a photo chosen in the Customizer for a slot, or "". */
function naja_custom_image( $key ) {
	$id = absint( get_theme_mod( 'naja_img_' . $key ) );
	if ( ! $id ) {
		return '';
	}
	$src = wp_get_attachment_image_url( $id, 'full' );
	return $src ? $src : '';
}

/**
 * Register the panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function naja_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'naja',
		array(
			'title'    => __( 'Naja Restaurant', 'naja' ),
			'priority' => 20,
		)
	);

	// Contact & location.
	$wp_customize->add_section( 'naja_contact', array( 'title' => __( 'Contact & location', 'naja' ), 'panel' => 'naja' ) );
	$text = array(
		'whatsapp'   => array( __( 'WhatsApp number', 'naja' ), __( 'International format, e.g. +966583815633. Empty = WhatsApp buttons disabled.', 'naja' ) ),
		'phone'      => array( __( 'Phone number (calls)', 'naja' ), __( 'Empty = Call buttons disabled.', 'naja' ) ),
		'name_ar'    => array( __( 'Restaurant name (Arabic)', 'naja' ), '' ),
		'address_ar' => array( __( 'Street address (Arabic)', 'naja' ), __( 'City (Dammam) is added automatically.', 'naja' ) ),
		'address_en' => array( __( 'Street address (English)', 'naja' ), '' ),
	);
	foreach ( $text as $key => $labels ) {
		$wp_customize->add_setting( 'naja_' . $key, array( 'default' => naja_mod_defaults()[ $key ], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( 'naja_' . $key, array( 'label' => $labels[0], 'description' => $labels[1], 'section' => 'naja_contact', 'type' => 'text' ) );
	}
	$wp_customize->add_setting( 'naja_maps_url', array( 'default' => naja_mod_defaults()['maps_url'], 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control(
		'naja_maps_url',
		array(
			'label'       => __( 'Google Maps link', 'naja' ),
			'description' => __( 'Paste the restaurant pin\'s share link from Google Maps.', 'naja' ),
			'section'     => 'naja_contact',
			'type'        => 'url',
		)
	);

	// Opening hours.
	$wp_customize->add_section(
		'naja_hours',
		array(
			'title'       => __( 'Opening hours', 'naja' ),
			'panel'       => 'naja',
			'description' => __( 'One line per row: Days | Time. Write the same number of lines in both languages. Empty = "To be announced".', 'naja' ),
		)
	);
	foreach ( array( 'hours_ar' => __( 'Hours (Arabic)', 'naja' ), 'hours_en' => __( 'Hours (English)', 'naja' ) ) as $key => $label ) {
		$wp_customize->add_setting( 'naja_' . $key, array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
		$wp_customize->add_control(
			'naja_' . $key,
			array(
				'label'       => $label,
				'section'     => 'naja_hours',
				'type'        => 'textarea',
				'input_attrs' => array( 'placeholder' => 'hours_ar' === $key ? 'السبت – الخميس | ١٢:٠٠ م – ١٢:٠٠ ص' : 'Saturday – Thursday | 12:00 PM – 12:00 AM' ),
			)
		);
	}

	// Social.
	$wp_customize->add_section( 'naja_social', array( 'title' => __( 'Social profiles', 'naja' ), 'panel' => 'naja', 'description' => __( 'Full URLs only. Empty profiles are hidden.', 'naja' ) ) );
	foreach ( array( 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'snapchat' => 'Snapchat', 'x' => 'X' ) as $key => $label ) {
		$wp_customize->add_setting( 'naja_social_' . $key, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( 'naja_social_' . $key, array( 'label' => $label, 'section' => 'naja_social', 'type' => 'url' ) );
	}

	// Menu options.
	$wp_customize->add_section( 'naja_menu_opts', array( 'title' => __( 'Menu & prices', 'naja' ), 'panel' => 'naja' ) );
	$wp_customize->add_setting( 'naja_show_sample_prices', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp_customize->add_control(
		'naja_show_sample_prices',
		array(
			'label'       => __( 'Show prices of sample (unconfirmed) dishes', 'naja' ),
			'description' => __( 'Confirmed dishes always show their price. Manage dishes under Dishes in the admin menu.', 'naja' ),
			'section'     => 'naja_menu_opts',
			'type'        => 'checkbox',
		)
	);

	// Photos.
	$wp_customize->add_section( 'naja_images', array( 'title' => __( 'Photos', 'naja' ), 'panel' => 'naja', 'description' => __( 'Replace the placeholder photos with the restaurant\'s own. Set the image "Alt text" in the Media Library to describe it.', 'naja' ) ) );
	foreach ( naja_image_slots() as $key => $label ) {
		$wp_customize->add_setting( 'naja_img_' . $key, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'naja_img_' . $key,
				array(
					'label'     => $label,
					'section'   => 'naja_images',
					'mime_type' => 'image',
				)
			)
		);
	}
}
add_action( 'customize_register', 'naja_customize_register' );

/** Opening hours rows in the shape config.js expects. */
function naja_opening_hours() {
	$split = function ( $text ) {
		$rows = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$parts  = array_map( 'trim', explode( '|', $line, 2 ) );
			$rows[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
		}
		return $rows;
	};
	$ar   = $split( naja_mod( 'hours_ar' ) );
	$en   = $split( naja_mod( 'hours_en' ) );
	$out  = array();
	$rows = max( count( $ar ), count( $en ) );
	for ( $i = 0; $i < $rows; $i++ ) {
		$a     = isset( $ar[ $i ] ) ? $ar[ $i ] : ( isset( $en[ $i ] ) ? $en[ $i ] : array( '', '' ) );
		$e     = isset( $en[ $i ] ) ? $en[ $i ] : $a;
		$out[] = array(
			'days' => array( 'ar' => $a[0], 'en' => $e[0] ),
			'time' => array( 'ar' => $a[1], 'en' => $e[1] ),
		);
	}
	return $out;
}

/** JS that applies the Customizer values on top of js/config.js. */
function naja_config_overrides_js() {
	$config = array(
		'nameAr'           => naja_mod( 'name_ar' ),
		'siteUrl'          => home_url( '/' ),
		'whatsapp'         => naja_digits( naja_mod( 'whatsapp' ) ),
		'phone'            => naja_digits( naja_mod( 'phone' ) ) ? '+' . naja_digits( naja_mod( 'phone' ) ) : '',
		'address'          => array( 'ar' => naja_mod( 'address_ar' ), 'en' => naja_mod( 'address_en' ) ),
		'googleMapsUrl'    => naja_mod( 'maps_url' ),
		'openingHours'     => naja_opening_hours(),
		'social'           => array(
			'instagram' => naja_mod( 'social_instagram' ),
			'tiktok'    => naja_mod( 'social_tiktok' ),
			'snapchat'  => naja_mod( 'social_snapchat' ),
			'x'         => naja_mod( 'social_x' ),
		),
		'showSamplePrices' => (bool) naja_mod( 'show_sample_prices' ),
	);

	// Photos: the logo always resolves to the theme folder; custom uploads replace placeholders.
	$images = array(
		'logoSign' => array( 'src' => get_template_directory_uri() . '/assets/images/naja-sign.webp' ),
	);
	foreach ( array_keys( naja_image_slots() ) as $key ) {
		$id = absint( get_theme_mod( 'naja_img_' . $key ) );
		if ( ! $id ) {
			continue;
		}
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( ! $src ) {
			continue;
		}
		$entry = array( 'src' => $src, 'position' => 'center' );
		$alt   = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		if ( '' !== $alt ) {
			$entry['alt'] = array( 'ar' => $alt, 'en' => $alt );
		}
		$images[ $key ] = $entry;
	}

	return 'window.najaApplyOverrides=function(c,i,o){var k;for(k in o.config)c[k]=o.config[k];for(k in o.images){i[k]=i[k]||{alt:{ar:"",en:""}};for(var p in o.images[k])i[k][p]=o.images[k][p];}};'
		. 'najaApplyOverrides(window.restaurantConfig,window.najaImages,' . wp_json_encode( array( 'config' => $config, 'images' => $images ) ) . ');';
}
