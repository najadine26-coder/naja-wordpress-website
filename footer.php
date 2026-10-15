<?php
/**
 * Footer, mobile action bar, lightbox, toast and icon sprite.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
  <!-- =============================== FOOTER =============================== -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-cta" data-reveal>
        <div class="footer-cta__copy">
          <h2 class="footer-cta__title" data-i18n="footer.ctaTitle">جاهز للطلب؟</h2>
          <p class="footer-cta__text" data-i18n="footer.ctaText">أرسل طلبك عبر واتساب، ويؤكد المطعم التوفر والمبلغ الإجمالي النهائي.</p>
        </div>
        <a class="btn btn--brand btn--lg" data-wa="general">
          <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
          <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
        </a>
      </div>
    </div>

    <div class="container site-footer__grid">
      <div class="site-footer__brand">
        <a class="wordmark" <?php echo naja_section_link( '' ); ?> data-i18n-attr="aria-label:brand.home" aria-label="مطعم نجا — الصفحة الرئيسية">
          <span class="wordmark__latin" lang="en">Naja</span>
          <span class="wordmark__rule" aria-hidden="true"></span>
          <span class="wordmark__arabic" lang="ar">نجا</span>
        </a>
        <p class="tagline tagline--sm" lang="en"><span class="tagline__text">Feel Fresh</span><svg class="tagline__swoosh" aria-hidden="true"><use href="#i-swoosh"/></svg></p>
        <p class="site-footer__about" data-i18n="footer.about">مطعم نجا في الدمام. قائمة متنوعة من المشويات والأرز والبرجر والباستا والحلويات، والطلب سهل عبر واتساب.</p>
        <div class="site-footer__social" id="footerSocialCol" hidden>
          <h2 class="site-footer__title" data-i18n="footer.follow">تابعنا</h2>
          <ul id="footerSocial"></ul>
        </div>
      </div>
      <nav class="site-footer__col" aria-labelledby="footNavTitle">
        <h2 class="site-footer__title" id="footNavTitle" data-i18n="footer.nav">روابط سريعة</h2>
        <ul>
          <li><a <?php echo naja_section_link( '' ); ?> data-i18n="nav.home">الرئيسية</a></li>
          <li><a <?php echo naja_menu_link(); ?> data-i18n="nav.menu">القائمة</a></li>
          <li><a <?php echo naja_experience_link(); ?> data-i18n="nav.experience">التجربة</a></li>
          <li><a <?php echo naja_gallery_link(); ?> data-i18n="nav.gallery">المعرض</a></li>
          <li><a <?php echo naja_section_link( '#how-to-order' ); ?> data-i18n="nav.order">طريقة الطلب</a></li>
          <li><a <?php echo naja_blog_link(); ?> data-i18n="nav.blog">المدونة</a></li>
          <li><a <?php echo naja_contact_link(); ?> data-i18n="nav.contact">تواصل معنا</a></li>
        </ul>
      </nav>
      <div class="site-footer__col">
        <h2 class="site-footer__title" data-i18n="footer.contact">تواصل</h2>
        <ul class="footer-contact" id="footerContact"></ul>
      </div>
      <div class="site-footer__col site-footer__map">
        <h2 class="site-footer__title" data-i18n="footer.map">موقعنا</h2>
        <a class="map-card" data-action="map" data-inview data-i18n-attr="aria-label:footer.mapOpen" aria-label="افتح الدمام في خرائط Google">
          <span class="map-card__frame">
            <svg class="map-card__svg" viewBox="0 0 320 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
              <defs>
                <pattern id="mapGrid" width="16" height="16" patternUnits="userSpaceOnUse"><path d="M16 0H0V16" fill="none" stroke="currentColor" stroke-width="0.5" opacity="0.08"/></pattern>
                <linearGradient id="mapSea" x1="0" x2="1"><stop offset="0" stop-color="#7a0b10" stop-opacity="0.35"/><stop offset="1" stop-color="#121212" stop-opacity="0.9"/></linearGradient>
                <radialGradient id="mapGlow"><stop offset="0" stop-color="#ee1d23" stop-opacity="0.45"/><stop offset="1" stop-color="#ee1d23" stop-opacity="0"/></radialGradient>
              </defs>
              <rect width="320" height="200" fill="url(#mapGrid)"/>
              <path class="map-sea" d="M236 0C214 34 252 70 228 112C208 150 246 176 236 200H320V0Z" fill="url(#mapSea)"/>
              <path class="map-wave" d="M262 40q8-5 16 0t16 0M256 92q8-5 16 0t16 0M266 146q8-5 16 0t16 0" fill="none" stroke="currentColor" stroke-width="1" opacity="0.25"/>
              <g class="map-roads" fill="none" stroke="currentColor" stroke-linecap="round">
                <path class="map-road" pathLength="100" d="M18 18L118 70L150 132L140 200"/>
                <path class="map-road" pathLength="100" d="M0 82L92 92L198 58L232 40"/>
                <path class="map-road" pathLength="100" d="M58 200L100 124L226 112"/>
                <path class="map-road map-road--minor" pathLength="100" d="M0 128L64 120L84 40L70 0"/>
                <path class="map-road map-road--minor" pathLength="100" d="M176 0L186 60L214 160L206 200"/>
              </g>
              <path class="map-route" pathLength="100" d="M12 176C60 162 96 144 118 122S150 100 168 98"/>
              <path class="map-route-flow" pathLength="100" d="M12 176C60 162 96 144 118 122S150 100 168 98"/>
              <circle cx="168" cy="98" r="40" fill="url(#mapGlow)"/>
              <circle class="map-pulse" cx="168" cy="98" r="7"/>
              <circle class="map-pulse map-pulse--late" cx="168" cy="98" r="7"/>
              <path class="map-pin" d="M168 101c-6-6.5-9-11-9-15a9 9 0 0 1 18 0c0 4-3 8.5-9 15Z"/>
              <circle cx="168" cy="86" r="3.2" fill="#121212"/>
              <g class="map-compass" transform="translate(28 30)"><circle r="11" fill="none" stroke="currentColor" opacity="0.3"/><path d="M0-8L3 0H-3Z" fill="#ee1d23"/><text y="-14" text-anchor="middle" font-size="8" fill="currentColor" opacity="0.6" font-family="Inter, sans-serif">N</text></g>
            </svg>
            <span class="map-card__label">
              <span class="map-card__city" data-i18n="footer.mapCity">الدمام</span>
              <span class="map-card__country" data-i18n="footer.mapCountry">المملكة العربية السعودية</span>
            </span>
          </span>
          <span class="map-card__open">
            <span data-i18n="footer.mapOpenShort">افتح في خرائط Google</span>
            <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
          </span>
        </a>
      </div>
    </div>

    <div class="container site-footer__base">
      <p id="footerRights"></p>
      <p class="site-footer__disclaimer" data-i18n="footer.disclaimer">الصور والأصناف والأسعار المعروضة توضيحية حالياً وقد لا تمثّل أطباق المطعم الفعلية.</p>
      <a href="#main" class="site-footer__top">
        <span data-i18n="footer.top">العودة للأعلى</span>
        <svg class="icon icon--up" aria-hidden="true"><use href="#i-arrow"/></svg>
      </a>
    </div>
  </footer>

  <!-- Mobile bottom action bar (shown only when phone/WhatsApp are configured) -->
  <div class="action-bar" id="actionBar" hidden>
    <nav class="action-bar__inner" data-i18n-attr="aria-label:bar.label" aria-label="إجراءات سريعة">
      <a class="action-bar__btn" data-action="call" id="barCall">
        <svg class="icon" aria-hidden="true"><use href="#i-phone"/></svg>
        <span data-i18n="bar.call">اتصال</span>
      </a>
      <a class="action-bar__btn action-bar__btn--brand" data-wa="general" id="barWa">
        <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
        <span data-i18n="cta.whatsappShort">واتساب</span>
      </a>
    </nav>
  </div>

  <!-- Lightbox -->
  <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" data-i18n-attr="aria-label:lb.label" aria-label="عارض الصور" hidden>
    <div class="lightbox__backdrop" data-lb-close></div>
    <figure class="lightbox__figure">
      <div class="lightbox__img-wrap img-frame">
        <img class="lightbox__img" id="lightboxImg" alt="" decoding="async">
      </div>
      <figcaption class="lightbox__caption">
        <span id="lightboxCaption"></span>
        <span class="lightbox__count" id="lightboxCount"></span>
      </figcaption>
    </figure>
    <button type="button" class="lightbox__btn lightbox__close" data-lb-close data-i18n-attr="aria-label:lb.close" aria-label="إغلاق">
      <svg class="icon" aria-hidden="true"><use href="#i-close"/></svg>
    </button>
    <button type="button" class="lightbox__btn lightbox__prev" id="lightboxPrev" data-i18n-attr="aria-label:lb.prev" aria-label="الصورة السابقة">
      <svg class="icon icon--back" aria-hidden="true"><use href="#i-arrow"/></svg>
    </button>
    <button type="button" class="lightbox__btn lightbox__next" id="lightboxNext" data-i18n-attr="aria-label:lb.next" aria-label="الصورة التالية">
      <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
    </button>
  </div>

  <div class="toast" id="toast" role="status" aria-live="polite"></div>

  <!-- Icon sprite -->
  <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
    <symbol id="i-whatsapp" viewBox="0 0 24 24"><path fill="currentColor" d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.45 9.9-9.91A9.85 9.85 0 0 0 12.04 2Zm0 18.15h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.25-8.23a8.2 8.2 0 0 1 8.23 8.24c0 4.54-3.7 8.23-8.23 8.23Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.15.16-.29.18-.54.06a6.7 6.7 0 0 1-3.32-2.9c-.25-.43.25-.4.72-1.33.08-.17.04-.31-.02-.43-.06-.13-.56-1.34-.76-1.84-.2-.48-.4-.41-.56-.42h-.47a.9.9 0 0 0-.66.31c-.23.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.24 3.74 1.57.68 2.19.74 2.98.62.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.14-1.18-.06-.11-.22-.17-.47-.29Z"/></symbol>
    <symbol id="i-swoosh" viewBox="0 0 240 28" preserveAspectRatio="none"><path fill="currentColor" d="M2 20.5C46 12 112 5.5 176 5.2c22-.1 42 .6 62 2.3-1 .9-2.4 1.4-4 1.5C171 8 102 13.6 30 25.5c-9 1.5-17.5 1.2-24-1.3-2.2-.9-3.2-2.3-4-3.7Z"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M4 12h15m-5-5 5 5-5 5"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.5"/><path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" d="m16 16 4 4"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" d="M5 4h3.5l1.5 4-2 1.5a11 11 0 0 0 6.5 6.5L16 14l4 1.5V19a1.5 1.5 0 0 1-1.6 1.5C10.6 20 4 13.4 3.5 5.6A1.5 1.5 0 0 1 5 4Z"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5" fill="none" stroke="currentColor" stroke-width="1.5"/><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" d="M12 7.5V12l3 2"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.5" d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3" fill="none" stroke="currentColor" stroke-width="1.5"/></symbol>
  </svg>

  <?php wp_footer(); ?>
</body>
</html>
