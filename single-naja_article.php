<?php
/**
 * A single blog article: /blog/{slug}/
 *
 * Breadcrumb, H1, featured image, article text, FAQ, call to action and
 * related articles. The article text is English (lang="en"); the surrounding
 * labels follow the site language.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
  <main id="main" class="bl-page">
    <?php
    while ( have_posts() ) :
      the_post();
      $id       = get_the_ID();
      $image    = naja_article_image( $id, 1200 );
      $cat      = naja_article_category( $id );
      $faq      = naja_article_faq( $id );
      $related  = naja_article_related( $id, 3 );
      $minutes  = naja_article_reading_minutes( $id );
      $updated  = get_the_modified_date( 'Y-m-d', $id ) !== get_the_date( 'Y-m-d', $id );
      ?>
      <div class="container bl-article">
        <nav class="bl-crumbs" aria-label="Breadcrumb" data-i18n-attr="aria-label:bl.breadcrumb">
          <ol>
            <li><a <?php echo naja_section_link( '' ); ?> data-i18n="nav.home">الرئيسية</a></li>
            <li><a <?php echo naja_blog_link(); ?> data-i18n="nav.blog">المدونة</a></li>
            <li aria-current="page"><span lang="en" dir="ltr"><?php the_title(); ?></span></li>
          </ol>
        </nav>

        <article id="post-<?php echo (int) $id; ?>" <?php post_class( 'bl-entry' ); ?>>
          <header class="bl-entry__head">
            <?php if ( $cat['slug'] ) : ?>
              <p class="bl-card__cat" data-i18n="bl.cat.<?php echo esc_attr( $cat['slug'] ); ?>"><?php echo esc_html( $cat['name'] ); ?></p>
            <?php endif; ?>
            <h1 class="bl-entry__title" lang="en" dir="ltr"><?php the_title(); ?></h1>
            <p class="bl-entry__lead" lang="en" dir="ltr"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <p class="bl-entry__meta">
              <span><span data-i18n="bl.by">بقلم</span> <span lang="en" dir="ltr">Naja Restaurant</span></span>
              <span class="bl-card__dot" aria-hidden="true">·</span>
              <span><span data-i18n="bl.published">نُشر</span> <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" lang="en" dir="ltr"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></time></span>
              <?php if ( $updated ) : ?>
                <span class="bl-card__dot" aria-hidden="true">·</span>
                <span><span data-i18n="bl.updated">آخر تحديث</span> <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>" lang="en" dir="ltr"><?php echo esc_html( get_the_modified_date( 'j M Y' ) ); ?></time></span>
              <?php endif; ?>
              <span class="bl-card__dot" aria-hidden="true">·</span>
              <span data-i18n="bl.minRead" data-n="<?php echo (int) $minutes; ?>"><?php echo (int) $minutes; ?> min read</span>
            </p>
            <p class="bl-langnote" data-i18n="bl.langNote">هذا المقال مكتوب بالإنجليزية.</p>
          </header>

          <?php if ( $image['src'] ) : ?>
            <figure class="bl-entry__figure">
              <span class="bl-entry__frame img-frame">
                <img src="<?php echo esc_url( $image['src'] ); ?>"<?php echo $image['srcset'] ? ' srcset="' . esc_attr( $image['srcset'] ) . '" sizes="(min-width: 1100px) 1000px, 94vw"' : ''; ?> alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo (int) $image['width']; ?>" height="<?php echo (int) $image['height']; ?>" fetchpriority="high" decoding="async">
              </span>
              <figcaption data-i18n="bl.imageNote">صورة توضيحية، وليست من مطعم نجا.</figcaption>
            </figure>
          <?php endif; ?>

          <div class="bl-prose" lang="en" dir="ltr">
            <?php the_content(); ?>
          </div>

          <?php if ( $faq ) : ?>
            <section class="bl-faq" aria-labelledby="blFaqTitle" lang="en" dir="ltr">
              <h2 id="blFaqTitle">Frequently asked questions</h2>
              <?php foreach ( $faq as $row ) : ?>
                <h3><?php echo esc_html( $row['q'] ); ?></h3>
                <p><?php echo esc_html( $row['a'] ); ?></p>
              <?php endforeach; ?>
            </section>
          <?php endif; ?>

          <aside class="bl-note" aria-labelledby="blNoteTitle">
            <h2 class="bl-note__title" id="blNoteTitle" data-i18n="bl.noteTitle">عن مطعم نجا</h2>
            <p data-i18n="bl.noteText">يقدّم مطعم نجا قائمة متنوعة من المشويات والأرز والبرجر والباستا والحلويات، ويمكنك الطلب عبر واتساب.</p>
            <div class="bl-note__ctas">
              <a class="btn btn--brand" <?php echo naja_menu_link(); ?>>
                <span data-i18n="xp.ctaTitle">اكتشف قائمة الطعام</span>
                <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
              </a>
              <a class="btn btn--ghost" <?php echo naja_contact_link(); ?>><span data-i18n="nav.contact">تواصل معنا</span></a>
            </div>
          </aside>
        </article>
      </div>

      <?php if ( $related ) : ?>
        <section class="bl-related section" aria-labelledby="blRelatedTitle" data-bg="#121212">
          <div class="container">
            <h2 class="section-title" id="blRelatedTitle" data-i18n="bl.related">مقالات ذات صلة</h2>
            <ul class="bl-grid bl-grid--related">
              <?php foreach ( $related as $rid ) : ?>
                <?php naja_blog_card( $rid ); ?>
              <?php endforeach; ?>
            </ul>
            <p class="bl-back"><a class="btn btn--ghost" <?php echo naja_blog_link(); ?>><span data-i18n="bl.back">العودة إلى المدونة</span></a></p>
          </div>
        </section>
      <?php endif; ?>
    <?php endwhile; ?>
  </main>
<?php
get_footer();
