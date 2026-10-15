<?php
/**
 * Template Name: Naja Experience
 *
 * The Naja Experience: cinematic hero, philosophy, food, ambience, highlights
 * and a closing call to action. Wording reuses the site's existing copy
 * (js/translations.js, xp.* keys); photos come from window.najaImages.
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
  <main id="main">
    <!-- ======================= A — CINEMATIC HERO ======================= -->
    <section class="hero hero--experience" id="experience-hero" aria-labelledby="xpHeroTitle" data-bg="#121212" data-anchor="experience-hero">
      <div class="hero__media img-frame">
        <?php naja_hero_img( 'plated' ); ?>
      </div>
      <div class="hero__shade" aria-hidden="true"></div>
      <div class="aurora aurora--hero" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="hero__spot" aria-hidden="true"></div>

      <div class="container hero__inner">
        <div class="hero__content">
          <p class="eyebrow hero__eyebrow" data-hero-item>
            <span class="eyebrow__line" aria-hidden="true"></span>
            <span data-i18n="xp.heroEyebrow">مطعم نجا · الدمام</span>
          </p>
          <h1 class="hero__title text-gradient-hero" id="xpHeroTitle" data-hero-item data-i18n="xp.heroTitle">تجربة نجا</h1>
          <p class="hero__alt" data-hero-item data-i18n-alt="xp.heroTitle" lang="en" dir="ltr">The Naja Experience</p>
          <p class="tagline" data-hero-item lang="en">
            <span class="tagline__text">Feel Fresh</span>
            <svg class="tagline__swoosh" aria-hidden="true"><use href="#i-swoosh"/></svg>
          </p>
          <p class="hero__copy" data-hero-item data-i18n="xp.heroCopy">قائمة متنوعة تناسب كل الأذواق، في أجواء مريحة تجمع العائلة والأصدقاء. اكتشف ما ينتظرك في نجا.</p>
        </div>
      </div>
      <a class="hero__scroll" href="#xp-philosophy">
        <span data-i18n="hero.scroll">مرّر للأسفل</span>
        <span class="hero__scroll-line" aria-hidden="true"></span>
      </a>
    </section>

    <!-- ======================= B — OUR PHILOSOPHY ======================= -->
    <section class="xp-philosophy section" id="xp-philosophy" aria-labelledby="xpPhilosophyTitle" data-bg="#1a1a1a" data-anchor="xp-philosophy">
      <div class="container xp-split">
        <div class="xp-split__text">
          <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="xp.philEyebrow">فلسفتنا</span></p>
          <h2 class="section-title section-title--xl text-gradient" id="xpPhilosophyTitle" data-reveal-heading data-i18n="xp.philTitle">الضيافة والطعام ولحظات تبقى</h2>
          <p class="xp-alt" data-i18n-alt="xp.philTitle" lang="en" dir="ltr">Hospitality, Food and Moments Worth Remembering</p>
          <span class="accent-rule" aria-hidden="true"></span>
          <p class="section-copy" data-reveal data-i18n="xp.philCopy1">في نجا نؤمن بأن الوجبة الجيدة تبدأ من المائدة وتمتد إلى اللحظات التي تُشاركها.</p>
          <p class="section-copy section-copy--muted" data-reveal data-i18n="xp.philCopy2">قائمة متنوعة تناسب كل الأذواق، في أجواء مريحة تجمع العائلة والأصدقاء.</p>
        </div>
        <div class="xp-split__media">
          <div class="xp-frame img-frame" data-xp-parallax>
            <span class="xp-zoom"><img data-img="presentation" data-img-sizes="(min-width: 1024px) 45vw, 100vw" alt="طبق لحم مقدم على لوح تقديم" loading="lazy" decoding="async" width="1100" height="1467"></span>
          </div>
          <span class="xp-outline" aria-hidden="true"></span>
        </div>
      </div>
    </section>

    <!-- ======================= C — FOOD & PRESENTATION ======================= -->
    <section class="xp-food section" id="xp-food" aria-labelledby="xpFoodTitle" data-bg="#121212" data-anchor="xp-food">
      <div class="container">
        <header class="section-head section-head--split">
          <div>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="xp.foodEyebrow">الطعام والتقديم</span></p>
            <h2 class="section-title" id="xpFoodTitle" data-reveal-heading data-i18n="xp.foodTitle">لكل ذوقٍ حكاية، ولكل طبقٍ لحظة.</h2>
          </div>
          <div>
            <p class="section-copy" data-i18n="xp.foodCopy">من المشويات والأرز إلى البرجر والباستا والحلويات، قائمة متنوعة تتسع لكل الأذواق.</p>
            <p class="notice notice--quiet" data-i18n="gal.note">الصور توضيحية</p>
          </div>
        </header>

        <div class="xp-food__grid">
          <figure class="xp-card xp-card--feature" data-reveal>
            <div class="xp-frame img-frame" data-xp-parallax>
              <span class="xp-zoom"><img data-img="grill" data-img-sizes="(min-width: 1024px) 55vw, 100vw" alt="أسياخ لحم مشوية على الفحم" loading="lazy" decoding="async" width="1100" height="825"></span>
            </div>
            <figcaption class="xp-card__cap" data-i18n="xp.capGrill">من الشواية</figcaption>
          </figure>
          <figure class="xp-card xp-card--tall" data-reveal>
            <div class="xp-frame img-frame">
              <span class="xp-zoom"><img data-img="burger" data-img-sizes="(min-width: 1024px) 30vw, 50vw" alt="برجر لحم مع الجبن والخضار" loading="lazy" decoding="async" width="1100" height="825"></span>
            </div>
            <figcaption class="xp-card__cap" data-i18n="xp.capBurger">كلاسيكيات مفضلة</figcaption>
          </figure>
          <figure class="xp-card xp-card--wide" data-reveal>
            <div class="xp-frame img-frame">
              <span class="xp-zoom"><img data-img="pizza" data-img-sizes="(min-width: 1024px) 40vw, 50vw" alt="بيتزا خارجة من الفرن بالجبن الذائب" loading="lazy" decoding="async" width="1100" height="825"></span>
            </div>
            <figcaption class="xp-card__cap" data-i18n="xp.capOven">من الفرن</figcaption>
          </figure>
        </div>
      </div>
    </section>

    <!-- ======================= D — AMBIENCE & HOSPITALITY ======================= -->
    <section class="xp-ambience section" id="xp-ambience" aria-labelledby="xpAmbienceTitle" data-bg="#1a1a1a" data-anchor="xp-ambience">
      <div class="container">
        <header class="section-head section-head--split">
          <div>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="xp.ambEyebrow">الأجواء والضيافة</span></p>
            <h2 class="section-title" id="xpAmbienceTitle" data-reveal-heading data-i18n="xp.ambTitle">مكان مريح للعائلة والأصدقاء</h2>
          </div>
          <div>
            <p class="section-copy" data-i18n="xp.ambCopy">سواء زرتنا أو طلبت عبر واتساب، نسعى أن تكون كل تفاصيل طلبك واضحة وسهلة.</p>
            <p class="notice notice--quiet" data-i18n="gal.note">الصور توضيحية</p>
          </div>
        </header>

        <div class="xp-ambience__grid">
          <figure class="xp-card xp-card--room" data-reveal>
            <div class="xp-frame img-frame" data-xp-parallax>
              <span class="xp-zoom"><img data-img="interior" data-img-sizes="(min-width: 1024px) 60vw, 100vw" alt="صالة مطعم بإضاءة دافئة وطاولات مُعدّة" loading="lazy" decoding="async" width="1600" height="1067"></span>
            </div>
            <figcaption class="xp-card__cap" data-i18n="xp.capRoom">أجواء الصالة</figcaption>
          </figure>
          <figure class="xp-card xp-card--sign" data-reveal>
            <div class="xp-frame img-frame">
              <span class="xp-zoom"><img data-img="logoSign" data-img-sizes="(min-width: 1024px) 30vw, 100vw" alt="لافتة مطعم نجا المضيئة: شعار الديك وعبارة Feel Fresh" loading="lazy" decoding="async" width="593" height="629"></span>
            </div>
            <figcaption class="xp-card__cap" data-i18n="xp.capSign">لافتة نجا المضيئة</figcaption>
          </figure>
        </div>
      </div>
    </section>

    <!-- ======================= E — SIGNATURE EXPERIENCE HIGHLIGHTS ======================= -->
    <section class="xp-highlights section" id="xp-highlights" aria-labelledby="xpHighlightsTitle" data-bg="#121212" data-anchor="xp-highlights">
      <div class="container">
        <header class="section-head section-head--center">
          <p class="eyebrow eyebrow--center"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="xp.hlEyebrow">ما يميّز تجربتك</span><span class="eyebrow__line" aria-hidden="true"></span></p>
          <h2 class="section-title section-title--center" id="xpHighlightsTitle" data-reveal-heading data-i18n="xp.hlTitle">تجربة تُحب العودة إليها</h2>
        </header>

        <ul class="xp-features">
          <li class="xp-feature" data-reveal>
            <svg class="xp-feature__icon" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M10 26h28M14 26c0 7 4 11 10 11s10-4 10-11M24 12v6M17 14l2 5M31 14l-2 5"/></svg>
            <h3 data-i18n="xp.f1Title">لحظات لا تُنسى</h3>
            <p data-i18n="xp.f1Text">وجبة تُشارَك مع من تحب.</p>
          </li>
          <li class="xp-feature" data-reveal>
            <svg class="xp-feature__icon" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><circle cx="24" cy="26" r="11"/><circle cx="24" cy="26" r="6"/><path d="M24 8v4M12 12l3 3M36 12l-3 3"/></svg>
            <h3 data-i18n="xp.f2Title">تقديم جميل</h3>
            <p data-i18n="xp.f2Text">نهتم بأن يبدو كل طبق جميلاً على المائدة.</p>
          </li>
          <li class="xp-feature" data-reveal>
            <svg class="xp-feature__icon" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M24 38S9 29 9 19a8 8 0 0 1 15-3.5A8 8 0 0 1 39 19c0 10-15 19-15 19Z"/></svg>
            <h3 data-i18n="xp.f3Title">ضيافة دافئة</h3>
            <p data-i18n="xp.f3Text">ترحيب في الزيارة وفي الطلب عبر واتساب.</p>
          </li>
          <li class="xp-feature" data-reveal>
            <svg class="xp-feature__icon" viewBox="0 0 48 48" aria-hidden="true" focusable="false"><path d="M8 40h32M12 40V20l12-9 12 9v20M20 40V28h8v12"/></svg>
            <h3 data-i18n="xp.f4Title">أجواء مريحة</h3>
            <p data-i18n="xp.f4Text">مكان مريح للعائلة والأصدقاء.</p>
          </li>
        </ul>
      </div>
    </section>

    <!-- ======================= F — CLOSING CALL TO ACTION ======================= -->
    <section class="banner xp-cta" id="xp-cta" aria-labelledby="xpCtaTitle" data-bg="#0b0b0b" data-anchor="xp-cta">
      <div class="banner__media img-frame">
        <img data-img="grill" data-img-sizes="100vw" alt="أسياخ لحم مشوية على الفحم" loading="lazy" decoding="async" width="1100" height="825">
      </div>
      <div class="banner__shade" aria-hidden="true"></div>
      <div class="container banner__inner">
        <p class="eyebrow eyebrow--center"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="xp.ctaEyebrow">ابدأ تجربتك</span><span class="eyebrow__line" aria-hidden="true"></span></p>
        <h2 class="banner__quote text-gradient" id="xpCtaTitle" data-reveal-heading data-i18n="xp.ctaTitle">اكتشف قائمة الطعام</h2>
        <p class="banner__alt" data-i18n-alt="xp.ctaTitle" lang="en" dir="ltr">Explore Our Menu</p>
        <p class="section-copy section-copy--center" data-reveal data-i18n="xp.ctaCopy">تصفّح القائمة، ثم أرسل طلبك عبر واتساب.</p>
        <div class="xp-cta__buttons" data-reveal>
          <a class="btn btn--brand btn--lg" <?php echo naja_menu_link(); ?>>
            <span data-i18n="xp.ctaTitle">اكتشف قائمة الطعام</span>
            <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
          </a>
          <a class="btn btn--ghost btn--lg" data-wa="general" data-hide-missing hidden>
            <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
            <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
          </a>
        </div>
      </div>
    </section>
  </main>

<?php
get_footer();
