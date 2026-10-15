/* =========================================================================
   NAJA RESTAURANT — MAIN (language, rendering, ordering, gallery)
   Works without GSAP. Animations live in js/animations.js.
   ========================================================================= */
(function () {
  "use strict";

  var config = window.restaurantConfig || {};
  var images = window.najaImages || {};
  var page = document.body.dataset.page || "home";
  var galleryHost = document.getElementById("galleryGrid");
  var gallerySource = galleryHost ? galleryHost.dataset.source : "";
  var galleryAll = (gallerySource === "page" && window.najaGalleryPage) || (gallerySource === "menu" && window.najaMenuGallery) || window.najaGallery || [];
  var galleryCats = window.najaGalleryCategories || [];
  var gallery = galleryAll; // on the Gallery page this narrows to the active filter
  var categories = window.najaMenuCategories || [];
  var signature = window.najaSignatureDishes || [];
  var menuItems = window.najaMenuItems || [];
  var dict = window.najaTranslations || { ar: {}, en: {} };

  var root = document.documentElement;
  var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

  var state = {
    lang: "ar",
    category: "all",
    query: "",
    qty: {},          // dishId -> quantity
    galleryCat: "all", // Gallery page filter
    lightboxIndex: -1
  };

  /* ---------------------------------------------------------------------
     Helpers
     --------------------------------------------------------------------- */
  function storageGet(key) { try { return window.localStorage.getItem(key); } catch (e) { return null; } }
  function storageSet(key, val) { try { window.localStorage.setItem(key, val); } catch (e) { /* ignore */ } }

  function other(lang) { return lang === "ar" ? "en" : "ar"; }

  function t(key, vars, lang) {
    var table = dict[lang || state.lang] || {};
    var str = table[key];
    if (str == null) str = (dict.en && dict.en[key]) || key;
    if (vars) {
      Object.keys(vars).forEach(function (k) { str = str.split("{" + k + "}").join(vars[k]); });
    }
    return str;
  }
  function loc(obj, lang) {
    if (obj == null) return "";
    if (typeof obj === "string") return obj;
    return obj[lang || state.lang] || obj.en || obj.ar || "";
  }
  function esc(str) {
    return String(str == null ? "" : str)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
  }
  function digits(str) {
    var d = String(str || "").replace(/[^\d]/g, "");
    if (d.indexOf("00") === 0) d = d.slice(2);
    return d;
  }
  function formatPrice(value, lang) {
    if (value == null || value === "" || isNaN(value)) return "";
    return t("price", { price: Number(value).toLocaleString("en-US") }, lang);
  }
  function pricesVisible() { return config.showSamplePrices !== false; }
  function showPrice(item) { return item.price != null && (!item.placeholder || pricesVisible()); }
  function isRTL() { return state.lang === "ar"; }
  function animations() { return window.NajaAnimations; }

  var refreshTimer;
  function requestLayoutRefresh() {
    clearTimeout(refreshTimer);
    refreshTimer = setTimeout(function () {
      if (animations() && animations().refresh) animations().refresh();
    }, 120);
  }

  /* ---------------------------------------------------------------------
     Toast
     --------------------------------------------------------------------- */
  var toastEl = $("#toast"), toastTimer;
  function toast(message) {
    if (!toastEl) return;
    toastEl.textContent = message;
    toastEl.classList.add("is-visible");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toastEl.classList.remove("is-visible"); }, 4200);
  }

  /* ---------------------------------------------------------------------
     Images (centralised in config.js) + graceful failure
     --------------------------------------------------------------------- */
  function unsplashSrcset(url, maxW) {
    if (!/images\.unsplash\.com/.test(url)) return "";
    var widths = [480, 800, 1200, 1600, 2200].filter(function (w) { return w <= (maxW || 2200); });
    return widths.map(function (w) {
      var u = /([?&])w=\d+/.test(url) ? url.replace(/([?&])w=\d+/, "$1w=" + w) : url + (url.indexOf("?") > -1 ? "&" : "?") + "w=" + w;
      return u + " " + w + "w";
    }).join(", ");
  }
  function sizedUrl(url, w) {
    if (!/images\.unsplash\.com/.test(url)) return url;
    return /([?&])w=\d+/.test(url) ? url.replace(/([?&])w=\d+/, "$1w=" + w) : url;
  }

  function watchImage(img) {
    var frame = img.closest(".img-frame");
    if (!frame || img.dataset.watched) return;
    img.dataset.watched = "1";
    img.addEventListener("error", function () { frame.classList.add("is-failed"); });
    img.addEventListener("load", function () { frame.classList.remove("is-failed"); });
    if (img.complete && img.getAttribute("src") && img.naturalWidth === 0) frame.classList.add("is-failed");
  }

  function setImage(img, key, opts) {
    var data = images[key];
    if (!data) return;
    opts = opts || {};
    watchImage(img);
    var current = img.getAttribute("src");
    if (current !== data.src) {
      img.removeAttribute("srcset");
      img.src = data.src;
    }
    if (!img.hasAttribute("srcset")) {
      var set = unsplashSrcset(data.src, opts.maxW);
      if (set) {
        img.srcset = set;
        img.sizes = img.dataset.imgSizes || opts.sizes || "100vw";
      }
    }
    img.alt = img.hasAttribute("data-img-decorative") ? "" : loc(data.alt);
    if (data.position) img.style.objectPosition = data.position;
  }

  function applyStaticImages() {
    $$("img[data-img]").forEach(function (img) { setImage(img, img.dataset.img); });
  }

  /* ---------------------------------------------------------------------
     WhatsApp / Call / Map actions
     --------------------------------------------------------------------- */
  function findDish(id) {
    var all = signature.concat(menuItems);
    for (var i = 0; i < all.length; i++) if (all[i].id === id) return all[i];
    return null;
  }

  function waMessage(dishId) {
    var name = state.lang === "ar" ? (config.nameAr || config.name) : config.name;
    if (!dishId) return t("wa.general", { name: name });
    var dish = findDish(dishId);
    if (!dish) return t("wa.general", { name: name });
    var qty = state.qty[dishId] || 1;
    var priceLine = showPrice(dish)
      ? t("wa.priceLine", { price: Number(dish.price).toLocaleString("en-US") })
      : "";
    return t("wa.order", { name: name, dish: loc(dish.name), qty: qty, priceLine: priceLine })
      .replace(/\n\n/g, "\n");
  }

  function waUrl(dishId) {
    var num = digits(config.whatsapp);
    if (!num) return "";
    return "https://wa.me/" + num + "?text=" + encodeURIComponent(waMessage(dishId));
  }

  function setAction(el, href, missingKey, external) {
    // Menu page: actions marked data-hide-missing disappear instead of disabling
    if (el.hasAttribute("data-hide-missing")) el.hidden = !href;
    if (href) {
      el.setAttribute("href", href);
      el.removeAttribute("aria-disabled");
      el.removeAttribute("role");
      el.removeAttribute("tabindex");
      el.removeAttribute("title");
      el.removeAttribute("data-missing");
      if (external) { el.target = "_blank"; el.rel = "noopener noreferrer"; }
    } else {
      el.removeAttribute("href");
      el.removeAttribute("target");
      el.setAttribute("role", "button");
      el.setAttribute("tabindex", "0");
      el.setAttribute("aria-disabled", "true");
      el.setAttribute("data-missing", missingKey);
      el.setAttribute("title", t("unavailable"));
    }
  }

  function refreshActions() {
    $$("[data-wa]").forEach(function (el) {
      setAction(el, waUrl(el.dataset.dish || ""), "toast.waMissing", true);
    });
    var phone = digits(config.phone);
    $$('[data-action="call"]').forEach(function (el) {
      setAction(el, phone ? "tel:+" + phone : "", "toast.phoneMissing", false);
    });
    $$('[data-action="map"]').forEach(function (el) {
      setAction(el, config.googleMapsUrl || "", "toast.mapMissing", true);
    });
  }

  // Disabled actions explain themselves instead of failing silently.
  function handleMissing(e) {
    var el = e.target.closest('[aria-disabled="true"][data-missing]');
    if (!el) return false;
    e.preventDefault();
    toast(t(el.getAttribute("data-missing")));
    return true;
  }
  document.addEventListener("click", handleMissing);
  document.addEventListener("keydown", function (e) {
    if ((e.key === "Enter" || e.key === " ") && e.target.matches && e.target.matches('[aria-disabled="true"][data-missing]')) {
      handleMissing(e);
    }
  });

  /* ---------------------------------------------------------------------
     Static translations
     --------------------------------------------------------------------- */
  function applyTranslations() {
    var lang = state.lang;
    root.lang = lang;
    root.dir = lang === "ar" ? "rtl" : "ltr";

    $$("[data-i18n]").forEach(function (el) { el.textContent = t(el.dataset.i18n); });
    $$("[data-i18n-attr]").forEach(function (el) {
      el.dataset.i18nAttr.split(";").forEach(function (pair) {
        var parts = pair.split(":");
        if (parts.length === 2) el.setAttribute(parts[0].trim(), t(parts[1].trim()));
      });
    });
    // Secondary-language echo lines (e.g. the English title under the Arabic one)
    $$("[data-i18n-alt]").forEach(function (el) {
      var alt = other(lang);
      el.textContent = t(el.dataset.i18nAlt, null, alt);
      el.lang = alt;
      el.dir = alt === "ar" ? "rtl" : "ltr";
    });

    // Pages say which title/description keys to translate. A page that sets data-page but no
    // keys (e.g. a WordPress page, or when an SEO plugin owns the tags) keeps its own.
    var body = document.body.dataset;
    var titleKey = body.metaTitle || (body.page ? "" : "meta.title");
    var descKey = body.metaDesc || (body.page ? "" : "meta.description");
    if (titleKey) {
      document.title = t(titleKey);
      var ogTitle = $('meta[property="og:title"]'), twTitle = $('meta[name="twitter:title"]');
      if (ogTitle) ogTitle.setAttribute("content", t(titleKey));
      if (twTitle) twTitle.setAttribute("content", t(titleKey));
    }
    if (descKey) {
      var desc = $('meta[name="description"]'), ogDesc = $('meta[property="og:description"]'), twDesc = $('meta[name="twitter:description"]');
      if (desc) desc.setAttribute("content", t(descKey));
      if (ogDesc) ogDesc.setAttribute("content", t(descKey));
      if (twDesc) twDesc.setAttribute("content", t(descKey));
    }
    var ogLocale = $('meta[property="og:locale"]');
    if (ogLocale) ogLocale.setAttribute("content", lang === "ar" ? "ar_SA" : "en_US");
    var ogAlt = $('meta[property="og:locale:alternate"]');
    if (ogAlt) ogAlt.setAttribute("content", lang === "ar" ? "en_US" : "ar_SA");

    // Links to the other page carry the chosen language (localStorage can be blocked)
    $$("a[data-page-link]").forEach(function (a) {
      var parts = a.dataset.pageLink.split("#");
      var query = lang === (config.defaultLanguage || "ar") ? "" : (parts[0].indexOf("?") > -1 ? "&" : "?") + "lang=" + lang;
      a.setAttribute("href", parts[0] + query + (parts[1] ? "#" + parts[1] : ""));
    });
  }

  /* ---------------------------------------------------------------------
     Section 02 — Signature dishes
     --------------------------------------------------------------------- */
  var track = $("#signatureTrack");

  function renderSignature() {
    if (!track) return;
    $$(".dish-card", track).forEach(function (n) { n.remove(); });
    var alt = other(state.lang);
    var html = signature.map(function (dish, i) {
      var img = images[dish.image] || {};
      var qty = state.qty[dish.id] || 1;
      var price = pricesVisible() && dish.price != null
        ? '<div class="price"><span class="price__value">' + esc(formatPrice(dish.price)) + "</span>" +
          (dish.placeholder ? '<span class="price__note">' + esc(t("sig.samplePrice")) + "</span>" : "") + "</div>"
        : "<div></div>";
      return (
        '<article class="dish-card" data-dish-card="' + esc(dish.id) + '">' +
          '<div class="dish-card__inner">' +
            '<div class="dish-card__media img-frame">' +
              '<img data-img="' + esc(dish.image) + '" data-img-sizes="(min-width: 1280px) 26vw, (min-width: 640px) 50vw, 100vw" alt="' + esc(loc(img.alt)) + '" loading="lazy" decoding="async" width="1100" height="1155">' +
              '<span class="dish-card__index" aria-hidden="true">0' + (i + 1) + "</span>" +
            "</div>" +
            '<div class="dish-card__body">' +
              '<h3 class="dish-card__name">' + esc(loc(dish.name)) + "</h3>" +
              '<p class="dish-card__alt" lang="' + alt + '">' + esc(loc(dish.name, alt)) + "</p>" +
              '<p class="dish-card__desc">' + esc(loc(dish.desc)) + "</p>" +
              '<div class="dish-card__meta">' + price +
                '<div class="qty" role="group" aria-label="' + esc(t("sig.qty") + " — " + loc(dish.name)) + '">' +
                  '<button type="button" class="qty__btn" data-qty-step="-1" data-dish="' + esc(dish.id) + '" aria-label="' + esc(t("sig.qtyDec")) + '">−</button>' +
                  '<output class="qty__value" aria-live="polite" data-qty-for="' + esc(dish.id) + '">' + qty + "</output>" +
                  '<button type="button" class="qty__btn" data-qty-step="1" data-dish="' + esc(dish.id) + '" aria-label="' + esc(t("sig.qtyInc")) + '">+</button>' +
                "</div>" +
              "</div>" +
              '<a class="btn btn--brand btn--block" data-wa="dish" data-dish="' + esc(dish.id) + '" aria-label="' + esc(t("menu.orderAria", { dish: loc(dish.name) })) + '">' +
                '<svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg><span>' + esc(t("cta.whatsapp")) + "</span>" +
              "</a>" +
            "</div>" +
          "</div>" +
        "</article>"
      );
    }).join("");
    track.insertAdjacentHTML("beforeend", html);
    $$("img[data-img]", track).forEach(function (img) { setImage(img, img.dataset.img, { maxW: 1200 }); });
    $$(".dish-card", track).forEach(bindTilt);
  }

  document.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-qty-step]");
    if (!btn) return;
    var id = btn.dataset.dish;
    var next = Math.min(20, Math.max(1, (state.qty[id] || 1) + Number(btn.dataset.qtyStep)));
    state.qty[id] = next;
    $$('[data-qty-for="' + id + '"]').forEach(function (o) { o.textContent = next; });
    $$('[data-wa][data-dish="' + id + '"]').forEach(function (el) {
      setAction(el, waUrl(id), "toast.waMissing", true);
    });
  });

  /* 3D tilt — desktop pointers only, respects reduced motion */
  var finePointer = window.matchMedia("(hover: hover) and (pointer: fine)");
  var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  function bindTilt(card) {
    var inner = $(".dish-card__inner", card);
    var raf = 0;
    card.addEventListener("pointermove", function (e) {
      if (!finePointer.matches || reducedMotion.matches || e.pointerType !== "mouse") return;
      var r = inner.getBoundingClientRect();
      var px = (e.clientX - r.left) / r.width;
      var py = (e.clientY - r.top) / r.height;
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(function () {
        card.classList.add("is-tilting");
        card.style.setProperty("--ry", ((px - 0.5) * 12).toFixed(2) + "deg");
        card.style.setProperty("--rx", ((0.5 - py) * 10).toFixed(2) + "deg");
        card.style.setProperty("--mx", (px * 100).toFixed(1) + "%");
        card.style.setProperty("--my", (py * 100).toFixed(1) + "%");
      });
    });
    card.addEventListener("pointerleave", function () {
      cancelAnimationFrame(raf);
      card.classList.remove("is-tilting");
      card.style.setProperty("--rx", "0deg");
      card.style.setProperty("--ry", "0deg");
    });
  }

  /* ---------------------------------------------------------------------
     Section 03 — Menu: filter + search
     --------------------------------------------------------------------- */
  var catsEl = $("#menuCats"), gridEl = $("#menuGrid"), emptyEl = $("#menuEmpty"),
      countEl = $("#menuCount"), searchEl = $("#menuSearch"), clearEl = $("#menuSearchClear");

  function normalize(str) {
    return String(str || "").toLowerCase()
      .replace(/[ً-ٰٟـ]/g, "")  // tashkeel + tatweel
      .replace(/[أإآٱ]/g, "ا").replace(/ة/g, "ه").replace(/ى/g, "ي")
      .replace(/\s+/g, " ").trim();
  }

  function categoryLabel(id, lang) {
    for (var i = 0; i < categories.length; i++) if (categories[i].id === id) return loc(categories[i].label, lang);
    return id;
  }

  function countIn(id) {
    return id === "all" ? menuItems.length : menuItems.filter(function (m) { return m.category === id; }).length;
  }

  function renderCategories() {
    if (!catsEl) return;
    var withCounts = catsEl.hasAttribute("data-counts");
    catsEl.innerHTML = categories.filter(function (c) { return countIn(c.id) > 0; }).map(function (c) {
      return '<button type="button" class="cat-btn" data-cat="' + esc(c.id) + '" aria-pressed="' + (c.id === state.category) + '">' + esc(loc(c.label)) +
        (withCounts ? '<span class="cat-btn__count" aria-hidden="true">' + countIn(c.id) + "</span>" : "") + "</button>";
    }).join("");
  }

  // Keep the pressed category visible inside a horizontally scrolling bar
  function revealActiveCategory() {
    if (!catsEl || !catsEl.hasAttribute("data-counts")) return;
    var btn = $('[aria-pressed="true"]', catsEl);
    if (!btn) return;
    var r = catsEl.getBoundingClientRect();
    if (r.top < 0 || r.bottom > window.innerHeight) return;
    try { btn.scrollIntoView({ block: "nearest", inline: "center", behavior: reducedMotion.matches ? "auto" : "smooth" }); } catch (e) { /* ignore */ }
  }

  function filteredItems() {
    var q = normalize(state.query);
    return menuItems.filter(function (item) {
      if (state.category !== "all" && item.category !== state.category) return false;
      if (!q) return true;
      var hay = normalize([item.name.ar, item.name.en, item.desc && item.desc.ar, item.desc && item.desc.en,
        categoryLabel(item.category, "ar"), categoryLabel(item.category, "en")].join(" "));
      return q.split(" ").every(function (word) { return hay.indexOf(word) > -1; });
    });
  }

  /* Menu page card layout */
  var cardObserver = null;
  function observeCards() {
    if (!("IntersectionObserver" in window) || reducedMotion.matches) return;
    if (!cardObserver) {
      cardObserver = new IntersectionObserver(function (entries) {
        var n = 0;
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.style.setProperty("--d", String(n++ % 6));
          entry.target.classList.remove("is-pending");
          cardObserver.unobserve(entry.target);
        });
      }, { rootMargin: "0px 0px -6% 0px" });
    }
    $$(".menu-card", gridEl).forEach(function (card) {
      card.classList.add("is-pending");
      cardObserver.observe(card);
    });
  }

  function menuCardsHtml(items) {
    var alt = other(state.lang);
    return items.map(function (item) {
      var img = item.image && images[item.image];
      var media = img
        ? '<img data-img="' + esc(item.image) + '" data-img-sizes="(min-width: 1440px) 22vw, (min-width: 1024px) 30vw, (min-width: 560px) 48vw, 100vw" alt="' + esc(loc(img.alt)) + '" loading="lazy" decoding="async" width="1100" height="825">'
        : '<span class="menu-card__ph"><span class="menu-card__ph-mark" aria-hidden="true">Naja</span><span class="menu-card__ph-text">' + esc(t("mp.noPhoto")) + "</span></span>";
      var price = showPrice(item)
        ? '<div class="price"><span class="price__value">' + esc(formatPrice(item.price)) + "</span>" +
          (item.placeholder ? '<span class="price__note">' + esc(t("sig.samplePrice")) + "</span>" : "") + "</div>"
        : "<div></div>";
      return (
        '<li class="menu-card" data-anchor="' + esc(item.id) + '">' +
          '<article class="dish-card dish-card--menu" aria-labelledby="dish-' + esc(item.id) + '">' +
            '<div class="dish-card__inner">' +
              '<div class="dish-card__media img-frame' + (img ? "" : " is-noimg") + '">' + media +
                (item.placeholder ? '<span class="menu-card__badge">' + esc(t("mp.sampleBadge")) + "</span>" : "") +
              "</div>" +
              '<div class="dish-card__body">' +
                '<p class="menu-card__cat">' + esc(categoryLabel(item.category)) + "</p>" +
                '<h3 class="dish-card__name" id="dish-' + esc(item.id) + '">' + esc(loc(item.name)) + "</h3>" +
                '<p class="dish-card__alt" lang="' + alt + '" dir="' + (alt === "ar" ? "rtl" : "ltr") + '">' + esc(loc(item.name, alt)) + "</p>" +
                (item.desc ? '<p class="dish-card__desc">' + esc(loc(item.desc)) + "</p>" : "") +
                '<div class="dish-card__meta">' + price +
                  '<a class="btn btn--brand btn--sm" data-wa="dish" data-dish="' + esc(item.id) + '" aria-label="' + esc(t("menu.orderAria", { dish: loc(item.name) })) + '">' +
                    '<svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg><span>' + esc(t("menu.order")) + "</span>" +
                  "</a>" +
                "</div>" +
              "</div>" +
            "</div>" +
          "</article>" +
        "</li>"
      );
    }).join("");
  }

  function renderMenu() {
    if (!gridEl) return;
    var items = filteredItems();
    var cards = gridEl.dataset.layout === "cards";
    var alt = other(state.lang);
    gridEl.innerHTML = cards ? menuCardsHtml(items) : items.map(function (item, i) {
      var price = pricesVisible() && item.price != null ? formatPrice(item.price) : "";
      return (
        '<li class="menu-item" style="--i:' + Math.min(i, 12) + '">' +
          '<div class="menu-item__head">' +
            '<h3 class="menu-item__name">' + esc(loc(item.name)) + "</h3>" +
            (price ? '<span class="menu-item__leader" aria-hidden="true"></span><span class="menu-item__price">' + esc(price) + "</span>" : "") +
          "</div>" +
          '<p class="menu-item__alt" lang="' + alt + '">' + esc(loc(item.name, alt)) + "</p>" +
          (item.desc ? '<p class="menu-item__desc">' + esc(loc(item.desc)) + "</p>" : "") +
          '<div class="menu-item__foot">' +
            '<span class="menu-item__cat">' + esc(categoryLabel(item.category)) + "</span>" +
            '<a class="btn btn--ghost" data-wa="dish" data-dish="' + esc(item.id) + '" aria-label="' + esc(t("menu.orderAria", { dish: loc(item.name) })) + '">' +
              '<svg class="icon" aria-hidden="true"><use href="#i-whatsapp"/></svg><span>' + esc(t("menu.order")) + "</span>" +
            "</a>" +
          "</div>" +
        "</li>"
      );
    }).join("");

    emptyEl.hidden = items.length > 0;
    gridEl.hidden = items.length === 0;
    countEl.textContent = items.length === 1 ? t("menu.countOne") : t("menu.count", { n: items.length });
    clearEl.hidden = !state.query;
    $$("[data-wa]", gridEl).forEach(function (el) {
      setAction(el, waUrl(el.dataset.dish), "toast.waMissing", true);
    });
    if (cards) {
      $$("img[data-img]", gridEl).forEach(function (img) { setImage(img, img.dataset.img, { maxW: 1200 }); });
      $$(".dish-card", gridEl).forEach(bindTilt);
      observeCards();
    }
    var activeEl = $("#menuActive");
    if (activeEl) {
      activeEl.hidden = state.category === "all";
      $("#menuActiveLabel").textContent = t("mp.activeCat", { cat: categoryLabel(state.category) });
    }
    requestLayoutRefresh();
  }

  if (catsEl) {
    catsEl.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-cat]");
      if (!btn) return;
      state.category = btn.dataset.cat;
      $$("[data-cat]", catsEl).forEach(function (b) { b.setAttribute("aria-pressed", String(b === btn)); });
      renderMenu();
      revealActiveCategory();
      // Menu page: if the visitor has scrolled past the start of the results, bring them back to it.
      // Instant on purpose: a smooth scroll would be cut short by the ScrollTrigger refresh that follows.
      var start = $("#menuResultsStart");
      if (start && start.getBoundingClientRect().top < 0) {
        try { start.scrollIntoView({ behavior: "instant", block: "start" }); } catch (err) { start.scrollIntoView(true); }
      }
    });
  }
  if (searchEl) {
    searchEl.addEventListener("input", function () { state.query = searchEl.value; renderMenu(); });
    searchEl.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && searchEl.value) { e.stopPropagation(); searchEl.value = ""; state.query = ""; renderMenu(); }
    });
  }
  if (clearEl) clearEl.addEventListener("click", function () { searchEl.value = ""; state.query = ""; renderMenu(); searchEl.focus(); });
  var activeClear = $("#menuActiveClear");
  if (activeClear) activeClear.addEventListener("click", function () { setCategory("all"); revealActiveCategory(); });
  var resetEl = $("#menuReset");
  if (resetEl) resetEl.addEventListener("click", function () {
    searchEl.value = ""; state.query = ""; state.category = "all";
    renderCategories(); renderMenu(); searchEl.focus();
  });

  function setCategory(id) {
    state.category = id;
    renderCategories();
    renderMenu();
  }

  /* ---------------------------------------------------------------------
     Marquee bands (decorative, aria-hidden)
     --------------------------------------------------------------------- */
  function renderMarquees() {
    var top = $("#marqueeTop"), bottom = $("#marqueeBottom");
    var star = '<span class="marquee__star">✦</span>';
    if (top) {
      var words = categories.filter(function (c) { return c.id !== "all"; }).map(function (c) {
        return '<span class="marquee__item" lang="' + state.lang + '">' + esc(loc(c.label)) + "</span>" + star +
               '<span class="marquee__item marquee__item--alt" lang="' + other(state.lang) + '">' + esc(loc(c.label, other(state.lang))) + "</span>" + star;
      }).join("");
      top.innerHTML = '<div class="marquee__group">' + words + '</div><div class="marquee__group">' + words + "</div>";
    }
    if (bottom) {
      var seq = "";
      for (var i = 0; i < 3; i++) {
        seq += '<span class="marquee__item" lang="ar">' + esc(t("marquee.order", null, "ar")) + "</span>" + star +
               '<span class="marquee__item marquee__item--alt" lang="en">' + esc(t("marquee.order", null, "en")) + "</span>" + star +
               '<span class="marquee__item marquee__item--script" lang="en">Feel Fresh</span>' + star;
      }
      bottom.innerHTML = '<div class="marquee__group">' + seq + '</div><div class="marquee__group">' + seq + "</div>";
    }
  }

  /* ---------------------------------------------------------------------
     Section 03 — Browse by category (feeds the menu filter)
     --------------------------------------------------------------------- */
  var catListEl = $("#catList"), catPreview = $("#catPreview");
  function renderCategoryList() {
    if (!catListEl) return;
    var alt = other(state.lang);
    catListEl.innerHTML = categories.filter(function (c) { return c.id !== "all"; }).map(function (c, i) {
      var n = menuItems.filter(function (m) { return m.category === c.id; }).length;
      return (
        '<li class="cat-row" data-cat-row>' +
          '<a class="cat-row__link" href="#menu" data-goto-cat="' + esc(c.id) + '" data-image="' + esc(c.image || "") + '" aria-label="' + esc(t("cats.view", { cat: loc(c.label) })) + '">' +
            '<span class="cat-row__num" aria-hidden="true">' + (i + 1 < 10 ? "0" : "") + (i + 1) + "</span>" +
            '<span class="cat-row__name">' + esc(loc(c.label)) + "</span>" +
            '<span class="cat-row__alt" lang="' + alt + '">' + esc(loc(c.label, alt)) + "</span>" +
            '<span class="cat-row__count">' + esc(t("cats.count", { n: n })) + "</span>" +
            '<span class="cat-row__arrow" aria-hidden="true"><svg class="icon icon--dir"><use href="#i-arrow"/></svg></span>' +
          "</a>" +
        "</li>"
      );
    }).join("");
  }
  if (catListEl) {
    catListEl.addEventListener("click", function (e) {
      var a = e.target.closest("[data-goto-cat]");
      if (!a) return;
      setCategory(a.dataset.gotoCat);
    });
    // Floating image preview that follows the cursor (desktop only)
    if (catPreview) {
      var pImg = $("img", catPreview), px = 0, py = 0, cx = 0, cy = 0, rafId = 0, shown = false;
      watchImage(pImg);
      var loop = function () {
        cx += (px - cx) * 0.16; cy += (py - cy) * 0.16;
        catPreview.style.transform = "translate3d(" + cx.toFixed(1) + "px," + cy.toFixed(1) + "px,0) translate(-50%,-50%) rotate(" + ((px - cx) * 0.04).toFixed(2) + "deg)";
        rafId = shown ? requestAnimationFrame(loop) : 0;
      };
      catListEl.addEventListener("pointermove", function (e) {
        if (!finePointer.matches || reducedMotion.matches || e.pointerType !== "mouse") return;
        var a = e.target.closest("[data-goto-cat]");
        if (!a) return;
        px = e.clientX; py = e.clientY;
        var key = a.dataset.image;
        if (key && images[key] && pImg.dataset.key !== key) {
          pImg.dataset.key = key;
          pImg.src = sizedUrl(images[key].src, 600);
        }
        if (!shown) { shown = true; cx = px; cy = py; catPreview.classList.add("is-visible"); rafId = requestAnimationFrame(loop); }
      });
      catListEl.addEventListener("pointerleave", function () {
        shown = false; catPreview.classList.remove("is-visible"); cancelAnimationFrame(rafId);
      });
    }
  }

  /* ---------------------------------------------------------------------
     Section 06 — Gallery + lightbox
     --------------------------------------------------------------------- */
  var galleryEl = galleryHost;

  /* Gallery page: category filters, masonry tiles, and photos that fail to load are dropped */
  var galleryFilterEl = $("#galleryFilters"), galleryStatusEl = $("#galleryStatus"), galleryEmptyEl = $("#galleryEmpty");
  var galleryDropTimer;

  function galleryShown(cat) {
    return galleryAll.filter(function (g) { return !g.failed && (cat === "all" || g.category === cat); });
  }

  function renderGalleryFilters() {
    if (!galleryFilterEl) return;
    galleryFilterEl.innerHTML = galleryCats.filter(function (c) {
      return c.id === "all" || galleryShown(c.id).length > 0;
    }).map(function (c) {
      return '<button type="button" class="cat-btn gp-filter" data-gallery-cat="' + esc(c.id) + '" aria-pressed="' + (c.id === state.galleryCat) + '">' +
        esc(loc(c.label)) + '<span class="gp-filter__count" aria-hidden="true">' + galleryShown(c.id).length + "</span></button>";
    }).join("");
  }

  function dropGalleryPhoto(entry) {
    if (!entry || entry.failed) return;
    entry.failed = true;
    clearTimeout(galleryDropTimer);
    galleryDropTimer = setTimeout(function wait() {
      if (state.lightboxIndex > -1) { galleryDropTimer = setTimeout(wait, 400); return; } // never reshuffle under an open lightbox
      renderGalleryPage();
      requestLayoutRefresh();
    }, 250);
  }

  function renderGalleryPage() {
    if (state.galleryCat !== "all" && !galleryShown(state.galleryCat).length) state.galleryCat = "all";
    var list = (gallery = galleryShown(state.galleryCat));
    galleryEl.innerHTML = list.map(function (g, i) {
      var img = images[g.image] || {};
      var cap = loc(g.caption);
      return (
        '<li class="gp-tile gp-tile--' + esc(g.shape || "square") + '" data-gallery-item>' +
          '<button type="button" class="gp-tile__btn" data-lightbox="' + i + '" aria-label="' + esc(t("gal.open", { caption: cap })) + '">' +
            '<span class="gp-tile__frame img-frame">' +
              '<img data-img="' + esc(g.image) + '" data-img-sizes="(min-width: 1100px) 25vw, (min-width: 700px) 33vw, 50vw" alt="' + esc(loc(img.alt)) + '" loading="lazy" decoding="async">' +
            "</span>" +
            '<span class="gp-tile__caption">' + esc(cap) + "</span>" +
          "</button>" +
        "</li>"
      );
    }).join("");
    $$("img[data-img]", galleryEl).forEach(function (img, i) {
      setImage(img, img.dataset.img, { maxW: 1600 });
      img.addEventListener("error", function () { dropGalleryPhoto(list[i]); });
    });
    renderGalleryFilters();
    if (galleryStatusEl) galleryStatusEl.textContent = list.length ? t("gp.count", { n: list.length }) : "";
    if (galleryEmptyEl) galleryEmptyEl.hidden = list.length > 0;
  }

  if (galleryFilterEl) {
    galleryFilterEl.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-gallery-cat]");
      if (!btn || btn.dataset.galleryCat === state.galleryCat) return;
      state.galleryCat = btn.dataset.galleryCat;
      renderGalleryPage();
      galleryEl.classList.add("is-filtered");
      setTimeout(function () { galleryEl.classList.remove("is-filtered"); }, 700);
      var active = $('[data-gallery-cat="' + state.galleryCat + '"]', galleryFilterEl);
      if (active) active.focus({ preventScroll: true }); // the filters were re-rendered; keep the keyboard place
      requestLayoutRefresh();
    });
  }

  function renderGallery() {
    if (!galleryEl) return;
    if (gallerySource === "page") { renderGalleryPage(); return; }
    galleryEl.innerHTML = gallery.map(function (g, i) {
      var img = images[g.image] || {};
      var cap = loc(g.caption);
      return (
        '<li class="gallery-item gallery-item--' + esc(g.shape || "square") + '" data-gallery-item>' +
          '<button type="button" class="gallery-item__btn" data-lightbox="' + i + '" aria-label="' + esc(t("gal.open", { caption: cap })) + '">' +
            '<span class="gallery-item__frame img-frame" data-parallax>' +
              '<img data-img="' + esc(g.image) + '" data-img-sizes="(min-width: 1024px) 50vw, 100vw" alt="' + esc(loc(img.alt)) + '" loading="lazy" decoding="async">' +
            "</span>" +
            '<span class="gallery-item__caption">' + esc(cap) + "</span>" +
          "</button>" +
        "</li>"
      );
    }).join("");
    $$("img[data-img]", galleryEl).forEach(function (img) { setImage(img, img.dataset.img, { maxW: 1600 }); });
  }

  var lb = $("#lightbox"), lbImg = $("#lightboxImg"), lbCap = $("#lightboxCaption"), lbCount = $("#lightboxCount"),
      lbWrap = lb ? $(".lightbox__img-wrap", lb) : null, lastFocus = null;

  function showLightbox(index) {
    var n = gallery.length;
    if (!n) return;
    state.lightboxIndex = (index + n) % n;
    var g = gallery[state.lightboxIndex];
    var img = images[g.image] || {};
    lbWrap.classList.remove("is-failed");
    lbImg.style.animation = "none";
    void lbImg.offsetWidth; // restart entrance animation
    lbImg.style.animation = "";
    lbImg.src = sizedUrl(img.src || "", 1800);
    lbImg.alt = loc(img.alt);
    lbCap.textContent = loc(g.caption);
    lbCount.textContent = (state.lightboxIndex + 1) + " / " + n;
    // Preload neighbours
    [1, -1].forEach(function (d) {
      var nb = images[gallery[(state.lightboxIndex + d + n) % n].image];
      if (nb) { var p = new Image(); p.src = sizedUrl(nb.src, 1800); }
    });
  }
  function openLightbox(index) {
    if (!lb) return;
    lastFocus = document.activeElement;
    lb.hidden = false;
    document.body.classList.add("lb-open");
    showLightbox(index);
    $(".lightbox__close", lb).focus();
  }
  function closeLightbox() {
    if (!lb || lb.hidden) return;
    lb.hidden = true;
    document.body.classList.remove("lb-open");
    state.lightboxIndex = -1;
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  }
  if (lb) {
    watchImage(lbImg);
    lbImg.addEventListener("error", function () { lbWrap.classList.add("is-failed"); });
    if (galleryEl) galleryEl.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-lightbox]");
      if (btn) openLightbox(Number(btn.dataset.lightbox));
    });
    $$("[data-lb-close]", lb).forEach(function (el) { el.addEventListener("click", closeLightbox); });
    $("#lightboxPrev").addEventListener("click", function () { showLightbox(state.lightboxIndex - 1); });
    $("#lightboxNext").addEventListener("click", function () { showLightbox(state.lightboxIndex + 1); });

    lb.addEventListener("keydown", function (e) {
      if (e.key === "Escape") { e.preventDefault(); closeLightbox(); }
      else if (e.key === "ArrowRight") { e.preventDefault(); showLightbox(state.lightboxIndex + (isRTL() ? -1 : 1)); }
      else if (e.key === "ArrowLeft") { e.preventDefault(); showLightbox(state.lightboxIndex + (isRTL() ? 1 : -1)); }
      else if (e.key === "Tab") {
        var f = $$("button", lb).filter(function (b) { return b.offsetParent !== null; });
        var first = f[0], last = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    });

    // Touch swipe
    var sx = 0, sy = 0;
    lb.addEventListener("touchstart", function (e) { sx = e.touches[0].clientX; sy = e.touches[0].clientY; }, { passive: true });
    lb.addEventListener("touchend", function (e) {
      var dx = e.changedTouches[0].clientX - sx, dy = e.changedTouches[0].clientY - sy;
      if (Math.abs(dx) < 50 || Math.abs(dx) < Math.abs(dy)) return;
      var forward = dx < 0; // swipe left
      if (isRTL()) forward = !forward;
      showLightbox(state.lightboxIndex + (forward ? 1 : -1));
    }, { passive: true });
  }

  /* ---------------------------------------------------------------------
     Section 06 — Contact details, footer, JSON-LD, action bar
     --------------------------------------------------------------------- */
  function tbc() { return '<span class="tbc">' + esc(t("visit.tbc")) + "</span>"; }

  // "966583815633" -> "+966 58 381 5633" (Saudi mobile); other numbers keep a plain + prefix
  function displayPhone(d) {
    return /^9665\d{8}$/.test(d) ? "+966 " + d.slice(3, 5) + " " + d.slice(5, 8) + " " + d.slice(8) : "+" + d;
  }

  function renderContact() {
    var phone = digits(config.phone), wa = digits(config.whatsapp);
    var address = loc(config.address);
    var addrEl = $("#infoAddress");
    if (addrEl) {
      addrEl.innerHTML = address
        ? (config.googleMapsUrl
            ? '<a href="' + esc(config.googleMapsUrl) + '" target="_blank" rel="noopener noreferrer">' + esc(address) + "</a>"
            : esc(address)) + "<br>" + esc(t("visit.cityLine"))
        : esc(t("visit.cityLine")) + "<br>" + tbc();
    }
    var hoursEl = $("#infoHours");
    if (hoursEl) {
      var hours = config.openingHours || [];
      hoursEl.innerHTML = hours.length
        ? hours.map(function (h) { return '<span class="hours-row"><span>' + esc(loc(h.days)) + "</span><span>" + esc(loc(h.time)) + "</span></span>"; }).join("")
        : tbc();
    }
    var phoneEl = $("#infoPhone");
    if (phoneEl) phoneEl.innerHTML = phone ? '<a class="ltr" href="tel:+' + phone + '">' + esc(displayPhone(phone)) + "</a>" : tbc();
    var waEl = $("#infoWhatsapp");
    if (waEl) waEl.innerHTML = wa ? '<a class="ltr" href="' + esc(waUrl("")) + '" target="_blank" rel="noopener noreferrer">' + esc(displayPhone(wa)) + "</a>" : tbc();

    // Footer contact (icon rows; only configured details)
    var fc = $("#footerContact");
    if (fc) {
      var row = function (icon, label, value) {
        return '<li class="footer-contact__row"><svg class="icon" aria-hidden="true"><use href="#' + icon + '"/></svg>' +
          '<span><span class="footer-contact__label">' + esc(label) + "</span>" + value + "</span></li>";
      };
      var rows = [row("i-pin", t("footer.location"), "<span>" + esc(address ? address + (isRTL() ? "، " : ", ") + t("visit.cityLine") : t("visit.cityLine")) + "</span>")];
      if (wa) rows.push(row("i-whatsapp", t("visit.whatsapp"), '<a class="ltr" dir="ltr" href="' + esc(waUrl("")) + '" target="_blank" rel="noopener noreferrer">' + esc(displayPhone(wa)) + "</a>"));
      if (phone) rows.push(row("i-phone", t("visit.phone"), '<a class="ltr" dir="ltr" href="tel:+' + phone + '">' + esc(displayPhone(phone)) + "</a>"));
      var fh = config.openingHours || [];
      if (fh.length) rows.push(row("i-clock", t("visit.hours"), fh.map(function (h) { return "<span>" + esc(loc(h.days)) + " · " + esc(loc(h.time)) + "</span>"; }).join("<br>")));
      fc.innerHTML = rows.join("");
    }

    // Social (verified links only)
    var labels = { instagram: "Instagram", tiktok: "TikTok", snapchat: "Snapchat", x: "X" };
    var social = config.social || {};
    var links = Object.keys(labels).filter(function (k) { return /^https?:\/\//.test(social[k] || ""); });
    var col = $("#footerSocialCol"), list = $("#footerSocial");
    if (col && list) {
      col.hidden = links.length === 0;
      list.innerHTML = links.map(function (k) {
        return '<li><a href="' + esc(social[k]) + '" target="_blank" rel="noopener noreferrer">' + labels[k] + "</a></li>";
      }).join("");
    }

    // Menu page: unconfigured details are hidden rather than shown as "to be announced"
    var hideHost = $("[data-hide-missing-info]");
    if (hideHost) {
      var have = {
        address: !!address,
        hours: !!(config.openingHours || []).length,
        phone: !!phone,
        whatsapp: !!wa
      };
      var any = false;
      $$("[data-info-cell]", hideHost).forEach(function (cell) {
        var ok = !!have[cell.dataset.infoCell];
        cell.hidden = !ok;
        any = any || ok;
      });
      hideHost.hidden = !any;
      var pending = $("#orderPending");
      if (pending) pending.hidden = !!(wa || phone || config.googleMapsUrl);
    }

    var rights = $("#footerRights");
    if (rights) rights.textContent = t("footer.rights", { year: new Date().getFullYear() });

    // Mobile action bar only when there is something real to act on
    var bar = $("#actionBar");
    if (bar) {
      var hasAny = !!(phone || wa);
      bar.hidden = !hasAny;
      $("#barCall").hidden = !phone;
      $("#barWa").hidden = !wa;
      document.body.classList.toggle("has-action-bar", hasAny);
    }
  }

  /* ---------------------------------------------------------------------
     Contact page: info cards + map from the same config as the footer
     (empty details show a clearly marked "to be announced" card, never a guess)
     --------------------------------------------------------------------- */
  var contactCardsEl = $("#contactCards"), contactMapEl = $("#contactMap");

  function contactCardHtml(c) {
    var inner =
      '<svg class="icon ct-card__icon' + (c.dir ? " icon--dir" : "") + '" aria-hidden="true"><use href="#' + c.icon + '"/></svg>' +
      '<span class="ct-card__body">' +
        '<span class="ct-card__label">' + esc(c.label) + "</span>" +
        '<span class="ct-card__value">' + c.value + "</span>" +
        (c.href ? '<span class="ct-card__cta">' + esc(c.cta) + '<svg class="icon icon--dir" aria-hidden="true"><use href="#i-arrow"/></svg></span>' : "") +
      "</span>";
    var cls = "ct-card" + (c.href ? "" : " ct-card--static") + (c.missing ? " is-missing" : "");
    return "<li>" + (c.href
      ? '<a class="' + cls + '" href="' + esc(c.href) + '"' + (c.external ? ' target="_blank" rel="noopener noreferrer"' : "") + ">" + inner + "</a>"
      : '<div class="' + cls + '">' + inner + "</div>") + "</li>";
  }

  function renderContactPage() {
    if (contactCardsEl) {
      var phone = digits(config.phone), wa = digits(config.whatsapp);
      var address = loc(config.address), hours = config.openingHours || [], map = config.googleMapsUrl;
      var cards = [
        { icon: "i-phone", label: t("visit.phone"), value: phone ? '<span class="ltr" dir="ltr">' + esc(displayPhone(phone)) + "</span>" : tbc(), href: phone ? "tel:+" + phone : "", cta: t("visit.call"), missing: !phone },
        { icon: "i-whatsapp", label: t("visit.whatsapp"), value: wa ? '<span class="ltr" dir="ltr">' + esc(displayPhone(wa)) + "</span>" : tbc(), href: wa ? waUrl("") : "", cta: t("cta.whatsapp"), external: true, missing: !wa },
        { icon: "i-pin", label: t("visit.address"), value: address ? esc(address) + "<br>" + esc(t("visit.cityLine")) : esc(t("visit.cityLine")) + "<br>" + tbc(), href: "", missing: !address },
        { icon: "i-arrow", dir: true, label: t("ct.dirLabel"), value: esc(t("visit.cityLine")), href: map || "", cta: t("visit.directions"), external: true, missing: !map },
        { icon: "i-clock", label: t("visit.hours"), value: hours.length
            ? hours.map(function (h) { return '<span class="hours-row"><span>' + esc(loc(h.days)) + "</span><span>" + esc(loc(h.time)) + "</span></span>"; }).join("")
            : tbc(), href: "", missing: !hours.length }
      ];
      contactCardsEl.innerHTML = cards.map(contactCardHtml).join("");
    }

    var addrEl = $("#contactAddress");
    if (addrEl) {
      var a = loc(config.address);
      addrEl.innerHTML = a ? esc(a) + "<br>" + esc(t("visit.cityLine")) : esc(t("visit.cityLine"));
    }

    if (contactMapEl) {
      var src = config.mapsEmbedUrl || "";
      var ok = /^https:\/\/(www\.google\.com\/maps\/embed\?|maps\.google\.com\/maps\?.*output=embed)/i.test(src);
      var frame = $("iframe", contactMapEl);
      if (ok && !frame) {
        contactMapEl.classList.add("has-map");
        contactMapEl.innerHTML = '<iframe src="' + esc(src) + '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen title=""></iframe>';
        frame = $("iframe", contactMapEl);
      }
      if (frame) frame.title = t("ct.mapTitle");
      var ph = $("[data-map-placeholder]");
      if (ph) ph.hidden = ok;
    }
  }

  /* ---------------------------------------------------------------------
     Homepage FAQ (visible list + FAQPage structured data from the same text)
     --------------------------------------------------------------------- */
  var FAQ_COUNT = 4;
  function faqAnswer(i, html) {
    var phone = digits(config.phone) || digits(config.whatsapp);
    var num = phone ? displayPhone(phone) : t("visit.tbc");
    var text = t("faq.a" + i);
    if (!html) return text.split("{phone}").join(num);
    return text.split("{phone}").map(esc).join('<span dir="ltr">' + esc(num) + "</span>");
  }
  function renderFaq() {
    var list = $("#faqList");
    if (!list) return;
    var open = $$("details", list).map(function (d) { return d.open; });
    var html = "";
    for (var i = 1; i <= FAQ_COUNT; i++) {
      html += '<details class="faq__item"' + ((open.length ? open[i - 1] : i === 1) ? " open" : "") + ">" +
        '<summary class="faq__q"><span>' + esc(t("faq.q" + i)) + '</span><span class="faq__icon" aria-hidden="true"></span></summary>' +
        '<div class="faq__a"><p>' + faqAnswer(i, true) + "</p></div></details>";
    }
    list.innerHTML = html;
    var ld = $("#faqJsonld");
    if (ld) {
      var entities = [];
      for (var j = 1; j <= FAQ_COUNT; j++) {
        entities.push({ "@type": "Question", name: t("faq.q" + j), acceptedAnswer: { "@type": "Answer", text: faqAnswer(j, false) } });
      }
      ld.textContent = JSON.stringify({ "@context": "https://schema.org", "@type": "FAQPage", mainEntity: entities });
    }
  }

  function updateJsonLd() {
    var el = $("#jsonld");
    if (!el) return;
    var data = {
      "@context": "https://schema.org",
      "@type": "Restaurant",
      name: config.name || "Naja Restaurant",
      alternateName: config.nameAr || undefined,
      slogan: config.slogan || undefined,
      servesCuisine: config.cuisine || "Mixed Menu",
      currenciesAccepted: config.currency || "SAR",
      address: {
        "@type": "PostalAddress",
        streetAddress: (config.address && config.address.en) || undefined,
        addressLocality: config.city || "Dammam",
        addressCountry: config.country || "SA"
      }
    };
    if (config.siteUrl) data.url = config.siteUrl;
    if (digits(config.phone)) data.telephone = "+" + digits(config.phone);
    if (config.googleMapsUrl) data.hasMap = config.googleMapsUrl;
    var hours = (config.openingHours || []).map(function (h) { return h.schema; }).filter(Boolean);
    if (hours.length) data.openingHours = hours;
    var social = config.social || {};
    var same = Object.keys(social).map(function (k) { return social[k]; }).filter(function (u) { return /^https?:\/\//.test(u || ""); });
    if (same.length) data.sameAs = same;
    if (page === "menu") {
      // Only dishes the restaurant has verified (placeholder: false) are published as structured data
      var verified = menuItems.filter(function (i) { return !i.placeholder; });
      var sections = categories.filter(function (c) { return c.id !== "all"; }).map(function (c) {
        return {
          "@type": "MenuSection",
          name: loc(c.label, "en"),
          hasMenuItem: verified.filter(function (i) { return i.category === c.id; }).map(function (i) {
            var entry = { "@type": "MenuItem", name: i.name.en, description: (i.desc && i.desc.en) || undefined };
            if (i.price != null) entry.offers = { "@type": "Offer", price: String(i.price), priceCurrency: config.currency || "SAR" };
            return entry;
          })
        };
      }).filter(function (sec) { return sec.hasMenuItem.length; });
      if (sections.length) data.hasMenu = { "@type": "Menu", name: "Naja Restaurant Menu", hasMenuSection: sections };
    }
    el.textContent = JSON.stringify(data, null, 2);
  }

  function reportMissingConfig() {
    var missing = [];
    if (!digits(config.whatsapp)) missing.push("whatsapp");
    if (!digits(config.phone)) missing.push("phone");
    if (!loc(config.address)) missing.push("address");
    if (!config.googleMapsUrl) missing.push("googleMapsUrl");
    if (!(config.openingHours || []).length) missing.push("openingHours");
    if (missing.length && window.console && console.info) {
      console.info("[Naja] Awaiting verified details in js/config.js: " + missing.join(", ") + ". Related buttons are disabled until provided.");
    }
  }

  /* ---------------------------------------------------------------------
     Header, navigation, mobile menu
     --------------------------------------------------------------------- */
  var header = $("#siteHeader"), toggle = $("#menuToggle"), mobileNav = $("#mobileNav");
  var hero = $("#home") || $(".hero"), bar = $("#actionBar");

  var progressEl = $("#scrollProgress");
  function onScroll() {
    var y = window.scrollY || window.pageYOffset;
    if (progressEl) {
      var max = document.documentElement.scrollHeight - window.innerHeight;
      progressEl.style.transform = "scaleX(" + (max > 0 ? Math.min(1, y / max) : 0).toFixed(4) + ")";
    }
    if (header) header.classList.toggle("is-scrolled", y > 40);
    if (bar && !bar.hidden) bar.classList.toggle("is-visible", y > (hero ? hero.offsetHeight * 0.6 : 300));
  }
  window.addEventListener("scroll", onScroll, { passive: true });

  function setNav(open) {
    if (!mobileNav || !toggle) return;
    mobileNav.hidden = !open;
    toggle.setAttribute("aria-expanded", String(open));
    toggle.setAttribute("aria-label", t(open ? "nav.close" : "nav.open"));
    toggle.dataset.i18nAttr = "aria-label:" + (open ? "nav.close" : "nav.open");
    document.body.classList.toggle("nav-open", open);
    if (open) { var first = $("a", mobileNav); if (first) first.focus(); }
  }
  if (toggle) toggle.addEventListener("click", function () { setNav(toggle.getAttribute("aria-expanded") !== "true"); });
  if (mobileNav) {
    mobileNav.addEventListener("click", function (e) { if (e.target.closest("a[href^='#']")) setNav(false); });
  }
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && toggle && toggle.getAttribute("aria-expanded") === "true") { setNav(false); toggle.focus(); }
    if (e.key === "Tab" && document.body.classList.contains("nav-open")) {
      var f = [toggle].concat($$("a, button", mobileNav)).concat($$(".site-header a, .site-header button").filter(function (el) { return el !== toggle && el.offsetParent !== null; }));
      f = f.filter(function (el) { return el.offsetParent !== null; });
      if (f.indexOf(document.activeElement) === -1) { e.preventDefault(); f[0].focus(); }
    }
  });
  window.addEventListener("resize", function () {
    if (window.innerWidth >= 1024 && document.body.classList.contains("nav-open")) setNav(false);
  });

  // Active nav link
  if ("IntersectionObserver" in window) {
    var navLinks = $$(".primary-nav a");
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var map = { signature: "home", categories: "menu", "how-to-order": "visit" };
        var id = map[entry.target.id] || entry.target.id;
        navLinks.forEach(function (a) {
          var active = a.getAttribute("href") === "#" + id;
          a.classList.toggle("is-active", active);
          if (active) a.setAttribute("aria-current", "true"); else a.removeAttribute("aria-current");
        });
      });
    }, { rootMargin: "-45% 0px -50% 0px" });
    ["home", "signature", "categories", "menu", "experience", "gallery", "how-to-order", "visit"].forEach(function (id) {
      var s = document.getElementById(id);
      if (s) io.observe(s);
    });
  }

  /* ---------------------------------------------------------------------
     Pointer effects: hero spotlight + magnetic buttons (desktop only)
     --------------------------------------------------------------------- */
  if (hero) {
    hero.addEventListener("pointermove", function (e) {
      if (!finePointer.matches || reducedMotion.matches) return;
      var r = hero.getBoundingClientRect();
      hero.style.setProperty("--sx", ((e.clientX - r.left) / r.width * 100).toFixed(1) + "%");
      hero.style.setProperty("--sy", ((e.clientY - r.top) / r.height * 100).toFixed(1) + "%");
    });
  }
  document.addEventListener("pointermove", function (e) {
    if (!finePointer.matches || reducedMotion.matches) return;
    var btn = e.target.closest && e.target.closest(".btn--lg");
    if (!btn) return;
    var r = btn.getBoundingClientRect();
    var dx = (e.clientX - (r.left + r.width / 2)) * 0.18;
    var dy = (e.clientY - (r.top + r.height / 2)) * 0.3;
    btn.style.transform = "translate(" + dx.toFixed(1) + "px," + dy.toFixed(1) + "px)";
    btn.classList.add("is-magnetic");
  });
  document.addEventListener("pointerout", function (e) {
    var btn = e.target.closest && e.target.closest(".btn--lg.is-magnetic");
    if (btn && !btn.contains(e.relatedTarget)) { btn.style.transform = ""; btn.classList.remove("is-magnetic"); }
  });

  /* ---------------------------------------------------------------------
     One-shot "in view" class (footer map animation)
     --------------------------------------------------------------------- */
  (function () {
    var els = $$("[data-inview]");
    if (!els.length) return;
    if (!("IntersectionObserver" in window) || reducedMotion.matches) {
      els.forEach(function (el) { el.classList.add("is-inview"); });
      return;
    }
    var seen = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-inview");
        seen.unobserve(entry.target);
      });
    }, { threshold: 0.35 });
    els.forEach(function (el) { seen.observe(el); });
  })();

  /* ---------------------------------------------------------------------
     Language
     --------------------------------------------------------------------- */
  function renderAll() {
    applyTranslations();
    applyStaticImages();
    renderSignature();
    renderMarquees();
    renderCategoryList();
    renderCategories();
    renderMenu();
    renderGallery();
    renderContact();
    renderContactPage();
    renderFaq();
    refreshActions();
    if (state.lightboxIndex > -1) showLightbox(state.lightboxIndex);
  }

  function setLanguage(lang, persist) {
    if (!dict[lang]) lang = "ar";
    var anim = animations();
    var scrollY = window.scrollY || window.pageYOffset;
    var anchor = findAnchor();
    if (anim && anim.teardown) anim.teardown();
    state.lang = lang;
    renderAll();
    document.dispatchEvent(new CustomEvent("naja:lang", { detail: { lang: lang } })); // lets page scripts (e.g. the contact form) refresh their own text
    if (persist) {
      storageSet("naja-lang", lang);
      try {
        var url = new URL(window.location.href);
        if (lang === (config.defaultLanguage || "ar")) url.searchParams.delete("lang"); else url.searchParams.set("lang", lang);
        history.replaceState(null, "", url.pathname + url.search + url.hash);
      } catch (e) { /* ignore */ }
    }
    if (anim && anim.init) anim.init();
    // Keep the visitor where they were (pin spacing is rebuilt above)
    try { window.scrollTo({ top: scrollY, behavior: "instant" }); } catch (e) { window.scrollTo(0, scrollY); }
    if (anchor) {
      // Re-align now, and again once the other language's font and ScrollTrigger have settled
      var realign = function () {
        var again = document.querySelector('[data-anchor="' + anchor.id + '"]');
        if (!again) return;
        var delta = again.getBoundingClientRect().top - anchor.top;
        if (Math.abs(delta) > 1) window.scrollBy(0, delta);
      };
      realign();
      requestAnimationFrame(realign);
      if (document.fonts && document.fonts.ready) document.fonts.ready.then(realign);
      setTimeout(realign, 320);
    }
  }

  // The first anchored element (dish card or section) whose top edge is visible below the header
  function findAnchor() {
    var minTop = header ? header.getBoundingClientRect().bottom : 0;
    var best = null;
    $$("[data-anchor]").forEach(function (el) {
      var top = el.getBoundingClientRect().top;
      if (top >= minTop - 2 && top < window.innerHeight && (!best || top < best.top)) best = { id: el.dataset.anchor, top: top };
    });
    return best;
  }

  $$("[data-lang-toggle]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      setLanguage(other(state.lang), true);
      if (document.body.classList.contains("nav-open")) setNav(false);
    });
  });

  /* ---------------------------------------------------------------------
     Boot
     --------------------------------------------------------------------- */
  function initialLang() {
    var param = null;
    try { param = new URL(window.location.href).searchParams.get("lang"); } catch (e) { /* ignore */ }
    return (param && dict[param] && param) || (dict[storageGet("naja-lang")] && storageGet("naja-lang")) || config.defaultLanguage || "ar";
  }

  state.lang = initialLang();
  renderAll();
  updateJsonLd();
  reportMissingConfig();
  onScroll();

  window.Naja = { setLanguage: setLanguage, t: t, state: state, waUrl: waUrl };
})();
