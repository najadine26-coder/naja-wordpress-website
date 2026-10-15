<?php
/**
 * Template Name: Naja Gallery
 *
 * The photo gallery: hero, category filters, responsive masonry grid with a
 * lightbox. Photos and categories live in js/gallery-data.js.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
  <main id="main">
    <!-- ======================= 01 — GALLERY HERO ======================= -->
    <section class="hero hero--gallery" id="gallery-hero" aria-labelledby="galleryHeroTitle" data-bg="#121212" data-anchor="gallery-hero">
      <div class="hero__media img-frame">
        <?php naja_hero_img( 'interior' ); ?>
      </div>
      <div class="hero__shade" aria-hidden="true"></div>
      <div class="aurora aurora--hero" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="hero__spot" aria-hidden="true"></div>

      <div class="container hero__inner">
        <div class="hero__content">
          <p class="eyebrow hero__eyebrow" data-hero-item>
            <span class="eyebrow__line" aria-hidden="true"></span>
            <span data-i18n="gp.heroEyebrow">مطعم نجا · الدمام</span>
          </p>
          <h1 class="hero__title text-gradient-hero" id="galleryHeroTitle" data-hero-item data-i18n="gp.heroTitle">معرضنا</h1>
          <p class="hero__alt" data-hero-item data-i18n-alt="gp.heroTitle" lang="en" dir="ltr">Our Gallery</p>
          <p class="tagline" data-hero-item lang="en">
            <span class="tagline__text">Feel Fresh</span>
            <svg class="tagline__swoosh" aria-hidden="true"><use href="#i-swoosh"/></svg>
          </p>
          <p class="hero__copy" data-hero-item data-i18n="gp.heroCopy">لمحات من المائدة وأجواء المكان: مشويات وأطباق مميزة وحلويات. اضغط على أي صورة لتكبيرها.</p>
        </div>
      </div>
    </section>

    <!-- ======================= 02 — FILTERS + PHOTO GRID ======================= -->
    <section class="gallery-page" id="gallery-browse" aria-labelledby="galleryBrowseTitle" data-bg="#121212" data-anchor="gallery-browse">
      <div class="container">
        <h2 class="visually-hidden" id="galleryBrowseTitle" data-i18n="gp.browseTitle">صور المعرض</h2>

        <div class="gp-filters" id="galleryFilters" role="group" data-i18n-attr="aria-label:gp.filterLabel" aria-label="تصفية الصور حسب الفئة">
          <!-- rendered from najaGalleryCategories -->
        </div>
        <p class="gp-status" id="galleryStatus" role="status" aria-live="polite"></p>

        <ul class="gp-grid" id="galleryGrid" data-source="page">
          <!-- rendered from najaGalleryPage -->
        </ul>
        <p class="gp-empty" id="galleryEmpty" data-i18n="gp.empty" hidden>لا توجد صور في هذه الفئة حالياً.</p>

        <p class="notice notice--quiet gp-note" data-i18n="gal.note">الصور توضيحية</p>
      </div>
    </section>

    <!-- ======================= 03 — ORDER ======================= -->
    <section class="visit order-cta section" id="order" aria-labelledby="visitTitle" data-bg="#0d0d0d" data-anchor="order">
      <div class="visit__glow" aria-hidden="true"></div>
      <div class="aurora aurora--visit" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="container">
        <div class="visit__head">
          <p class="eyebrow eyebrow--center"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="gp.ctaEyebrow">من المعرض إلى المائدة</span><span class="eyebrow__line" aria-hidden="true"></span></p>
          <h2 class="section-title section-title--center text-gradient" id="visitTitle" data-reveal-heading data-i18n="gp.ctaTitle">اختر طبقك المفضل</h2>
          <p class="visit__alt" data-i18n-alt="gp.ctaTitle" lang="en" dir="ltr">Choose Your Favourite</p>
          <p class="section-copy section-copy--center" data-i18n="gp.ctaCopy">تصفّح القائمة الكاملة، ثم أرسل طلبك عبر واتساب.</p>

          <div class="visit__ctas" data-reveal>
            <a class="btn btn--brand btn--lg" <?php echo naja_menu_link(); ?>>
              <span data-i18n="cta.explore">استكشف قائمتنا</span>
              <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
            <a class="btn btn--ghost btn--lg" data-wa="general" data-hide-missing hidden>
              <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
              <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php
get_footer();
