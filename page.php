<?php
/**
 * Regular pages (About, Privacy Policy, …).
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
      <?php
      while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'wp-entry' ); ?>>
          <h1 class="section-title wp-page__title"><?php the_title(); ?></h1>
          <div class="wp-content"><?php the_content(); ?></div>
        </article>
      <?php endwhile; ?>
    </div>
  </main>
<?php
get_footer();
