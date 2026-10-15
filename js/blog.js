/* =========================================================================
   NAJA RESTAURANT — BLOG LISTING
   -------------------------------------------------------------------------
   Loaded only on /blog/. Every article is already a crawlable link in the
   HTML; this script adds search (title, keywords, excerpt), category filter
   and "show more" without reloading the page. Text comes from the existing
   translation table (bl.* keys) and refreshes on the "naja:lang" event.
   ========================================================================= */

(function () {
  "use strict";

  var grid = document.getElementById("blogGrid");
  if (!grid || !window.Naja) return;

  var Naja = window.Naja;
  var t = function (key, vars) { return Naja.t(key, vars); };

  var items = Array.prototype.slice.call(grid.children);
  var pageSize = parseInt(grid.dataset.pageSize, 10) || 9;
  var searchEl = document.getElementById("blogSearch");
  var clearEl = document.getElementById("blogSearchClear");
  var catsEl = document.getElementById("blogCats");
  var countEl = document.getElementById("blogCount");
  var emptyEl = document.getElementById("blogEmpty");
  var moreWrap = document.getElementById("blogMoreWrap");
  var moreBtn = document.getElementById("blogMore");
  var resetBtn = document.getElementById("blogReset");

  var state = { cat: "all", query: "", shown: pageSize };

  function normalize(str) {
    return String(str || "").toLowerCase().normalize("NFKD").replace(/[̀-ͯ]/g, "").replace(/\s+/g, " ").trim();
  }

  function matches(li, words) {
    if (state.cat !== "all" && li.dataset.cat !== state.cat) return false;
    var hay = li.dataset.searchNorm || (li.dataset.searchNorm = normalize(li.dataset.search));
    return words.every(function (w) { return hay.indexOf(w) > -1; });
  }

  function render(focusFirstNew) {
    var q = normalize(state.query);
    var words = q ? q.split(" ") : [];
    var list = items.filter(function (li) { return matches(li, words); });
    var visible = 0, firstNew = null;

    items.forEach(function (li) { li.hidden = true; });
    list.forEach(function (li, i) {
      var show = i < state.shown;
      if (show && li.hidden && i >= state.shown - pageSize && !firstNew) firstNew = li;
      li.hidden = !show;
      if (show) visible++;
    });

    countEl.textContent = list.length ? t("bl.count", { shown: visible, total: list.length }) : "";
    emptyEl.hidden = list.length > 0;
    grid.hidden = list.length === 0;
    moreWrap.hidden = list.length <= state.shown;
    clearEl.hidden = !state.query;

    Array.prototype.forEach.call(catsEl.querySelectorAll("[data-cat]"), function (b) {
      b.setAttribute("aria-pressed", String(b.dataset.cat === state.cat));
    });

    if (focusFirstNew && firstNew) {
      var link = firstNew.querySelector("a.bl-card__more, .bl-card__title a");
      if (link) link.focus({ preventScroll: false });
    }
    if (window.NajaAnimations && NajaAnimations.refresh) NajaAnimations.refresh();
  }

  searchEl.addEventListener("input", function () {
    state.query = searchEl.value;
    state.shown = pageSize;
    render();
  });
  searchEl.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && searchEl.value) { searchEl.value = ""; state.query = ""; render(); }
  });
  clearEl.addEventListener("click", function () {
    searchEl.value = ""; state.query = ""; state.shown = pageSize; render(); searchEl.focus();
  });

  catsEl.addEventListener("click", function (e) {
    var btn = e.target.closest("[data-cat]");
    if (!btn || btn.dataset.cat === state.cat) return;
    state.cat = btn.dataset.cat;
    state.shown = pageSize;
    render();
  });

  resetBtn.addEventListener("click", function () {
    searchEl.value = ""; state.query = ""; state.cat = "all"; state.shown = pageSize; render();
  });

  moreBtn.addEventListener("click", function () {
    var before = state.shown;
    state.shown += pageSize;
    render();
    var next = items.filter(function (li) { return !li.hidden; })[before];
    var link = next && next.querySelector(".bl-card__title a");
    if (link) link.focus();
  });

  document.addEventListener("naja:lang", function () { render(); });
  render();
})();
