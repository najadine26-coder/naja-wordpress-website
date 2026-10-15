/* =========================================================================
   NAJA RESTAURANT — CENTRAL CONFIGURATION
   -------------------------------------------------------------------------
   Edit this file to update business details and imagery.
   IMPORTANT: Leave a value as "" until it has been verified by the
   restaurant. Empty values automatically hide or disable the related
   buttons (WhatsApp, Call, Directions, mobile bottom bar, social links)
   and are left out of the SEO structured data.
   ========================================================================= */

window.restaurantConfig = {
  name: "Naja Restaurant",
  nameAr: "مطعم نجا", // TODO: confirm the official Arabic spelling with the restaurant
  city: "Dammam",
  cityAr: "الدمام",
  country: "SA",
  cuisine: "Mixed Menu",
  slogan: "Feel Fresh", // from the logo
  currency: "SAR",
  defaultLanguage: "ar", // "ar" | "en"

  // Public site URL (used for canonical / Open Graph / JSON-LD). Example: "https://naja.sa"
  siteUrl: "",

  // ---- Contact details (NOT YET SUPPLIED — keep empty until verified) ----
  // Phone in international format, digits only or with +, e.g. "+9665XXXXXXXX"
  phone: "+966583815633", // same number as WhatsApp (confirmed by the restaurant)
  // WhatsApp number in international format WITHOUT "+" or spaces, e.g. "9665XXXXXXXX"
  whatsapp: "966583815633", // +966 58 381 5633 (supplied by the restaurant)
  // Street address in both languages
  address: { ar: "", en: "" },
  // Full Google Maps link to the restaurant (share link from Google Maps)
  // Currently a search for the city; replace with the restaurant's exact Google Maps pin link.
  googleMapsUrl: "https://www.google.com/maps/search/?api=1&query=Dammam%2C%20Saudi%20Arabia",
  // Contact page map: the https://www.google.com/maps/embed?... address of the restaurant pin
  // (Google Maps → Share → Embed a map → copy the iframe src). Empty = a map placeholder is shown.
  mapsEmbedUrl: "",

  // Opening hours. Example entry:
  // { days: { ar: "السبت – الخميس", en: "Saturday – Thursday" }, time: { ar: "١٢:٠٠ م – ١٢:٠٠ ص", en: "12:00 PM – 12:00 AM" } }
  // For JSON-LD you can also add: schema: "Sa-Th 12:00-24:00"
  openingHours: [],

  // Verified social profile URLs only — empty entries are hidden.
  social: {
    instagram: "",
    tiktok: "",
    snapchat: "",
    x: ""
  },

  // Show sample prices on placeholder menu items. Set to false to hide
  // all prices until the real menu and prices are supplied.
  showSamplePrices: true
};

/* -------------------------------------------------------------------------
   IMAGE LIBRARY
   Remote placeholders (Unsplash) — NOT photographs of Naja Restaurant.
   Replace with the restaurant's own photography before launch, e.g.
   hero: { src: "assets/images/hero.jpg", ... }.
   Unsplash URLs automatically receive responsive srcsets.
   NOTE: the hero <img> in index.html also hard-codes the hero URL so the
   browser can fetch it with top priority before JavaScript runs — update
   both places when replacing the hero image.
   ------------------------------------------------------------------------- */
window.najaImages = {
  // The restaurant's own logo sign (supplied by the client).
  logoSign: {
    src: "assets/images/naja-sign.webp",
    alt: { ar: "لافتة مطعم نجا المضيئة: شعار الديك وعبارة Feel Fresh", en: "Naja's illuminated sign: the rooster logo and the tagline Feel Fresh" },
    position: "center"
  },
  hero: {
    src: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=2200&q=85",
    alt: { ar: "طبق طعام ملون وطازج مقدم بأناقة", en: "A colourful, freshly prepared dish presented elegantly" },
    position: "center 45%"
  },
  grill: {
    src: "https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=1100&q=85",
    alt: { ar: "أسياخ لحم مشوية على الفحم", en: "Skewers of meat grilling over charcoal" },
    position: "center"
  },
  burger: {
    src: "https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=1100&q=85",
    alt: { ar: "برجر لحم مع الجبن والخضار", en: "A beef burger with cheese and fresh vegetables" },
    position: "center"
  },
  pizza: {
    src: "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=1100&q=85",
    alt: { ar: "بيتزا خارجة من الفرن بالجبن الذائب", en: "A freshly baked pizza with melted cheese" },
    position: "center"
  },
  interior: {
    src: "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1600&q=85",
    alt: { ar: "صالة مطعم بإضاءة دافئة وطاولات مُعدّة", en: "A warmly lit restaurant dining room with set tables" },
    position: "center"
  },
  presentation: {
    src: "https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1100&q=85",
    alt: { ar: "طبق لحم مقدم على لوح تقديم", en: "A meat dish served on a wooden board" },
    position: "center"
  },
  plated: {
    src: "https://images.unsplash.com/photo-1515003197210-e0cd7184f8b5?auto=format&fit=crop&w=1100&q=85",
    alt: { ar: "طبق مقدم بعناية على طاولة داكنة", en: "A carefully plated dish on a dark table" },
    position: "center"
  }
};

/* Gallery order and layout. `shape` controls the asymmetric composition:
   "tall" | "wide" | "square" | "feature". */
window.najaGallery = [
  { image: "hero", shape: "feature", caption: { ar: "ألوان المائدة", en: "Colours of the table" } },
  { image: "interior", shape: "wide", caption: { ar: "أجواء الصالة", en: "The dining room" } },
  { image: "grill", shape: "tall", caption: { ar: "من الشواية", en: "From the grill" } },
  { image: "plated", shape: "square", caption: { ar: "تقديم بعناية", en: "Thoughtful plating" } },
  { image: "pizza", shape: "square", caption: { ar: "من الفرن", en: "From the oven" } },
  { image: "presentation", shape: "wide", caption: { ar: "على لوح التقديم", en: "On the board" } },
  { image: "burger", shape: "tall", caption: { ar: "كلاسيكيات مفضلة", en: "Favourite classics" } }
];

/* Menu page — featured food gallery (6 images). Same shapes as above. */
window.najaMenuGallery = [
  { image: "hero", shape: "feature", caption: { ar: "ألوان المائدة", en: "Colours of the table" } },
  { image: "grill", shape: "tall", caption: { ar: "من الشواية", en: "From the grill" } },
  { image: "burger", shape: "square", caption: { ar: "البرجر والكلاسيكيات", en: "Burgers and classics" } },
  { image: "pizza", shape: "square", caption: { ar: "من الفرن", en: "From the oven" } },
  { image: "presentation", shape: "wide", caption: { ar: "أطباق للمشاركة", en: "Made for sharing" } },
  { image: "plated", shape: "wide", caption: { ar: "تقديم بعناية", en: "Thoughtful plating" } }
];
