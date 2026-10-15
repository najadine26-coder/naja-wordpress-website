<?php
/**
 * Homepage (always used for the site's front page).
 *
 * @package Naja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
  <main id="main">
    <!-- ======================= 01 — CINEMATIC HERO ======================= -->
    <section class="hero" id="home" aria-labelledby="heroTitle" data-bg="#121212">
      <div class="hero__media img-frame">
        <?php naja_hero_img( 'hero' ); ?>
      </div>
      <div class="hero__shade" aria-hidden="true"></div>
      <div class="aurora aurora--hero" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="hero__spot" aria-hidden="true"></div>
      <div class="hero__ghost" aria-hidden="true"><span>Naja</span></div>

      <div class="container hero__inner">
        <div class="hero__content">
          <p class="eyebrow hero__eyebrow" data-hero-item>
            <span class="eyebrow__line" aria-hidden="true"></span>
            <span data-i18n="hero.eyebrow">الدمام، المملكة العربية السعودية</span>
          </p>
          <h1 class="hero__title text-gradient-hero" id="heroTitle" data-hero-item data-i18n="hero.title">اكتشف وجبتك المفضلة</h1>
          <p class="hero__alt" data-hero-item data-i18n-alt="hero.title" lang="en" dir="ltr">Discover Your Next Favorite Meal</p>
          <p class="tagline" data-hero-item lang="en">
            <span class="tagline__text">Feel Fresh</span>
            <svg class="tagline__swoosh" aria-hidden="true"><use href="#i-swoosh"/></svg>
          </p>
          <p class="hero__copy" data-hero-item data-i18n="hero.copy">قائمة متنوعة تجمع المشويات والأرز والوجبات السريعة والحلويات — في مكان واحد في قلب الدمام.</p>
          <div class="hero__ctas" data-hero-item>
            <a class="btn btn--brand btn--lg" data-wa="general">
              <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
              <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
            </a>
            <a class="btn btn--ghost btn--lg" href="#menu">
              <span data-i18n="cta.explore">استكشف قائمتنا</span>
              <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
          </div>
        </div>

        <figure class="hero__frame" aria-hidden="true">
          <div class="hero__frame-img img-frame">
            <img data-img="logoSign" data-img-decorative alt="" loading="eager" decoding="async" width="593" height="629">
          </div>
          <figcaption class="hero__frame-cap">Naja · Feel Fresh</figcaption>
        </figure>
      </div>

      <a class="hero__scroll" href="#signature">
        <span data-i18n="hero.scroll">مرّر للأسفل</span>
        <span class="hero__scroll-line" aria-hidden="true"></span>
      </a>
    </section>

    <!-- Animated category marquee (decorative) -->
    <div class="marquee" aria-hidden="true" data-marquee>
      <div class="marquee__track" id="marqueeTop"></div>
    </div>

    <!-- ======================= 02 — SIGNATURE DISHES ======================= -->
    <section class="signature" id="signature" aria-labelledby="sigTitle" data-bg="#141414">
      <div class="signature__pin">
        <div class="signature__track" id="signatureTrack">
          <header class="signature__intro section-head">
            <p class="section-num" aria-hidden="true">02 <span>/ 08</span></p>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="sig.eyebrow">الأطباق المميزة</span></p>
            <h2 class="section-title" id="sigTitle" data-reveal-heading data-i18n="sig.title">مختاراتنا المميزة</h2>
            <p class="section-copy" data-i18n="sig.copy">أطباق تمثّل روح قائمتنا المتنوعة.</p>
            <p class="signature__hint" aria-hidden="true">
              <span data-i18n="sig.hint">مرّر لاستكشاف الأطباق</span>
              <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
            </p>
          </header>
          <!-- Dish cards are rendered from js/menu-data.js (najaSignatureDishes) -->
        </div>
        <div class="signature__progress" aria-hidden="true"><span id="signatureProgress"></span></div>
      </div>
    </section>

    <!-- ======================= 03 — BROWSE BY CATEGORY ======================= -->
    <section class="cats section" id="categories" aria-labelledby="catsTitle" data-bg="#140f0f">
      <div class="aurora aurora--soft" aria-hidden="true"><span></span><span></span></div>
      <div class="container">
        <header class="section-head section-head--split">
          <div>
            <p class="section-num" aria-hidden="true">03 <span>/ 08</span></p>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="cats.eyebrow">تصفّح حسب الذوق</span></p>
            <h2 class="section-title" id="catsTitle" data-reveal-heading data-i18n="cats.title">لكل ذوقٍ قسم</h2>
          </div>
          <p class="section-copy" data-i18n="cats.copy">اختر قسماً لعرض أطباقه مباشرة في القائمة.</p>
        </header>
        <ol class="cat-list" id="catList"><!-- rendered from najaMenuCategories --></ol>
      </div>
      <div class="cat-preview img-frame" id="catPreview" aria-hidden="true"><img alt="" decoding="async"></div>
    </section>

    <!-- ======================= 04 — EXPLORE THE MENU ======================= -->
    <section class="menu section" id="menu" aria-labelledby="menuTitle" data-bg="#121212">
      <div class="container">
        <header class="section-head section-head--split">
          <div>
            <p class="section-num" aria-hidden="true">04 <span>/ 08</span></p>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="menu.eyebrow">القائمة</span></p>
            <h2 class="section-title" id="menuTitle" data-reveal-heading data-i18n="menu.title">استكشف القائمة</h2>
          </div>
          <div>
            <p class="section-copy" data-i18n="menu.copy">تصفّح الأقسام أو ابحث عن طبقك، ثم اطلبه مباشرة عبر واتساب.</p>
            <p class="notice" data-i18n="menu.notice">قائمة تجريبية — ستُحدَّث الأصناف والأسعار بعد اعتمادها من المطعم.</p>
          </div>
        </header>

        <div class="menu__controls" data-reveal>
          <div class="menu__search">
            <label class="visually-hidden" for="menuSearch" data-i18n="menu.searchLabel">ابحث في القائمة</label>
            <svg class="icon menu__search-icon" aria-hidden="true"><use href="#i-search"/></svg>
            <input type="search" id="menuSearch" autocomplete="off" enterkeyhint="search"
                   data-i18n-attr="placeholder:menu.searchPlaceholder" placeholder="ابحث عن طبق…">
            <button type="button" class="menu__search-clear" id="menuSearchClear" hidden data-i18n-attr="aria-label:menu.clear" aria-label="مسح البحث">
              <svg class="icon" aria-hidden="true"><use href="#i-close"/></svg>
            </button>
          </div>
          <div class="menu__cats" id="menuCats" role="group" data-i18n-attr="aria-label:menu.categories" aria-label="أقسام القائمة">
            <!-- rendered from najaMenuCategories -->
          </div>
        </div>

        <p class="menu__count" id="menuCount" role="status" aria-live="polite"></p>

        <ul class="menu-grid" id="menuGrid">
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

    <!-- ======================= 04 — THE NAJA EXPERIENCE ======================= -->
    <section class="experience section" id="experience" aria-labelledby="expTitle" data-bg="#1a1a1a">
      <div class="container experience__grid">
        <div class="experience__media">
          <div class="experience__reveal img-frame">
            <img data-img="interior" data-img-sizes="(min-width: 1024px) 55vw, 100vw" alt="صالة مطعم بإضاءة دافئة وطاولات مُعدّة" loading="lazy" decoding="async" width="1600" height="1067">
          </div>
          <span class="experience__outline" aria-hidden="true"></span>
        </div>

        <div class="experience__content">
          <p class="section-num" aria-hidden="true">05 <span>/ 08</span></p>
          <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="exp.eyebrow">تجربة نجا</span></p>
          <h2 class="section-title section-title--xl text-gradient" id="expTitle" data-reveal-heading data-i18n="exp.title">أكثر من مجرد وجبة، إنها تجربة</h2>
          <p class="experience__alt" data-i18n-alt="exp.title" lang="en" dir="ltr">More Than a Meal, An Experience</p>
          <span class="accent-rule" aria-hidden="true"></span>
          <p class="section-copy" data-reveal data-i18n="exp.copy">في نجا نؤمن بأن الوجبة الجيدة تبدأ من المائدة وتمتد إلى اللحظات التي تُشاركها. قائمة متنوعة تناسب كل الأذواق، في أجواء مريحة تجمع العائلة والأصدقاء.</p>
          <p class="section-copy section-copy--muted" data-reveal data-i18n="exp.copy2">سواء زرتنا أو طلبت عبر واتساب، نسعى أن تكون كل تفاصيل طلبك واضحة وسهلة.</p>

          <ul class="highlights">
            <li class="highlight" data-reveal>
              <span class="highlight__num" aria-hidden="true">I</span>
              <div><h3 data-i18n="exp.h1Title">قائمة لكل الأذواق</h3><p data-i18n="exp.h1Text">من المشويات والأرز إلى الباستا والحلويات.</p></div>
            </li>
            <li class="highlight" data-reveal>
              <span class="highlight__num" aria-hidden="true">II</span>
              <div><h3 data-i18n="exp.h2Title">طلب سهل عبر واتساب</h3><p data-i18n="exp.h2Text">اختر طبقك وأرسل طلبك برسالة واحدة.</p></div>
            </li>
            <li class="highlight" data-reveal>
              <span class="highlight__num" aria-hidden="true">III</span>
              <div><h3 data-i18n="exp.h3Title">في الدمام</h3><p data-i18n="exp.h3Text">وجهتك القريبة لوجبة تستمتع بها.</p></div>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Cinematic interlude -->
    <section class="banner" aria-labelledby="bannerQuote" data-bg="#0b0b0b">
      <div class="banner__media img-frame">
        <img data-img="grill" data-img-sizes="100vw" alt="أسياخ لحم مشوية على الفحم" loading="lazy" decoding="async" width="1600" height="1067">
      </div>
      <div class="banner__shade" aria-hidden="true"></div>
      <div class="container banner__inner">
        <span class="banner__mark" aria-hidden="true">“</span>
        <p class="banner__quote text-gradient" id="bannerQuote" data-reveal-heading data-i18n="banner.quote">لكل ذوقٍ حكاية، ولكل طبقٍ لحظة.</p>
        <p class="banner__alt" data-i18n-alt="banner.quote" lang="en" dir="ltr">Every taste has a story, every dish a moment.</p>
        <a class="btn btn--ghost btn--lg" href="#menu" data-reveal>
          <span data-i18n="cta.explore">استكشف قائمتنا</span>
          <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
        </a>
      </div>
    </section>

    <!-- ======================= 06 — FOOD GALLERY ======================= -->
    <section class="gallery section" id="gallery" aria-labelledby="galTitle" data-bg="#121212">
      <div class="container">
        <header class="section-head section-head--split">
          <div>
            <p class="section-num" aria-hidden="true">06 <span>/ 08</span></p>
            <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="gal.eyebrow">المعرض</span></p>
            <h2 class="section-title" id="galTitle" data-reveal-heading data-i18n="gal.title">معرض الأطباق</h2>
          </div>
          <div>
            <p class="section-copy" data-i18n="gal.copy">لمحات من المائدة. اضغط على أي صورة لتكبيرها.</p>
            <p class="notice notice--quiet" data-i18n="gal.note">الصور توضيحية</p>
          </div>
        </header>
        <ul class="gallery-grid" id="galleryGrid">
          <!-- rendered from najaGallery -->
        </ul>
      </div>
    </section>

    <!-- ======================= 07 — HOW TO ORDER ======================= -->
    <section class="steps section" id="how-to-order" aria-labelledby="stepsTitle" data-bg="#151010">
      <div class="aurora aurora--soft aurora--alt" aria-hidden="true"><span></span><span></span></div>
      <div class="container">
        <header class="section-head section-head--center">
          <p class="section-num" aria-hidden="true">07 <span>/ 08</span></p>
          <p class="eyebrow eyebrow--center"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="steps.eyebrow">كيف تطلب</span><span class="eyebrow__line" aria-hidden="true"></span></p>
          <h2 class="section-title section-title--center" id="stepsTitle" data-reveal-heading data-i18n="steps.title">ثلاث خطوات إلى مائدتك</h2>
        </header>
        <ol class="steps__list">
          <li class="steps__line" aria-hidden="true"><span id="stepsLine"></span></li>
          <li class="step" data-step>
            <span class="step__num" aria-hidden="true">01</span>
            <h3 data-i18n="steps.s1Title">اختر طبقك</h3>
            <p data-i18n="steps.s1Text">تصفّح القائمة واختر ما يناسب ذوقك والكمية.</p>
          </li>
          <li class="step" data-step>
            <span class="step__num" aria-hidden="true">02</span>
            <h3 data-i18n="steps.s2Title">أرسل طلبك عبر واتساب</h3>
            <p data-i18n="steps.s2Text">رسالة جاهزة بتفاصيل طلبك بضغطة واحدة.</p>
          </li>
          <li class="step" data-step>
            <span class="step__num" aria-hidden="true">03</span>
            <h3 data-i18n="steps.s3Title">أكّد مع المطعم</h3>
            <p data-i18n="steps.s3Text">يؤكد المطعم توفر الطلب والمبلغ الإجمالي النهائي.</p>
          </li>
        </ol>
        <div class="steps__cta" data-reveal>
          <a class="btn btn--brand btn--lg" data-wa="general">
            <svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg>
            <span data-i18n="cta.whatsapp">اطلب عبر واتساب</span>
          </a>
        </div>
      </div>
    </section>

    <!-- ======================= ABOUT NAJA (SEO content + FAQ) ======================= -->
    <section class="about-naja section" id="about-naja" aria-labelledby="aboutTitle" data-bg="#121212">
      <div class="container about-naja__grid">
        <div class="about-naja__text">
          <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="seo.eyebrow">عن مطعم نجا</span></p>
          <h2 class="section-title" id="aboutTitle" data-reveal-heading data-i18n="seo.title">مطعم نجا في الدمام: قائمة متنوعة لكل الأذواق</h2>
          <p class="section-copy" data-i18n="seo.p1">مطعم نجا في الدمام بالمملكة العربية السعودية يقدّم قائمة متنوعة تجمع المشويات والأطباق الرئيسية والأرز والبرجر والوجبات السريعة والباستا، إلى جانب المشروبات والحلويات. سواء كنت تبحث عن وجبة عائلية أو غداء سريع، ستجد في قائمتنا ما يناسب ذوقك.</p>
          <p class="section-copy" data-i18n="seo.p2">تصفّح قائمة الطعام كاملة بالعربية أو الإنجليزية، وابحث عن طبقك المفضل، ثم أرسل طلبك مباشرة عبر واتساب برسالة جاهزة بتفاصيل الطلب. يؤكد المطعم توفر الطلب والمبلغ الإجمالي النهائي قبل التحضير.</p>
          <p class="section-copy" data-i18n="seo.p3">شعارنا «Feel Fresh» يلخّص ما نسعى إليه: وجبة تستمتع بها، وتجربة طلب سهلة وواضحة من أول خطوة إلى آخرها.</p>
          <a class="btn btn--ghost btn--lg about-naja__cta" <?php echo naja_menu_link(); ?>>
            <span data-i18n="seo.menuCta">تصفّح قائمة الطعام</span>
            <svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg>
          </a>
        </div>
        <div class="about-naja__faq" data-reveal>
          <h3 class="faq__title" id="faqTitle" data-i18n="faq.title">أسئلة شائعة</h3>
          <div class="faq" id="faqList">
            <details class="faq__item" open>
              <summary class="faq__q"><span>أين يقع مطعم نجا؟</span><span class="faq__icon" aria-hidden="true"></span></summary>
              <div class="faq__a"><p>يقع مطعم نجا في مدينة الدمام بالمملكة العربية السعودية. سنضيف العنوان التفصيلي وموقع المطعم على الخريطة قريباً.</p></div>
            </details>
            <details class="faq__item">
              <summary class="faq__q"><span>كيف أطلب من مطعم نجا؟</span><span class="faq__icon" aria-hidden="true"></span></summary>
              <div class="faq__a"><p>اختر طبقك من القائمة واضغط «اطلب عبر واتساب»، أو تواصل معنا مباشرة على الرقم <span dir="ltr"><?php echo esc_html( naja_contact_number() ); ?></span> عبر واتساب أو اتصال هاتفي.</p></div>
            </details>
            <details class="faq__item">
              <summary class="faq__q"><span>ماذا تقدّم قائمة مطعم نجا؟</span><span class="faq__icon" aria-hidden="true"></span></summary>
              <div class="faq__a"><p>قائمة متنوعة تشمل الأطباق الرئيسية والأرز والمشويات والبرجر والوجبات السريعة والباستا والمشروبات والحلويات.</p></div>
            </details>
            <details class="faq__item">
              <summary class="faq__q"><span>هل القائمة متوفرة باللغة الإنجليزية؟</span><span class="faq__icon" aria-hidden="true"></span></summary>
              <div class="faq__a"><p>نعم، يمكنك التبديل بين العربية والإنجليزية في أي وقت من زر اللغة أعلى الصفحة، وتعمل القائمة والبحث والطلب باللغتين.</p></div>
            </details>
          </div>
        </div>
      </div>
    </section>

    <div class="marquee marquee--reverse marquee--outline" aria-hidden="true" data-marquee>
      <div class="marquee__track" id="marqueeBottom"></div>
    </div>

    <!-- ======================= 08 — VISIT & ORDER ======================= -->
    <section class="visit section" id="visit" aria-labelledby="visitTitle" data-bg="#0d0d0d">
      <div class="visit__glow" aria-hidden="true"></div>
      <div class="aurora aurora--visit" aria-hidden="true"><span></span><span></span><span></span></div>
      <div class="container">
        <div class="visit__head">
          <p class="section-num" aria-hidden="true">08 <span>/ 08</span></p>
          <figure class="visit__mark">
            <span class="visit__mark-glow" aria-hidden="true"></span>
            <span class="visit__mark-frame img-frame">
              <img data-img="logoSign" alt="" loading="lazy" decoding="async" width="593" height="629">
            </span>
            <figcaption class="tagline tagline--center" lang="en">
              <span class="tagline__text">Feel Fresh</span>
              <svg class="tagline__swoosh" aria-hidden="true"><use href="#i-swoosh"/></svg>
            </figcaption>
          </figure>
          <p class="eyebrow eyebrow--center"><span class="eyebrow__line" aria-hidden="true"></span><span data-i18n="visit.eyebrow">زُرنا واطلب</span><span class="eyebrow__line" aria-hidden="true"></span></p>
          <h2 class="section-title section-title--center text-gradient" id="visitTitle" data-reveal-heading data-i18n="visit.title">جاهز لوجبتك القادمة؟</h2>
          <p class="visit__alt" data-i18n-alt="visit.title" lang="en" dir="ltr">Ready for Your Next Meal?</p>
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

        <dl class="visit__info" data-reveal>
          <div class="visit__cell">
            <dt data-i18n="visit.address">العنوان</dt>
            <dd id="infoAddress"></dd>
          </div>
          <div class="visit__cell">
            <dt data-i18n="visit.hours">ساعات العمل</dt>
            <dd id="infoHours"></dd>
          </div>
          <div class="visit__cell">
            <dt data-i18n="visit.phone">الهاتف</dt>
            <dd id="infoPhone"></dd>
          </div>
          <div class="visit__cell">
            <dt data-i18n="visit.whatsapp">واتساب</dt>
            <dd id="infoWhatsapp"></dd>
          </div>
        </dl>
      </div>
    </section>
  </main>

<?php
get_footer();
