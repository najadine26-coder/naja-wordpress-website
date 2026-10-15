<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
	<div class="container">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		<?php endif; ?>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => 'nav',
				'fallback_cb'    => false,
			)
		);
		?>
	</div>
</header>
<main class="site-main container">
