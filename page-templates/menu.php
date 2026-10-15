<?php
/**
 * Template Name: Naja Menu
 *
 * The full menu: hero, sticky categories, search, dish cards, gallery, order.
 * Dishes come from Dishes in wp-admin (or the samples in js/menu-data.js).
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
  <main id="main">
    <!-- ======================= 01 — MENU HERO ======================= -->
    <section class="hero hero--menu" id="menu-hero" aria-labelledby="menuHeroTitle" data-bg="#121212" data-anchor="menu-hero">
      <div class="hero__media img-frame">
        <?php naja_hero_img( 'presentation' ); ?>
      </div>
      <div class="hero__shade" aria-hidden="true"></div>
      <div class="aurora aurora--hero" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="hero__spot" aria-hidden="true"></div>

      <div class="container hero__inner">
        <div class="hero__content">
          <p class="eyebrow hero__eyebrow" data-hero-item>
            <span class="eyebrow__line" aria-hidden="true"></span>
            <span data-i18n="mp.heroEyebrow">مطعم نجا · الدمام</span>
          </p>
          <h1 class="hero__title text-gradient-hero" id="menuHeroTitle" data-hero-item data-i18n="mp.heroTitle">قائمة الطعام</h1>
          <p class="hero__alt" data-hero-item data-i18n-alt="mp.heroTitle" lang="en" dir="ltr">Explore Our Menu</p>
          <p class="tagline" data-hero-item lang="en">
            <span class="tagline__text">Feel Fresh</span>
            <svg class="tagline__swoosh" aria-hidden="true"><use href="#i-swoosh"/></svg>
          </p>
          <p class="hero__copy" data-hero-item data-i18n="mp.heroCopy">من المشويات والأرز إلى البرجر والباستا والحلويات. تصفّح الأقسام، اختر طبقك، ثم اطلبه عبر واتساب.</p>
          <div class="hero__ctas" data-hero-item>
            <a class="btn btn--brand btn--lg" href="#menu-search">
              <span data-i18n="mp.viewDishes">تصفّح الأطباق</span>
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

    <div class="menu-browse">
      <!-- ======================= 02 — STICKY MENU CATEGORIES ======================= -->
      <nav class="menu-nav" id="menu-categories" data-i18n-attr="aria-label:menu.categories" aria-label="أقسام القائمة">
        <div class="container">
          <div class="menu-nav__cats" id="menuCats" role="group" data-counts data-i18n-attr="aria-label:menu.categories" aria-label="أقسام القائمة">
            <!-- rendered from najaMenuCategories (empty categories are hidden) -->
          </div>
        </div>
      </nav>

      <!-- ======================= 03 — SEARCH AND FILTERS ======================= -->
      <section class="menu-search" id="menu-search" aria-labelledby="menuSearchTitle" data-bg="#121212" data-anchor="menu-search">
        <div class="container menu-search__grid">
          <div>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="mp.searchEyebrow">ابحث وصفِّ</span></p>
            <h2 class="section-title" id="menuSearchTitle" data-reveal-heading data-i18n="mp.searchTitle">ابحث عن طبقك</h2>
          </div>
          <div class="menu-search__tools">
            <div class="menu__search">
              <label class="visually-hidden" for="menuSearch" data-i18n="menu.searchLabel">ابحث في القائمة</label>
              <svg class="icon menu__search-icon" aria-hidden="true"><use href="#i-search"/></svg>
              <input type="search" id="menuSearch" autocomplete="off" enterkeyhint="search" aria-describedby="menuSearchHint"
                     data-i18n-attr="placeholder:menu.searchPlaceholder" placeholder="ابحث عن طبق…">
              <button type="button" class="menu__search-clear" id="menuSearchClear" hidden data-i18n-attr="aria-label:menu.clear" aria-label="مسح البحث">
                <svg class="icon" aria-hidden="true"><use href="#i-close"/></svg>
              </button>
            </div>
            <p class="menu-search__hint" id="menuSearchHint" data-i18n="mp.searchHint">ابحث بالعربية أو الإنجليزية، أو اختر قسماً من الشريط أعلاه.</p>
            <div class="menu-search__status">
              <p class="menu__count" id="menuCount" role="status" aria-live="polite"></p>
              <p class="filter-chip" id="menuActive" hidden>
                <span id="menuActiveLabel"></span>
                <button type="button" class="filter-chip__clear" id="menuActiveClear" data-i18n-attr="aria-label:mp.clearCat" aria-label="إزالة تصفية القسم">
                  <svg class="icon" aria-hidden="true"><use href="#i-close"/></svg>
                </button>
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- ======================= 04 — FOOD MENU GRID ======================= -->
      <section class="menu-dishes" id="menu-dishes" aria-labelledby="menuDishesTitle" data-bg="#121212">
        <div class="container">
          <span id="menuResultsStart" aria-hidden="true"></span>
          <header class="menu-dishes__head">
            <div>
              <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="mp.gridEyebrow">أطباقنا</span></p>
              <h2 class="section-title" id="menuDishesTitle" data-i18n="mp.gridTitle">كل ما في القائمة</h2>
            </div>
            <p class="notice" data-i18n="menu.notice">قائمة تجريبية — ستُحدَّث الأصناف والأسعار بعد اعتمادها من المطعم.</p>
          </header>

          <ul class="menu-cards" id="menuGrid" data-layout="cards">
            <!-- rendered from najaMenuItems -->
          </ul>

          <div class="menu__empty" id="menuEmpty" hidden>
            <span class="menu__empty-mark" aria-hidden="true"></span>
            <h3 data-i18n="menu.emptyTitle">لا توجد نتائج</h3>
            <p data-i18n="menu.emptyText">لم نجد أطباقاً تطابق بحثك.</p>
            <button type="button" class="btn btn--ghost" id="menuReset" data-i18n="menu.reset">عرض كل الأصناف</button>
          </div>
        </div>
      </section>
    </div>

    <!-- ======================= 05 — FEATURED FOOD GALLERY ======================= -->
    <section class="gallery section" id="featured-gallery" aria-labelledby="galTitle" data-bg="#121212" data-anchor="featured-gallery">
      <div class="container">
        <header class="section-head section-head--split">
          <div>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="mp.galEyebrow">مختارات</span></p>
            <h2 class="section-title" id="galTitle" data-reveal-heading data-i18n="mp.galTitle">معرض الأطباق المميزة</h2>
          </div>
          <div>
            <p class="section-copy" data-i18n="mp.galCopy">لمحات بصرية من أصناف القائمة. اضغط على أي صورة لتكبيرها.</p>
            <p class="notice notice--quiet" data-i18n="gal.note">الصور توضيحية</p>
          </div>
        </header>
        <ul class="gallery-grid gallery-grid--menu" id="galleryGrid" data-source="menu">
          <!-- rendered from najaMenuGallery -->
        </ul>
      </div>
    </section>

    <!-- ======================= 06 — ORDER ======================= -->
    <section class="visit order-cta section" id="order" aria-labelledby="visitTitle" data-bg="#0d0d0d" data-anchor="order">
      <div class="visit__glow" aria-hidden="true"></div>
      <div class="aurora aurora--visit" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="container">
        <div class="visit__head">
          <p class="eyebrow eyebrow--center"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="mp.orderEyebrow">اطلب الآن</span><span class="eyebrow__line" aria-hidden="true"></span></p>
          <h2 class="section-title section-title--center text-gradient" id="visitTitle" data-reveal-heading data-i18n="mp.orderTitle">وجدت طبقك المفضل؟</h2>
          <p class="visit__alt" data-i18n-alt="mp.orderTitle" lang="en" dir="ltr">Found Your Favourite?</p>
          <p class="tagline tagline--center tagline--sm" lang="en">
            <span class="tagline__text">Feel Fresh</span>
            <svg class="tagline__swoosh" aria-hidden="true"><use href="#i-swoosh"/></svg>
          </p>
          <p class="section-copy section-copy--center" data-i18n="mp.orderCopy">أرسل طلبك عبر واتساب أو اتصل بنا، ويؤكد المطعم التوفر والمبلغ الإجمالي النهائي.</p>

          <div class="visit__ctas" data-reveal>
            <a class="btn btn--brand btn--lg" data-wa="general" data-hide-missing hidden>
              <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
              <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
            </a>
            <a class="btn btn--ghost btn--lg" data-action="call" data-hide-missing hidden>
              <svg class="icon" aria-hidden="true"><use href="#i-phone"/></svg>
              <span data-i18n="visit.call">اتصل الآن</span>
            </a>
            <a class="btn btn--ghost btn--lg" data-action="map" data-hide-missing hidden>
              <svg class="icon" aria-hidden="true"><use href="#i-pin"/></svg>
              <span data-i18n="visit.directions">الاتجاهات على الخريطة</span>
            </a>
          </div>
          <p class="notice notice--quiet order-cta__pending" id="orderPending" data-i18n="mp.orderPending" hidden>سننشر وسائل الطلب والتواصل قريباً.</p>
        </div>

        <dl class="visit__info" data-reveal data-hide-missing-info hidden>
          <div class="visit__cell" data-info-cell="address" hidden>
            <dt data-i18n="visit.address">العنوان</dt>
            <dd id="infoAddress"></dd>
          </div>
          <div class="visit__cell" data-info-cell="hours" hidden>
            <dt data-i18n="visit.hours">ساعات العمل</dt>
            <dd id="infoHours"></dd>
          </div>
          <div class="visit__cell" data-info-cell="phone" hidden>
            <dt data-i18n="visit.phone">الهاتف</dt>
            <dd id="infoPhone"></dd>
          </div>
          <div class="visit__cell" data-info-cell="whatsapp" hidden>
            <dt data-i18n="visit.whatsapp">واتساب</dt>
            <dd id="infoWhatsapp"></dd>
          </div>
        </dl>
      </div>
    </section>
  </main>

<?php
get_footer();
