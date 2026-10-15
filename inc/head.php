<?php
/**
 * <head> output: titles, meta description, Open Graph, preloads and JSON-LD.
 *
 * Titles/descriptions are skipped when an SEO plugin (Rank Math, Yoast, AIOSEO,
 * SEOPress) is active, so the two never duplicate. main.js updates them when
 * the visitor switches to English.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function naja_seo_plugin_active() {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/** Arabic SEO copy (same text as js/translations.js). */
function naja_seo_copy() {
	if ( is_front_page() ) {
		return array(
			'title' => 'مطعم نجا في الدمام | مشويات وبرجر وأرز – اطلب عبر واتساب',
			'desc'  => 'مطعم نجا في الدمام: قائمة متنوعة من المشويات والأرز والبرجر والوجبات السريعة والباستا والحلويات. تصفّح القائمة واطلب بسهولة عبر واتساب أو اتصل بنا.',
			'image' => naja_custom_image( 'hero' ) ? naja_custom_image( 'hero' ) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1200&h=630&q=80',
			'meta'  => array( 'title' => 'meta.title', 'desc' => 'meta.description' ),
		);
	}
	if ( naja_is_menu_page() ) {
		return array(
			'title' => 'منيو مطعم نجا في الدمام | مشويات وبرجر وأرز وحلويات',
			'desc'  => 'اكتشف منيو مطعم نجا في الدمام: مشويات، أرز، برجر، وجبات سريعة، باستا، مشروبات وحلويات. ابحث عن طبقك بالعربية أو الإنجليزية واطلبه بسهولة عبر واتساب.',
			'image' => naja_custom_image( 'presentation' ) ? naja_custom_image( 'presentation' ) : 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&h=630&q=80',
			'meta'  => array( 'title' => 'mp.metaTitle', 'desc' => 'mp.metaDesc' ),
		);
	}
	return null;
}

/** Use the designed titles on the homepage and menu page. */
function naja_document_title( $title ) {
	$copy = naja_seo_copy();
	return ( $copy && ! naja_seo_plugin_active() ) ? $copy['title'] : $title;
}
add_filter( 'pre_get_document_title', 'naja_document_title', 20 );

function naja_head() {
	$copy = naja_seo_copy();
	echo '<meta name="theme-color" content="#121212">' . "\n";
	echo '<meta name="color-scheme" content="dark">' . "\n";

	if ( $copy && ! naja_seo_plugin_active() ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $copy['desc'] ) );
		printf( '<meta property="og:type" content="website">' . "\n" );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( 'Naja Restaurant | مطعم نجا' ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $copy['title'] ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $copy['desc'] ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( is_front_page() ? home_url( '/' ) : get_permalink() ) );
		echo '<meta property="og:locale" content="ar_SA">' . "\n" . '<meta property="og:locale:alternate" content="en_US">' . "\n";
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $copy['image'] ) );
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $copy['title'] ) );
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $copy['desc'] ) );
	}

	// Load the Arabic font and the hero photo first.
	printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( get_template_directory_uri() . '/assets/fonts/fustat-arabic-wght-normal.woff2' ) );
	$hero_key = is_front_page() ? 'hero' : ( naja_is_menu_page() ? 'presentation' : '' );
	if ( $hero_key ) {
		$custom = naja_custom_image( $hero_key );
		if ( $custom ) {
			printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $custom ) );
		} else {
			echo '<link rel="preconnect" href="https://images.unsplash.com" crossorigin>' . "\n";
			printf( '<link rel="preload" as="image" fetchpriority="high" imagesrcset="%s" imagesizes="100vw">' . "\n", esc_attr( naja_default_hero_srcset( $hero_key ) ) );
		}
	}

	// Structured data: confirmed details only. main.js keeps it in sync with the language.
	$restaurant = array(
		'@context'           => 'https://schema.org',
		'@type'              => 'Restaurant',
		'name'               => 'Naja Restaurant',
		'alternateName'      => naja_mod( 'name_ar' ),
		'slogan'             => 'Feel Fresh',
		'servesCuisine'      => 'Mixed Menu',
		'currenciesAccepted' => 'SAR',
		'url'                => home_url( '/' ),
		'address'            => array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => naja_mod( 'address_en' ),
				'addressLocality' => 'Dammam',
				'addressCountry'  => 'SA',
			)
		),
	);
	$phone = naja_digits( naja_mod( 'phone' ) );
	if ( $phone ) {
		$restaurant['telephone'] = '+' . $phone;
	}
	if ( naja_mod( 'maps_url' ) ) {
		$restaurant['hasMap'] = naja_mod( 'maps_url' );
	}
	if ( naja_is_menu_page() || is_front_page() ) {
		$restaurant['hasMenu'] = naja_menu_url();
	}
	echo '<script type="application/ld+json" id="jsonld">' . wp_json_encode( $restaurant, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . "</script>\n";

	if ( is_front_page() ) {
		$faq = array();
		foreach ( naja_faq() as $row ) {
			$faq[] = array(
				'@type'          => 'Question',
				'name'           => $row['q'],
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $row['a'] ),
			);
		}
		echo '<script type="application/ld+json" id="faqJsonld">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . "</script>\n";
	}
}
add_action( 'wp_head', 'naja_head', 2 );

/** Responsive srcset of the default (placeholder) hero photos. */
function naja_default_hero_srcset( $key ) {
	$base = 'hero' === $key
		? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=%d&q=85'
		: 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=%d&q=85';
	$set  = array();
	foreach ( array( 800, 1200, 1600, 2200 ) as $w ) {
		$set[] = sprintf( $base, $w ) . ' ' . $w . 'w';
	}
	return implode( ', ', $set );
}

/**
 * Hero <img> for a page: the Customizer photo if one is set, otherwise the
 * responsive placeholder. main.js fills in the alt text for either language.
 */
function naja_hero_img( $key ) {
	$custom = naja_custom_image( $key );
	if ( $custom ) {
		printf( '<img class="hero__img" data-img="%s" data-img-sizes="100vw" src="%s" alt="" fetchpriority="high" decoding="async">', esc_attr( $key ), esc_url( $custom ) );
		return;
	}
	$srcset = naja_default_hero_srcset( $key );
	$src    = 'hero' === $key
		? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=2200&q=85'
		: 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1100&q=85';
	$alt    = 'hero' === $key ? 'طبق طعام ملون وطازج مقدم بأناقة' : 'طبق لحم مقدم على لوح تقديم';
	printf(
		'<img class="hero__img" data-img="%1$s" data-img-sizes="100vw" src="%2$s" srcset="%3$s" sizes="100vw" alt="%4$s" fetchpriority="high" decoding="async" width="2200" height="1467">',
		esc_attr( $key ),
		esc_url( $src ),
		esc_attr( $srcset ),
		esc_attr( $alt )
	);
}

/** Homepage FAQ (Arabic; main.js renders the English version on switch). */
function naja_faq() {
	$number = naja_contact_number();
	return array(
		array( 'q' => 'أين يقع مطعم نجا؟', 'a' => 'يقع مطعم نجا في مدينة الدمام بالمملكة العربية السعودية. سنضيف العنوان التفصيلي وموقع المطعم على الخريطة قريباً.' ),
		array( 'q' => 'كيف أطلب من مطعم نجا؟', 'a' => 'اختر طبقك من القائمة واضغط «اطلب عبر واتساب»، أو تواصل معنا مباشرة على الرقم ' . $number . ' عبر واتساب أو اتصال هاتفي.' ),
		array( 'q' => 'ماذا تقدّم قائمة مطعم نجا؟', 'a' => 'قائمة متنوعة تشمل الأطباق الرئيسية والأرز والمشويات والبرجر والوجبات السريعة والباستا والمشروبات والحلويات.' ),
		array( 'q' => 'هل القائمة متوفرة باللغة الإنجليزية؟', 'a' => 'نعم، يمكنك التبديل بين العربية والإنجليزية في أي وقت من زر اللغة أعلى الصفحة، وتعمل القائمة والبحث والطلب باللغتين.' ),
	);
}

/** Attributes on <body> that main.js reads (page type and which meta keys to translate). */
function naja_body_attrs() {
	$copy = naja_seo_copy();
	$page = is_front_page() ? 'home' : ( naja_is_menu_page() ? 'menu' : 'page' );
	echo 'data-page="' . esc_attr( $page ) . '"';
	if ( $copy && ! naja_seo_plugin_active() ) {
		echo ' data-meta-title="' . esc_attr( $copy['meta']['title'] ) . '" data-meta-desc="' . esc_attr( $copy['meta']['desc'] ) . '"';
	}
	// Without data-meta-* keys, main.js leaves the WordPress / SEO-plugin title alone.
}
