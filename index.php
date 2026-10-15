<?php
/**
 * Fallback template: blog, archives, search and 404, styled to match the site.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
  <main id="main" class="wp-page">
    <div class="container wp-page__inner">
      <?php if ( have_posts() ) : ?>
        <?php if ( ! is_singular() ) : ?>
          <h1 class="section-title wp-page__title"><?php echo is_home() ? esc_html( get_bloginfo( 'name' ) ) : wp_kses_post( get_the_archive_title() ); ?></h1>
        <?php endif; ?>
        <?php
        while ( have_posts() ) :
          the_post();
          ?>
          <article id="post-<?php the_ID(); ?>" <?php post_class( 'wp-entry' ); ?>>
            <?php if ( is_singular() ) : ?>
              <h1 class="section-title wp-page__title"><?php the_title(); ?></h1>
              <div class="wp-content"><?php the_content(); ?></div>
            <?php else : ?>
              <h2 class="wp-entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <div class="wp-content"><?php the_excerpt(); ?></div>
            <?php endif; ?>
          </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
      <?php else : ?>
        <h1 class="section-title wp-page__title">404</h1>
        <p class="section-copy"><?php esc_html_e( 'This page could not be found.', 'naja' ); ?></p>
        <p><a class="btn btn--brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the homepage', 'naja' ); ?></a></p>
      <?php endif; ?>
    </div>
  </main>
<?php
get_footer();
