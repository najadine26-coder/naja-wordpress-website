<?php get_header(); ?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( is_singular() ) : ?>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			<?php else : ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
	<?php the_posts_pagination(); ?>
<?php else : ?>
	<p><?php esc_html_e( 'কোনো কনটেন্ট পাওয়া যায়নি।', 'naja-theme' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
