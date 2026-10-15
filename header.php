<?php
/**
 * Header: <head>, fixed site header and mobile navigation.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html lang="ar" dir="rtl" class="no-js<?php echo naja_is_menu_page() ? ' page-menu' : ''; ?><?php echo naja_is_gallery_page() ? ' page-gallery' : ''; ?><?php echo naja_is_experience_page() ? ' page-experience' : ''; ?><?php echo naja_is_contact_page() ? ' page-contact' : ''; ?>">
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <script>document.documentElement.classList.replace('no-js', 'js');</script>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> <?php naja_body_attrs(); ?>>
<?php wp_body_open(); ?>
  <div class="scroll-progress" aria-hidden="true"><span id="scrollProgress"></span></div>
  <a class="skip-link" href="#main" data-i18n="skip">تخطَّ إلى المحتوى</a>

  <!-- =============================== HEADER =============================== -->
  <header class="site-header" id="siteHeader">
    <div class="container site-header__bar">
      <a class="wordmark" <?php echo naja_section_link( '' ); ?> data-i18n-attr="aria-label:brand.home" aria-label="مطعم نجا — الصفحة الرئيسية">
        <span class="wordmark__latin" lang="en">Naja</span>
        <span class="wordmark__rule" aria-hidden="true"></span>
        <span class="wordmark__arabic" lang="ar">نجا</span>
      </a>

      <nav class="primary-nav" data-i18n-attr="aria-label:nav.label" aria-label="التنقل الرئيسي">
        <ul class="primary-nav__list">
          <li><a <?php echo naja_section_link( '' ); ?> data-i18n="nav.home">الرئيسية</a></li>
          <li><a <?php echo naja_menu_link(); ?><?php echo naja_is_menu_page() ? ' class="is-active"' : ''; ?> data-i18n="nav.menu">القائمة</a></li>
          <li><a <?php echo naja_experience_link(); ?><?php echo naja_is_experience_page() ? ' class="is-active"' : ''; ?> data-i18n="nav.experience">التجربة</a></li>
          <li><a <?php echo naja_gallery_link(); ?><?php echo naja_is_gallery_page() ? ' class="is-active"' : ''; ?> data-i18n="nav.gallery">المعرض</a></li>
          <li><a <?php echo naja_contact_link(); ?><?php echo naja_is_contact_page() ? ' class="is-active"' : ''; ?> data-i18n="nav.contact">تواصل معنا</a></li>
        </ul>
      </nav>

      <div class="site-header__actions">
        <button type="button" class="lang-switch" data-lang-toggle data-i18n-attr="aria-label:lang.aria" aria-label="Switch to English">
          <span data-i18n="lang.switch">EN</span>
        </button>
        <a class="btn btn--brand btn--sm site-header__wa" data-wa="general">
          <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
          <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
        </a>
        <button type="button" class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="mobileNav" data-i18n-attr="aria-label:nav.open" aria-label="فتح القائمة">
          <span class="menu-toggle__bar"></span><span class="menu-toggle__bar"></span>
        </button>
      </div>
    </div>
  </header>

  <!-- Mobile navigation panel -->
  <div class="mobile-nav" id="mobileNav" hidden>
    <nav class="container mobile-nav__inner" data-i18n-attr="aria-label:nav.label" aria-label="التنقل الرئيسي">
      <ol class="mobile-nav__list">
        <li><a <?php echo naja_section_link( '' ); ?>><span class="mobile-nav__num">01</span><span data-i18n="nav.home">الرئيسية</span></a></li>
        <li><a <?php echo naja_menu_link(); ?>><span class="mobile-nav__num">02</span><span data-i18n="nav.menu">القائمة</span></a></li>
        <li><a <?php echo naja_experience_link(); ?>><span class="mobile-nav__num">03</span><span data-i18n="nav.experience">التجربة</span></a></li>
        <li><a <?php echo naja_gallery_link(); ?>><span class="mobile-nav__num">04</span><span data-i18n="nav.gallery">المعرض</span></a></li>
        <li><a <?php echo naja_section_link( '#how-to-order' ); ?>><span class="mobile-nav__num">05</span><span data-i18n="nav.order">طريقة الطلب</span></a></li>
        <li><a <?php echo naja_contact_link(); ?>><span class="mobile-nav__num">06</span><span data-i18n="nav.contact">تواصل معنا</span></a></li>
      </ol>
      <div class="mobile-nav__foot">
        <button type="button" class="btn btn--ghost" data-lang-toggle>
          <span data-i18n="lang.switchFull">English</span>
        </button>
        <a class="btn btn--brand" data-wa="general">
          <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
          <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
        </a>
      </div>
    </nav>
  </div>

