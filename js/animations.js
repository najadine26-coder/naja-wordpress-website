/* =========================================================================
   NAJA RESTAURANT — CINEMATIC SCROLL ANIMATIONS (GSAP + ScrollTrigger)
   -------------------------------------------------------------------------
   Progressive enhancement: every element is fully visible by default.
   If GSAP fails to load, or the visitor prefers reduced motion, the page
   simply renders statically. Only Section 02 is pinned (desktop only).
   ========================================================================= */
(function () {
  "use strict";

  var noop = function () {};
  if (!window.gsap || !window.ScrollTrigger) {
    document.documentElement.classList.add("no-gsap");
    window.NajaAnimations = { init: noop, teardown: noop, refresh: noop };
    return;
  }

  var gsap = window.gsap;
  var ScrollTrigger = window.ScrollTrigger;
  gsap.registerPlugin(ScrollTrigger);
  ScrollTrigger.config({ ignoreMobileResize: true });

  var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
  var $$ = function (sel, ctx) { return gsap.utils.toArray((ctx || document).querySelectorAll(sel)); };
  var rtl = function () { return document.documentElement.dir === "rtl"; };
  // Pages share this file, so only animate elements that exist on the current one
  var has = function (sel) { return !!document.querySelector(sel); };

  var mm = null;
  var introPlayed = false;

  /* ---------- Hero entrance (once per page load, short and non-blocking) ---------- */
  function heroIntro() {
    if (introPlayed) return;
    introPlayed = true;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    var tl = gsap.timeline({ defaults: { ease: "power3.out" } });
    if (has(".hero__img")) tl.from(".hero__img", { scale: 1.16, duration: 2.4, ease: "power2.out", clearProps: "transform" }, 0);
    if (has("[data-hero-item]")) tl.from("[data-hero-item]", { y: 36, autoAlpha: 0, duration: 1, stagger: 0.08, clearProps: "transform,opacity,visibility" }, 0.15);
    if (has(".hero__ghost span")) tl.from(".hero__ghost span", { yPercent: 25, autoAlpha: 0, duration: 1.6, clearProps: "all" }, 0.1);
    if (has(".hero__frame-img")) {
      tl.from(".hero__frame-img, .hero__frame-cap", {
        autoAlpha: 0, rotateY: rtl() ? 22 : -22, z: -160, y: 40,
        transformPerspective: 1200, duration: 1.5, stagger: 0.1, clearProps: "all"
      }, 0.35);
    }
    tl.from(".site-header__bar > *", { y: -14, autoAlpha: 0, duration: 0.8, stagger: 0.06, clearProps: "all" }, 0.2);
  }

  function restoreSplits(list) {
    list.forEach(function (h) {
      if (h.dataset.splitOriginal != null) { h.textContent = h.dataset.splitOriginal; delete h.dataset.splitOriginal; }
    });
  }

  /* ---------- Build all scroll-linked animations ---------- */
  function build() {
    mm = gsap.matchMedia();

    mm.add({
      desktop: "(min-width: 1024px)",
      mobile: "(max-width: 1023px)",
      motion: "(prefers-reduced-motion: no-preference)"
    }, function (ctx) {
      var c = ctx.conditions;
      if (!c.motion) return; // reduced motion: static layout, nothing pinned

      var dir = rtl() ? -1 : 1;

      /* --- Section background transitions --- */
      $$("main section[data-bg]").forEach(function (sec) {
        ScrollTrigger.create({
          trigger: sec, start: "top 55%", end: "bottom 55%",
          onToggle: function (self) {
            if (self.isActive) gsap.to(document.body, { backgroundColor: sec.dataset.bg, duration: 0.9, ease: "power1.out", overwrite: "auto" });
          }
        });
      });

      /* --- 01 HERO: layered parallax depth (pages without a hero skip it) --- */
      if (has(".hero")) {
        var heroTl = gsap.timeline({
          scrollTrigger: { trigger: ".hero", start: "top top", end: "bottom top", scrub: true }
        });
        heroTl
          .to(".hero__media", { yPercent: c.desktop ? 14 : 8, scale: 1.08, ease: "none" }, 0)
          .to(".hero__content", { y: c.desktop ? -120 : -60, autoAlpha: 0.1, ease: "none" }, 0);
        if (has(".hero__ghost")) heroTl.to(".hero__ghost", { yPercent: -35, ease: "none" }, 0);
        if (has(".hero__scroll")) heroTl.to(".hero__scroll", { autoAlpha: 0, ease: "none", duration: 0.25 }, 0);
        if (c.desktop && has(".hero__frame")) {
          heroTl.to(".hero__frame", {
            y: -200, rotateY: -10 * dir, rotateX: 6, z: 80,
            transformPerspective: 1200, ease: "none"
          }, 0);
        }
      }

      /* --- Section headings: word-by-word cinematic reveal (blur → sharp).
             Splitting is by whole words, so Arabic letter joining stays intact.
             Gradient-text headings animate as one block (background-clip needs it). --- */
      var splitHeadings = [];
      $$("[data-reveal-heading]").forEach(function (h) {
        var gradient = h.classList.contains("text-gradient");
        if (!gradient && h.children.length === 0) {
          var original = h.textContent;
          h.dataset.splitOriginal = original;
          h.innerHTML = original.trim().split(/\s+/).map(function (w) {
            return '<span class="split-word">' + w.replace(/&/g, "&amp;").replace(/</g, "&lt;") + "</span>";
          }).join(" ");
          splitHeadings.push(h);
          gsap.from($$(".split-word", h), {
            yPercent: 70, autoAlpha: 0, rotateX: -50, filter: "blur(10px)", transformPerspective: 800, transformOrigin: "50% 100%",
            duration: 1.1, stagger: 0.07, ease: "power3.out", clearProps: "filter",
            scrollTrigger: { trigger: h, start: "top 88%", once: true }
          });
        } else {
          gsap.from(h, {
            y: 50, autoAlpha: 0, filter: "blur(12px)", scale: 0.96,
            duration: 1.4, ease: "power3.out", clearProps: "filter",
            scrollTrigger: { trigger: h, start: "top 88%", once: true }
          });
        }
      });

      /* --- Marquees: lean into the scroll velocity --- */
      var groups = $$(".marquee__group");
      if (groups.length) {
        var skewTo = gsap.quickTo(groups, "skewX", { duration: 0.5, ease: "power3" });
        ScrollTrigger.create({
          start: 0, end: "max",
          onUpdate: function (self) { skewTo(gsap.utils.clamp(-10, 10, self.getVelocity() / -250)); }
        });
      }

      /* --- 03 Categories: rows slide in from the reading edge --- */
      $$("[data-cat-row]").forEach(function (row, i) {
        gsap.from(row, {
          x: -60 * dir, autoAlpha: 0, duration: 1, delay: (i % 4) * 0.06, ease: "power3.out",
          scrollTrigger: { trigger: row, start: "top 92%", once: true }
        });
      });

      /* --- Cinematic banner: slow zoom + parallax --- */
      if ($(".banner")) {
        gsap.fromTo(".banner__media", { yPercent: -8, scale: 1.25 }, {
          yPercent: 8, scale: 1.02, ease: "none",
          scrollTrigger: { trigger: ".banner", start: "top bottom", end: "bottom top", scrub: true }
        });
        gsap.fromTo(".banner__inner", { y: 80 }, {
          y: -60, ease: "none",
          scrollTrigger: { trigger: ".banner", start: "top bottom", end: "bottom top", scrub: true }
        });
        gsap.from(".banner__mark", {
          scale: 0.4, autoAlpha: 0, rotate: -20 * dir, duration: 1.4, ease: "back.out(1.6)",
          scrollTrigger: { trigger: ".banner", start: "top 70%", once: true }
        });
      }

      /* --- 07 How to order: the timeline draws itself, steps rise in 3D --- */
      var line = $("#stepsLine");
      if (line) {
        gsap.fromTo(line, c.desktop ? { scaleX: 0 } : { scaleY: 0 }, {
          scaleX: 1, scaleY: 1, ease: "none",
          scrollTrigger: { trigger: ".steps__list", start: "top 80%", end: "bottom 60%", scrub: true }
        });
      }
      $$("[data-step]").forEach(function (step, i) {
        gsap.from(step, {
          y: 70, autoAlpha: 0, rotateX: 14, transformPerspective: 1000, transformOrigin: "50% 100%",
          duration: 1.1, delay: c.desktop ? i * 0.15 : 0, ease: "power3.out",
          scrollTrigger: { trigger: c.desktop ? ".steps__list" : step, start: "top 85%", once: true }
        });
      });

      /* --- 08 Visit: the wordmark opens up as it arrives --- */
      if ($(".visit__mark")) {
        gsap.fromTo(".visit__mark", { scale: 0.82, autoAlpha: 0.2, letterSpacing: "-0.02em" }, {
          scale: 1, autoAlpha: 1, letterSpacing: "0em", ease: "none",
          scrollTrigger: { trigger: ".visit", start: "top 85%", end: "top 30%", scrub: true }
        });
      }

      /* --- Generic reveals --- */
      $$("[data-reveal]").forEach(function (el) {
        gsap.from(el, {
          y: 40, autoAlpha: 0, duration: 1, ease: "power3.out",
          scrollTrigger: { trigger: el, start: "top 90%", once: true }
        });
      });

      /* --- 02 SIGNATURE: pinned horizontal showcase on desktop --- */
      var section = $(".signature");
      var track = $("#signatureTrack");
      var cards = track ? $$(".dish-card", track) : []; // without a track, $$ would match every card on the page

      if (c.desktop && section && track && cards.length) {
        section.classList.add("is-horizontal");
        var distance = function () { return Math.max(0, track.scrollWidth - window.innerWidth); };
        var progress = $("#signatureProgress");

        var horizontal = gsap.to(track, {
          x: function () { return -dir * distance(); },
          ease: "none",
          scrollTrigger: {
            trigger: section,
            pin: true,
            start: "top top",
            end: function () { return "+=" + distance(); },
            scrub: 0.7,
            anticipatePin: 1,
            invalidateOnRefresh: true,
            refreshPriority: 1, // created after other triggers but sits above them on the page
            onUpdate: function (self) { if (progress) gsap.set(progress, { scaleX: self.progress }); }
          }
        });

        // Cards turn towards the viewer as they travel in
        cards.forEach(function (card) {
          gsap.fromTo(card,
            { rotateY: 18 * dir, z: -100, autoAlpha: 0.5, transformPerspective: 1400, transformOrigin: rtl() ? "100% 50%" : "0% 50%" },
            {
              rotateY: 0, z: 0, autoAlpha: 1, ease: "power1.out",
              scrollTrigger: {
                trigger: card, containerAnimation: horizontal,
                // from first appearance at the leading edge until fully on screen
                start: rtl() ? "right left" : "left right",
                end: rtl() ? "left left" : "right right",
                scrub: true
              }
            });
        });

        return function () {
          section.classList.remove("is-horizontal");
          restoreSplits(splitHeadings);
        };
      }

      /* Mobile / tablet: simple, non-hijacking reveal */
      cards.forEach(function (card, i) {
        gsap.from(card, {
          y: 60, autoAlpha: 0, rotateX: 10, transformPerspective: 900, transformOrigin: "50% 100%",
          duration: 1, delay: (i % 2) * 0.08, ease: "power3.out",
          scrollTrigger: { trigger: card, start: "top 92%", once: true }
        });
      });
      return function () { restoreSplits(splitHeadings); };
    });

    /* --- 04 EXPERIENCE + 05 GALLERY (separate context keeps the early return above simple) --- */
    mm.add({
      desktop: "(min-width: 1024px)",
      motion: "(prefers-reduced-motion: no-preference)"
    }, function (ctx) {
      var c = ctx.conditions;
      if (!c.motion) return;

      var reveal = $(".experience__reveal");
      if (reveal) {
        gsap.fromTo(reveal,
          { clipPath: "inset(16% 12% 16% 12%)" },
          {
            clipPath: "inset(0% 0% 0% 0%)", ease: "none",
            scrollTrigger: { trigger: ".experience__media", start: "top 88%", end: "center 55%", scrub: true }
          });
        gsap.fromTo(".experience__reveal img",
          { yPercent: -7, scale: 1.18 },
          {
            yPercent: 7, scale: 1, ease: "none",
            scrollTrigger: { trigger: ".experience", start: "top bottom", end: "bottom top", scrub: true }
          });
      }
      if (c.desktop && reveal) {
        gsap.fromTo(".experience__outline", { y: 50, x: 0 }, {
          y: -50, ease: "none",
          scrollTrigger: { trigger: ".experience", start: "top bottom", end: "bottom top", scrub: true }
        });
        gsap.fromTo(".experience__content", { y: 70 }, {
          y: -30, ease: "none",
          scrollTrigger: { trigger: ".experience", start: "top bottom", end: "bottom top", scrub: true }
        });
      }

      /* Gallery: depth reveal + varied parallax */
      var items = $$("[data-gallery-item]");
      ScrollTrigger.batch(items, {
        start: "top 92%",
        once: true,
        onEnter: function (batch) {
          gsap.fromTo(batch,
            { y: 70, autoAlpha: 0, rotateX: 8, transformPerspective: 1000, transformOrigin: "50% 100%" },
            { y: 0, autoAlpha: 1, rotateX: 0, duration: 1.1, stagger: 0.09, ease: "power3.out", overwrite: true });
        }
      });
      if (c.desktop) {
        items.forEach(function (item, i) {
          var frame = $("[data-parallax]", item);
          if (!frame) return;
          var amount = [6, 4, 7, 3, 5, 4, 6][i % 7];
          gsap.fromTo(frame, { yPercent: -amount }, {
            yPercent: amount, ease: "none",
            scrollTrigger: { trigger: item, start: "top bottom", end: "bottom top", scrub: true }
          });
        });
      }
    });
  }

  /* ---------- Public API (used by main.js on language change / re-render) ---------- */
  var refreshTimer;
  window.NajaAnimations = {
    init: function () {
      build();
      ScrollTrigger.refresh();
    },
    teardown: function () {
      if (mm) { mm.revert(); mm = null; }
      gsap.set(document.body, { clearProps: "backgroundColor" });
    },
    refresh: function () {
      clearTimeout(refreshTimer);
      refreshTimer = setTimeout(function () { ScrollTrigger.refresh(); }, 60);
    }
  };

  heroIntro();
  build();

  // Re-measure once fonts and images have settled.
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { ScrollTrigger.refresh(); });
  window.addEventListener("load", function () { ScrollTrigger.refresh(); });
})();
