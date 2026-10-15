<?php
/**
 * Blog listing: /blog/
 *
 * Hero, featured article, search + category filter, grid of every article.
 * The articles are crawlable links in the HTML; js/blog.js only adds the
 * search, filter and "show more" behaviour on top.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$articles = $GLOBALS['wp_query']->posts;
$counts   = array();
foreach ( $articles as $article ) {
	$cat = naja_article_category( $article->ID );
	if ( $cat['slug'] ) {
		$counts[ $cat['slug'] ] = isset( $counts[ $cat['slug'] ] ) ? $counts[ $cat['slug'] ] + 1 : 1;
	}
}
$labels   = naja_blog_categories();
$featured = $articles ? $articles[0] : null;
?>
  <main id="main">
    <!-- ======================= 01 — BLOG HERO ======================= -->
    <section class="hero hero--blog" id="blog-hero" aria-labelledby="blogHeroTitle" data-bg="#121212" data-anchor="blog-hero">
      <div class="hero__media img-frame">
        <?php naja_hero_img( 'hero' ); ?>
      </div>
      <div class="hero__shade" aria-hidden="true"></div>
      <div class="aurora aurora--hero" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="hero__spot" aria-hidden="true"></div>

      <div class="container hero__inner">
        <div class="hero__content">
          <p class="eyebrow hero__eyebrow" data-hero-item>
            <span class="eyebrow__line" aria-hidden="true"></span>
            <span data-i18n="bl.heroEyebrow">مطعم نجا</span>
          </p>
          <h1 class="hero__title text-gradient-hero" id="blogHeroTitle" data-hero-item data-i18n="bl.heroTitle">مدونتنا</h1>
          <p class="hero__alt" data-hero-item data-i18n-alt="bl.heroTitle" lang="en" dir="ltr">Our Blog</p>
          <p class="hero__copy" data-hero-item data-i18n="bl.heroCopy">أدلة ونصائح عن المطاعم والأكل في الدمام: الأكل السعودي والمشويات والعشاء العائلي وكيف تخطط لزيارتك. المقالات متوفرة حالياً بالإنجليزية.</p>
        </div>
      </div>
      <a class="hero__scroll" href="#blog-browse">
        <span data-i18n="hero.scroll">مرّر للأسفل</span>
        <span class="hero__scroll-line" aria-hidden="true"></span>
      </a>
    </section>

    <?php if ( $featured ) : ?>
    <!-- ======================= 02 — FEATURED ARTICLE ======================= -->
    <section class="bl-featured section" id="blog-featured" aria-labelledby="blogFeaturedTitle" data-bg="#1a1a1a" data-anchor="blog-featured">
      <div class="container">
        <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="bl.featured">مقال مميز</span></p>
        <?php
        $f_image = naja_article_image( $featured->ID, 1200 );
        $f_cat   = naja_article_category( $featured->ID );
        ?>
        <article class="bl-feature" data-reveal>
          <a class="bl-feature__media img-frame" href="<?php echo esc_url( get_permalink( $featured ) ); ?>" tabindex="-1" aria-hidden="true">
            <?php if ( $f_image['src'] ) : ?>
              <img src="<?php echo esc_url( $f_image['src'] ); ?>"<?php echo $f_image['srcset'] ? ' srcset="' . esc_attr( $f_image['srcset'] ) . '" sizes="(min-width: 1000px) 55vw, 92vw"' : ''; ?> alt="<?php echo esc_attr( $f_image['alt'] ); ?>" width="<?php echo (int) $f_image['width']; ?>" height="<?php echo (int) $f_image['height']; ?>" loading="lazy" decoding="async">
            <?php endif; ?>
          </a>
          <div class="bl-feature__body">
            <?php if ( $f_cat['slug'] ) : ?>
              <p class="bl-card__cat" data-i18n="bl.cat.<?php echo esc_attr( $f_cat['slug'] ); ?>"><?php echo esc_html( $f_cat['name'] ); ?></p>
            <?php endif; ?>
            <h2 class="bl-feature__title" id="blogFeaturedTitle" lang="en" dir="ltr"><a href="<?php echo esc_url( get_permalink( $featured ) ); ?>"><?php echo esc_html( get_the_title( $featured ) ); ?></a></h2>
            <p class="bl-feature__excerpt" lang="en" dir="ltr"><?php echo esc_html( get_the_excerpt( $featured ) ); ?></p>
            <div class="bl-card__meta">
              <time datetime="<?php echo esc_attr( get_the_date( 'c', $featured ) ); ?>" lang="en" dir="ltr"><?php echo esc_html( get_the_date( 'j M Y', $featured ) ); ?></time>
              <span class="bl-card__dot" aria-hidden="true">·</span>
              <span data-i18n="bl.minRead" data-n="<?php echo (int) naja_article_reading_minutes( $featured->ID ); ?>"><?php echo (int) naja_article_reading_minutes( $featured->ID ); ?> min read</span>
            </div>
            <a class="btn btn--brand btn--lg" href="<?php echo esc_url( get_permalink( $featured ) ); ?>">
              <span data-i18n="bl.readMore">اقرأ المقال</span>
              <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
          </div>
        </article>
      </div>
    </section>
    <?php endif; ?>

    <!-- ======================= 03 — SEARCH, FILTER + ALL ARTICLES ======================= -->
    <section class="bl-browse section" id="blog-browse" aria-labelledby="blogBrowseTitle" data-bg="#121212" data-anchor="blog-browse">
      <div class="container">
        <header class="section-head">
          <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="bl.allEyebrow">المقالات</span></p>
          <h2 class="section-title" id="blogBrowseTitle" data-reveal-heading data-i18n="bl.allTitle">كل المقالات</h2>
        </header>

        <div class="bl-tools">
          <div class="menu__search bl-search">
            <label class="visually-hidden" for="blogSearch" data-i18n="bl.searchLabel">ابحث في المقالات</label>
            <svg class="icon menu__search-icon" aria-hidden="true"><use href="#i-search"/></svg>
            <input type="search" id="blogSearch" autocomplete="off" enterkeyhint="search" data-i18n-attr="placeholder:bl.searchPh" placeholder="ابحث بالعنوان أو الكلمة المفتاحية…">
            <button type="button" class="menu__search-clear" id="blogSearchClear" hidden data-i18n-attr="aria-label:menu.clear" aria-label="مسح البحث">
              <svg class="icon" aria-hidden="true"><use href="#i-close"/></svg>
            </button>
          </div>
          <div class="bl-cats" id="blogCats" role="group" data-i18n-attr="aria-label:bl.filterLabel" aria-label="تصفية المقالات حسب القسم">
            <button type="button" class="cat-btn" data-cat="all" aria-pressed="true"><span data-i18n="bl.cat.all">الكل</span><span class="bl-cats__count"><?php echo (int) count( $articles ); ?></span></button>
            <?php foreach ( $labels as $slug => $name ) : ?>
              <?php if ( ! empty( $counts[ $slug ] ) ) : ?>
                <button type="button" class="cat-btn" data-cat="<?php echo esc_attr( $slug ); ?>" aria-pressed="false"><span data-i18n="bl.cat.<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></span><span class="bl-cats__count"><?php echo (int) $counts[ $slug ]; ?></span></button>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
        <p class="bl-status" id="blogCount" role="status" aria-live="polite" data-total="<?php echo (int) count( $articles ); ?>"></p>

        <ul class="bl-grid" id="blogGrid" data-page-size="9">
          <?php foreach ( $articles as $article ) : ?>
            <?php naja_blog_card( $article->ID, true ); ?>
          <?php endforeach; ?>
        </ul>

        <div class="menu__empty" id="blogEmpty" hidden>
          <span class="menu__empty-mark" aria-hidden="true"></span>
          <h3 data-i18n="bl.emptyTitle">لا توجد مقالات مطابقة</h3>
          <p data-i18n="bl.emptyText">جرّب كلمة أخرى أو قسماً مختلفاً.</p>
          <button type="button" class="btn btn--ghost" id="blogReset" data-i18n="bl.reset">عرض كل المقالات</button>
        </div>

        <div class="bl-more" id="blogMoreWrap" hidden>
          <button type="button" class="btn btn--ghost btn--lg" id="blogMore" data-i18n="bl.loadMore">عرض المزيد</button>
        </div>
      </div>
    </section>

    <!-- ======================= 04 — CTA ======================= -->
    <section class="visit order-cta section" id="blog-cta" aria-labelledby="blogCtaTitle" data-bg="#0d0d0d" data-anchor="blog-cta">
      <div class="visit__glow" aria-hidden="true"></div>
      <div class="container">
        <div class="visit__head">
          <h2 class="section-title section-title--center text-gradient" id="blogCtaTitle" data-reveal-heading data-i18n="bl.ctaTitle">جاهز لوجبتك القادمة؟</h2>
          <p class="section-copy section-copy--center" data-i18n="bl.ctaText">تصفّح قائمة مطعم نجا، أو تواصل معنا.</p>
          <div class="visit__ctas" data-reveal>
            <a class="btn btn--brand btn--lg" <?php echo naja_menu_link(); ?>>
              <span data-i18n="xp.ctaTitle">اكتشف قائمة الطعام</span>
              <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
            <a class="btn btn--ghost btn--lg" <?php echo naja_contact_link(); ?>>
              <span data-i18n="nav.contact">تواصل معنا</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php
get_footer();
