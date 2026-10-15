<?php
/**
 * Template Name: Naja Contact
 *
 * Contact page: hero, contact cards, message form, location + map, quick
 * contact call to action. Details come from the Customizer (via
 * window.restaurantConfig); the form posts to inc/contact.php.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
  <main id="main">
    <!-- ======================= 01 — CONTACT HERO ======================= -->
    <section class="hero hero--contact" id="contact-hero" aria-labelledby="ctHeroTitle" data-bg="#121212" data-anchor="contact-hero">
      <div class="hero__media img-frame">
        <?php naja_hero_img( 'grill' ); ?>
      </div>
      <div class="hero__shade" aria-hidden="true"></div>
      <div class="aurora aurora--hero" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="hero__spot" aria-hidden="true"></div>

      <div class="container hero__inner">
        <div class="hero__content">
          <p class="eyebrow hero__eyebrow" data-hero-item>
            <span class="eyebrow__line" aria-hidden="true"></span>
            <span data-i18n="ct.heroEyebrow">مطعم نجا · الدمام</span>
          </p>
          <h1 class="hero__title text-gradient-hero" id="ctHeroTitle" data-hero-item data-i18n="ct.heroTitle">تواصل معنا</h1>
          <p class="hero__alt" data-hero-item data-i18n-alt="ct.heroTitle" lang="en" dir="ltr">Contact Us</p>
          <p class="tagline" data-hero-item lang="en">
            <span class="tagline__text">Feel Fresh</span>
            <svg class="tagline__swoosh" aria-hidden="true"><use href="#i-swoosh"/></svg>
          </p>
          <p class="hero__copy" data-hero-item data-i18n="ct.heroCopy">يسعدنا تواصلك: اتصل بنا، أو راسلنا عبر واتساب، أو اترك لنا رسالة من النموذج.</p>
        </div>
      </div>
      <a class="hero__scroll" href="#contact-info">
        <span data-i18n="hero.scroll">مرّر للأسفل</span>
        <span class="hero__scroll-line" aria-hidden="true"></span>
      </a>
    </section>

    <!-- ======================= 02 — CONTACT INFORMATION ======================= -->
    <section class="ct-info section" id="contact-info" aria-labelledby="ctInfoTitle" data-bg="#1a1a1a" data-anchor="contact-info">
      <div class="container">
        <header class="section-head section-head--split">
          <div>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="ct.infoEyebrow">معلومات التواصل</span></p>
            <h2 class="section-title" id="ctInfoTitle" data-reveal-heading data-i18n="ct.infoTitle">كيف تصل إلينا</h2>
          </div>
          <p class="section-copy" data-i18n="ct.infoCopy">اختر الطريقة الأنسب لك: اتصال مباشر، أو واتساب، أو زيارتنا في الدمام.</p>
        </header>
        <ul class="ct-cards" id="contactCards" data-reveal>
          <!-- rendered from the restaurant details (Customizer) -->
        </ul>
      </div>
    </section>

    <!-- ======================= 03 — CONTACT FORM ======================= -->
    <section class="ct-form-section section" id="contact-form" aria-labelledby="ctFormTitle" data-bg="#121212" data-anchor="contact-form">
      <div class="container ct-form-wrap">
        <div class="ct-form-intro">
          <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="ct.formEyebrow">راسلنا</span></p>
          <h2 class="section-title" id="ctFormTitle" data-reveal-heading data-i18n="ct.formTitle">أرسل لنا رسالة</h2>
          <p class="section-copy" data-reveal data-i18n="ct.formCopy">اكتب لنا استفسارك أو ملاحظتك وسيطّلع عليها فريق المطعم. للأمور العاجلة، الاتصال أو واتساب أسرع.</p>
          <a class="btn btn--ghost" data-wa="general" data-hide-missing hidden data-reveal>
            <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
            <span data-i18n="cta.whatsappShort">واتساب</span>
          </a>
        </div>

        <form class="ct-form" id="contactForm" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate aria-labelledby="ctFormTitle">
          <div class="ct-status" id="ctStatus" role="status" aria-live="polite" tabindex="-1" hidden></div>
          <div class="ct-summary" id="ctSummary" role="alert" tabindex="-1" hidden></div>

          <p class="ct-required-note" data-i18n="ct.requiredNote">الحقول المميزة بـ * مطلوبة.</p>

          <div class="ct-grid">
            <div class="ct-field">
              <label for="ctName"><span data-i18n="ct.name">الاسم الكامل</span> <span class="ct-req" aria-hidden="true">*</span></label>
              <input type="text" id="ctName" name="name" autocomplete="name" required maxlength="100" aria-describedby="ctNameErr">
              <p class="ct-error" id="ctNameErr" hidden></p>
            </div>
            <div class="ct-field">
              <label for="ctPhone"><span data-i18n="ct.phone">رقم الهاتف</span> <span class="ct-req" aria-hidden="true">*</span></label>
              <input type="tel" id="ctPhone" name="phone" autocomplete="tel" inputmode="tel" dir="ltr" required maxlength="25" aria-describedby="ctPhoneErr">
              <p class="ct-error" id="ctPhoneErr" hidden></p>
            </div>
            <div class="ct-field">
              <label for="ctEmail"><span data-i18n="ct.email">البريد الإلكتروني</span> <span class="ct-opt" data-i18n="ct.optional">(اختياري)</span></label>
              <input type="email" id="ctEmail" name="email" autocomplete="email" inputmode="email" dir="ltr" maxlength="120" aria-describedby="ctEmailErr">
              <p class="ct-error" id="ctEmailErr" hidden></p>
            </div>
            <div class="ct-field">
              <label for="ctSubject"><span data-i18n="ct.subject">الموضوع</span> <span class="ct-req" aria-hidden="true">*</span></label>
              <input type="text" id="ctSubject" name="subject" required maxlength="150" aria-describedby="ctSubjectErr">
              <p class="ct-error" id="ctSubjectErr" hidden></p>
            </div>
            <div class="ct-field ct-field--wide">
              <label for="ctMessage"><span data-i18n="ct.message">الرسالة</span> <span class="ct-req" aria-hidden="true">*</span></label>
              <textarea id="ctMessage" name="message" rows="6" required maxlength="2000" aria-describedby="ctMessageErr"></textarea>
              <p class="ct-error" id="ctMessageErr" hidden></p>
            </div>
          </div>

          <!-- Spam trap: hidden from people, filled in by bots -->
          <div class="ct-hp" aria-hidden="true">
            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
          </div>
          <input type="hidden" name="action" value="naja_contact">
          <input type="hidden" name="ts" id="ctTs" value="<?php echo (int) time(); ?>">
          <input type="hidden" name="token" id="ctToken" value="">
          <input type="hidden" name="lang" id="ctLang" value="ar">

          <button type="submit" class="btn btn--brand btn--lg ct-submit" id="ctSubmit">
            <span class="ct-submit__spinner" aria-hidden="true"></span>
            <span class="ct-submit__label" data-i18n="ct.send">إرسال الرسالة</span>
            <svg class="icon icon--dir ct-submit__arrow" aria-hidden="true"><use href="#i-arrow"/></svg>
          </button>
        </form>
      </div>
    </section>

    <!-- ======================= 04 — LOCATION & MAP ======================= -->
    <section class="ct-location section" id="contact-location" aria-labelledby="ctLocTitle" data-bg="#1a1a1a" data-anchor="contact-location">
      <div class="container ct-location__grid">
        <div class="ct-location__text">
          <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="ct.locEyebrow">موقعنا</span></p>
          <h2 class="section-title" id="ctLocTitle" data-reveal-heading data-i18n="ct.locTitle">زُرنا في الدمام</h2>
          <p class="ct-location__label" data-i18n="visit.address">العنوان</p>
          <p class="ct-location__address" id="contactAddress"></p>
          <a class="btn btn--brand btn--lg" data-action="map" data-reveal>
            <svg class="icon" aria-hidden="true"><use href="#i-pin"/></svg>
            <span data-i18n="visit.directions">الاتجاهات على الخريطة</span>
          </a>
        </div>

        <div class="ct-map" id="contactMap" data-reveal>
          <div class="ct-map__ph" data-map-placeholder>
            <svg class="ct-map__svg" viewBox="0 0 320 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
              <rect width="320" height="200" fill="#161616"/>
              <g fill="none" stroke="currentColor" stroke-linecap="round" opacity="0.22">
                <path d="M18 18L118 70L150 132L140 200"/>
                <path d="M0 82L92 92L198 58L232 40"/>
                <path d="M58 200L100 124L226 112"/>
                <path d="M0 128L64 120L84 40L70 0"/>
                <path d="M176 0L186 60L214 160L206 200"/>
              </g>
              <circle cx="168" cy="98" r="40" fill="#ee1d23" opacity="0.12"/>
              <path d="M168 101c-6-6.5-9-11-9-15a9 9 0 0 1 18 0c0 4-3 8.5-9 15Z" fill="#ee1d23"/>
              <circle cx="168" cy="86" r="3.2" fill="#121212"/>
            </svg>
            <div class="ct-map__ph-text">
              <p class="ct-map__city" data-i18n="visit.cityLine">الدمام، المملكة العربية السعودية</p>
              <p class="ct-map__soon" data-i18n="ct.mapSoon">سنضيف موقع المطعم الدقيق على الخريطة قريباً.</p>
              <?php if ( current_user_can( 'manage_options' ) ) : ?>
                <p class="ct-map__setup">
                  <strong>Setup needed (only visible to admins):</strong>
                  Appearance → Customize → Naja Restaurant → Contact &amp; location → paste the Google Maps embed (Share → Embed a map) of the restaurant pin into "Google Maps embed". Until then no map is shown, so no wrong location can appear.
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======================= 05 — QUICK CONTACT CTA ======================= -->
    <section class="visit order-cta section" id="contact-cta" aria-labelledby="ctCtaTitle" data-bg="#0d0d0d" data-anchor="contact-cta">
      <div class="visit__glow" aria-hidden="true"></div>
      <div class="aurora aurora--visit" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="container">
        <div class="visit__head">
          <p class="eyebrow eyebrow--center"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="ct.ctaEyebrow">نحن بانتظارك</span><span class="eyebrow__line" aria-hidden="true"></span></p>
          <h2 class="section-title section-title--center text-gradient" id="ctCtaTitle" data-reveal-heading data-i18n="ct.ctaTitle">يسعدنا تواصلك</h2>
          <p class="visit__alt" data-i18n-alt="ct.ctaTitle" lang="en" dir="ltr">We'd Love to Hear from You</p>
          <p class="section-copy section-copy--center" data-i18n="visit.copy">اطلب عبر واتساب أو اتصل بنا أو زرنا في الدمام.</p>

          <div class="visit__ctas" data-reveal>
            <a class="btn btn--brand btn--lg" data-wa="general">
              <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
              <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
            </a>
            <a class="btn btn--ghost btn--lg" data-action="call">
              <svg class="icon" aria-hidden="true"><use href="#i-phone"/></svg>
              <span data-i18n="visit.call">اتصل الآن</span>
            </a>
            <a class="btn btn--ghost btn--lg" data-action="map">
              <svg class="icon" aria-hidden="true"><use href="#i-pin"/></svg>
              <span data-i18n="visit.directions">الاتجاهات على الخريطة</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php
get_footer();
