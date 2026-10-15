/* =========================================================================
   NAJA RESTAURANT — CONTACT FORM
   -------------------------------------------------------------------------
   Loaded only on the Contact page, after main.js. Validates the form in the
   visitor's language, posts it to WordPress (inc/contact.php, action
   "naja_contact") and shows the real result: success only when the server
   says the message was received.

   Uses the existing language system: window.Naja.t() for text and the
   "naja:lang" event (fired by main.js) to refresh messages after a switch.
   ========================================================================= */

(function () {
  "use strict";

  var form = document.getElementById("contactForm");
  if (!form || !window.Naja) return;

  var Naja = window.Naja;
  var t = function (key, vars) { return Naja.t(key, vars); };
  var url = (window.najaContact && window.najaContact.ajaxUrl) || form.getAttribute("action");

  var status = document.getElementById("ctStatus");
  var summary = document.getElementById("ctSummary");
  var submit = document.getElementById("ctSubmit");
  var label = submit.querySelector(".ct-submit__label");
  var langField = document.getElementById("ctLang");

  var fields = {
    name: document.getElementById("ctName"),
    phone: document.getElementById("ctPhone"),
    email: document.getElementById("ctEmail"),
    subject: document.getElementById("ctSubject"),
    message: document.getElementById("ctMessage")
  };
  var errorEls = {
    name: document.getElementById("ctNameErr"),
    phone: document.getElementById("ctPhoneErr"),
    email: document.getElementById("ctEmailErr"),
    subject: document.getElementById("ctSubjectErr"),
    message: document.getElementById("ctMessageErr")
  };

  var errors = {};        // field -> error code ("required", "phone", ...)
  var banner = null;      // { type: "ok" | "error", key: "ct.ok" }
  var submitting = false;
  var token = makeToken();

  function makeToken() {
    if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
    return "t" + Date.now().toString(36) + Math.random().toString(36).slice(2, 10);
  }

  // Arabic-Indic digits -> Latin, so "٠٥٨…" passes the same phone check
  function latinDigits(str) {
    return String(str).replace(/[٠-٩]/g, function (d) { return String(d.charCodeAt(0) - 0x0660); });
  }

  /* ---------- Validation (mirrors the server rules in inc/contact.php) ---------- */
  function check(key) {
    var v = fields[key].value.trim();
    switch (key) {
      case "name":
        return v ? "" : "required";
      case "phone": {
        if (!v) return "required";
        var p = latinDigits(v), digits = p.replace(/\D+/g, "");
        return /^[0-9+\-()\s.]{7,25}$/.test(p) && digits.length >= 7 && digits.length <= 15 ? "" : "phone";
      }
      case "email":
        return !v || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? "" : "email";
      case "subject":
        return v ? "" : "required";
      case "message":
        if (!v) return "required";
        if (v.length < 10) return "short";
        return v.length > 2000 ? "long" : "";
    }
    return "";
  }

  function paintField(key) {
    var code = errors[key], el = errorEls[key], input = fields[key];
    var field = input.closest(".ct-field");
    if (code) {
      el.textContent = t("ct.err." + code);
      el.hidden = false;
      input.setAttribute("aria-invalid", "true");
      field.classList.add("is-invalid");
    } else {
      el.hidden = true;
      el.textContent = "";
      input.removeAttribute("aria-invalid");
      field.classList.remove("is-invalid");
    }
  }

  function paintAll() { Object.keys(fields).forEach(paintField); }

  function validateAll() {
    errors = {};
    Object.keys(fields).forEach(function (key) {
      var code = check(key);
      if (code) errors[key] = code;
    });
    paintAll();
    return Object.keys(errors).length === 0;
  }

  /* ---------- Messages ---------- */
  function paintBanner() {
    if (!banner) { status.hidden = true; status.textContent = ""; status.className = "ct-status"; return; }
    status.textContent = t(banner.key);
    status.className = "ct-status ct-status--" + banner.type;
    status.hidden = false;
  }

  function paintSummary(show) {
    summary.hidden = !show;
    summary.textContent = show ? t("ct.errSummary") : "";
  }

  function setBanner(type, key) {
    banner = { type: type, key: key };
    paintBanner();
    status.focus({ preventScroll: false });
  }

  function setBusy(busy) {
    submitting = busy;
    submit.disabled = busy;
    submit.classList.toggle("is-busy", busy);
    if (busy) submit.setAttribute("aria-busy", "true"); else submit.removeAttribute("aria-busy");
    label.textContent = t(busy ? "ct.sending" : "ct.send");
  }

  /* ---------- Events ---------- */
  Object.keys(fields).forEach(function (key) {
    var input = fields[key];
    input.addEventListener("blur", function () {
      if (!input.value.trim() && !errors[key]) return; // don't nag about untouched fields
      var code = check(key);
      if (code) errors[key] = code; else delete errors[key];
      paintField(key);
    });
    input.addEventListener("input", function () {
      if (!errors[key]) return;
      var code = check(key);
      if (code) errors[key] = code; else delete errors[key];
      paintField(key);
      if (!Object.keys(errors).length) paintSummary(false);
    });
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (submitting) return;
    banner = null;
    paintBanner();

    if (!validateAll()) {
      paintSummary(true);
      var first = Object.keys(fields).filter(function (k) { return errors[k]; })[0];
      if (first) fields[first].focus();
      return;
    }
    paintSummary(false);

    var data = new FormData(form);
    data.set("phone", latinDigits(fields.phone.value.trim()));
    data.set("token", token);
    data.set("lang", Naja.state.lang);

    setBusy(true);
    var controller = window.AbortController ? new AbortController() : null;
    var timer = setTimeout(function () { if (controller) controller.abort(); }, 20000);

    fetch(url, { method: "POST", body: data, credentials: "same-origin", signal: controller ? controller.signal : undefined })
      .then(function (res) { return res.json().catch(function () { return null; }); })
      .then(function (json) {
        if (json && json.success) {
          form.reset();
          errors = {};
          paintAll();
          token = makeToken(); // a new message gets a new token
          setBanner("ok", "ct.ok");
          return;
        }
        if (json && json.code === "invalid" && json.fields) {
          errors = {};
          Object.keys(json.fields).forEach(function (k) { if (fields[k]) errors[k] = json.fields[k]; });
          paintAll();
          paintSummary(true);
          var firstBad = Object.keys(fields).filter(function (k) { return errors[k]; })[0];
          if (firstBad) fields[firstBad].focus();
          return;
        }
        var key = { rate: "ct.failRate", too_fast: "ct.failFast" }[json && json.code] || "ct.failServer";
        setBanner("error", key);
      })
      .catch(function () { setBanner("error", "ct.failNetwork"); })
      .then(function () { clearTimeout(timer); setBusy(false); });
  });

  // Language switch: refresh every message that is currently on screen
  document.addEventListener("naja:lang", function (e) {
    langField.value = e.detail.lang;
    paintAll();
    paintBanner();
    paintSummary(!summary.hidden);
    label.textContent = t(submitting ? "ct.sending" : "ct.send");
  });
  langField.value = Naja.state.lang;
})();
