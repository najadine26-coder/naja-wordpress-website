/* =========================================================================
   NAJA RESTAURANT — GALLERY PAGE DATA
   -------------------------------------------------------------------------
   Loaded only on the Gallery page (after config.js, before main.js).

   PHOTOS
   Most photos are Unsplash placeholders (as in config.js) — they are NOT
   photographs of Naja Restaurant. The only real Naja photo is the sign
   (logoSign). Replace a placeholder by changing its `src`, e.g.
   src: "assets/images/grill-01.jpg" (files go in assets/images/), and keep
   the alt text honest. Unsplash URLs automatically receive responsive srcsets.
   A photo that fails to load is dropped from the grid instead of showing a
   broken tile.

   ENTRIES
   image    key in window.najaImages (existing keys from config.js work too)
   category id from najaGalleryCategories (not "all")
   shape    "tall" (3:4) | "square" (1:1) | "wide" (4:3) — sets the tile ratio
   caption  { ar, en }
   ========================================================================= */

(function () {
  "use strict";

  var images = (window.najaImages = window.najaImages || {});
  var unsplash = function (id) {
    return "https://images.unsplash.com/photo-" + id + "?auto=format&fit=crop&w=1400&q=80";
  };
  var add = function (key, id, alt, position) {
    images[key] = { src: unsplash(id), alt: alt, position: position || "center" };
  };

  /* Extra placeholder photos (the ones from config.js — hero, grill, burger,
     pizza, interior, presentation, plated — are reused as they are). */
  add("galDish", "1414235077428-338989a2e8c0",
    { ar: "طبق رئيسي مقدم بعناية على صحن أبيض", en: "A main course carefully plated on a white dish" });
  add("galHerbs", "1504674900247-0877df9cc836",
    { ar: "طبق لحم مع أعشاب وخضار طازجة", en: "A meat dish with fresh herbs and vegetables" });
  add("galPasta", "1473093295043-cdd812d0e601",
    { ar: "طبق باستا بالطماطم والريحان", en: "A plate of pasta with tomato and basil" });
  add("galCake", "1578985545062-69928b1d9587",
    { ar: "قطعة كيك شوكولاتة", en: "A slice of chocolate cake" });
  add("galDonuts", "1551024601-bec78aea704b",
    { ar: "دونات مزيّنة بالألوان", en: "Colourfully decorated donuts" });
  add("galPancakes", "1567620905732-2d1ec7ab7445",
    { ar: "بان كيك مع التوت والشراب", en: "Pancakes with berries and syrup" });
  add("galRoom", "1555396273-367ea4eb4db5",
    { ar: "صالة طعام بإضاءة دافئة", en: "A dining room with warm lighting" });
  add("galTables", "1559339352-11d035aa65de",
    { ar: "طاولات مطعم مُعدّة للضيوف", en: "Restaurant tables set for guests" });

  window.najaGalleryCategories = [
    { id: "all", label: { ar: "الكل", en: "All" } },
    { id: "food", label: { ar: "الأطباق", en: "Food" } },
    { id: "signature", label: { ar: "الأطباق المميزة", en: "Signature Dishes" } },
    { id: "desserts", label: { ar: "الحلويات", en: "Desserts" } },
    { id: "restaurant", label: { ar: "المطعم", en: "Restaurant" } }
  ];

  window.najaGalleryPage = [
    { image: "hero", category: "signature", shape: "wide", caption: { ar: "ألوان المائدة", en: "Colours of the table" } },
    { image: "interior", category: "restaurant", shape: "tall", caption: { ar: "أجواء الصالة", en: "The dining room" } },
    { image: "galCake", category: "desserts", shape: "square", caption: { ar: "حلوى الشوكولاتة", en: "Chocolate indulgence" } },
    { image: "grill", category: "signature", shape: "tall", caption: { ar: "من الشواية", en: "From the grill" } },
    { image: "galPasta", category: "food", shape: "square", caption: { ar: "باستا طازجة", en: "Fresh pasta" } },
    { image: "logoSign", category: "restaurant", shape: "square", caption: { ar: "لافتة نجا المضيئة", en: "Naja's illuminated sign" } },
    { image: "galPancakes", category: "desserts", shape: "wide", caption: { ar: "بان كيك بالتوت", en: "Pancakes with berries" } },
    { image: "burger", category: "signature", shape: "square", caption: { ar: "كلاسيكيات مفضلة", en: "Favourite classics" } },
    { image: "galRoom", category: "restaurant", shape: "wide", caption: { ar: "مكان دافئ للجلوس", en: "A warm place to sit" } },
    { image: "presentation", category: "signature", shape: "tall", caption: { ar: "على لوح التقديم", en: "On the board" } },
    { image: "pizza", category: "food", shape: "square", caption: { ar: "من الفرن", en: "From the oven" } },
    { image: "galDonuts", category: "desserts", shape: "square", caption: { ar: "حلويات للمشاركة", en: "Sweets to share" } },
    { image: "galDish", category: "food", shape: "wide", caption: { ar: "تقديم أنيق", en: "Elegant plating" } },
    { image: "galTables", category: "restaurant", shape: "tall", caption: { ar: "طاولات جاهزة لضيوفنا", en: "Tables set for guests" } },
    { image: "plated", category: "food", shape: "square", caption: { ar: "تقديم بعناية", en: "Thoughtful plating" } },
    { image: "galHerbs", category: "food", shape: "wide", caption: { ar: "أعشاب وخضار طازجة", en: "Fresh herbs and vegetables" } }
  ];
})();
