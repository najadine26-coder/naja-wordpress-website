/* =========================================================================
   NAJA RESTAURANT — MENU DATA
   -------------------------------------------------------------------------
   ALL ITEMS BELOW ARE SAMPLE PLACEHOLDERS. Names, descriptions, categories
   and prices are NOT the restaurant's verified menu. Replace them with the
   real menu once supplied, and set `placeholder: false` per item.

   price: number in SAR, or null to hide the price for that item.
   category: one of the ids in najaMenuCategories (except "all").
   image: optional key from najaImages. Only set it when that photo really
   shows the dish; items without one get a branded "photo coming soon" frame
   on the Menu page. Categories with no items are hidden from the filters.
   ========================================================================= */

/* `image` = illustrative preview shown when hovering a category (keys from najaImages). */
window.najaMenuCategories = [
  { id: "all", label: { ar: "الكل", en: "All" } },
  { id: "main", image: "presentation", label: { ar: "الأطباق الرئيسية", en: "Main Course" } },
  { id: "rice", image: "plated", label: { ar: "الأرز", en: "Rice" } },
  { id: "grills", image: "grill", label: { ar: "المشويات", en: "Grills" } },
  { id: "burgers", image: "burger", label: { ar: "البرجر", en: "Burgers" } },
  { id: "fastfood", image: "pizza", label: { ar: "الوجبات السريعة", en: "Fast Food" } },
  { id: "pasta", image: "pizza", label: { ar: "الباستا", en: "Pasta" } },
  { id: "beverages", image: "interior", label: { ar: "المشروبات", en: "Beverages" } },
  { id: "desserts", image: "hero", label: { ar: "الحلويات", en: "Desserts" } }
];

/* Section 02 — Signature dishes (sample placeholders). */
window.najaSignatureDishes = [
  {
    id: "sig-grill",
    image: "grill",
    name: { ar: "مشاوي مشكلة على الفحم", en: "Charcoal Mixed Grill" },
    desc: {
      ar: "تشكيلة من المشاوي على الفحم تُقدّم مع الخبز والصلصات.",
      en: "A selection of charcoal-grilled cuts served with bread and sauces."
    },
    price: 89,
    placeholder: true
  },
  {
    id: "sig-burger",
    image: "burger",
    name: { ar: "برجر نجا الكلاسيكي", en: "Naja Classic Burger" },
    desc: {
      ar: "شريحة لحم مشوية، جبن ذائب، خضار طازجة وخبز محمّص.",
      en: "Grilled beef patty, melted cheese, fresh greens and a toasted bun."
    },
    price: 42,
    placeholder: true
  },
  {
    id: "sig-pizza",
    image: "pizza",
    name: { ar: "بيتزا مارغريتا", en: "Margherita Pizza" },
    desc: {
      ar: "عجينة رقيقة، صلصة طماطم، موزاريلا وريحان.",
      en: "Thin crust, tomato sauce, mozzarella and basil."
    },
    price: 48,
    placeholder: true
  },
  {
    id: "sig-platter",
    image: "presentation",
    name: { ar: "طبق الشيف المميز", en: "Chef's Signature Platter" },
    desc: {
      ar: "طبق للمشاركة يجمع مختارات من المطبخ، مثالي للعائلة والأصدقاء.",
      en: "A sharing platter of kitchen favourites, made for family and friends."
    },
    price: 129,
    placeholder: true
  }
];

/* Section 03 — Digital menu (sample placeholders). */
window.najaMenuItems = [
  // Main Course
  { id: "m1", category: "main", price: 68, placeholder: true,
    name: { ar: "دجاج مشوي بالأعشاب", en: "Herb Roasted Chicken" },
    desc: { ar: "نصف دجاجة متبلة بالأعشاب مع خضار مشوية.", en: "Half chicken marinated in herbs with roasted vegetables." } },
  { id: "m2", category: "main", price: 95, placeholder: true, image: "presentation",
    name: { ar: "ستيك لحم مع صلصة الفلفل", en: "Beef Steak, Pepper Sauce" },
    desc: { ar: "شريحة لحم مشوية حسب الرغبة مع بطاطس.", en: "Grilled to your preference, served with potatoes." } },
  { id: "m3", category: "main", price: 79, placeholder: true,
    name: { ar: "سمك فيليه بالليمون", en: "Lemon Fish Fillet" },
    desc: { ar: "فيليه سمك مع صلصة الليمون والزبدة.", en: "Fish fillet with a lemon butter sauce." } },

  // Rice
  { id: "r1", category: "rice", price: 58, placeholder: true,
    name: { ar: "كبسة دجاج", en: "Chicken Kabsa" },
    desc: { ar: "أرز متبل بالبهارات مع دجاج ومكسرات.", en: "Spiced rice with chicken and toasted nuts." } },
  { id: "r2", category: "rice", price: 85, placeholder: true,
    name: { ar: "مندي لحم", en: "Lamb Mandi" },
    desc: { ar: "لحم مطهو ببطء على أرز المندي.", en: "Slow-cooked lamb over mandi rice." } },
  { id: "r3", category: "rice", price: 49, placeholder: true,
    name: { ar: "أرز بالخضار", en: "Vegetable Rice" },
    desc: { ar: "أرز بسمتي مع خضار موسمية.", en: "Basmati rice with seasonal vegetables." } },

  // Grills
  { id: "g1", category: "grills", price: 62, placeholder: true, image: "grill",
    name: { ar: "شيش طاووق", en: "Shish Tawook" },
    desc: { ar: "مكعبات دجاج متبلة مشوية على الفحم.", en: "Marinated chicken cubes grilled over charcoal." } },
  { id: "g2", category: "grills", price: 69, placeholder: true, image: "grill",
    name: { ar: "كباب لحم", en: "Lamb Kebab" },
    desc: { ar: "لحم مفروم متبل على أسياخ مع خبز.", en: "Seasoned minced lamb skewers with bread." } },
  { id: "g3", category: "grills", price: 89, placeholder: true, image: "grill",
    name: { ar: "مشاوي مشكلة", en: "Mixed Grill" },
    desc: { ar: "تشكيلة من الكباب والشيش والتكة.", en: "Kebab, shish and tikka selection." } },

  // Burgers
  { id: "f1", category: "burgers", price: 42, placeholder: true, image: "burger",
    name: { ar: "برجر كلاسيكي", en: "Classic Burger" },
    desc: { ar: "لحم، جبن، خس، طماطم وصلصة خاصة.", en: "Beef, cheese, lettuce, tomato and house sauce." } },
  { id: "f2", category: "burgers", price: 38, placeholder: true,
    name: { ar: "برجر دجاج مقرمش", en: "Crispy Chicken Burger" },
    desc: { ar: "صدر دجاج مقرمش مع صلصة المايونيز.", en: "Crispy chicken breast with mayo sauce." } },

  // Fast Food
  { id: "f3", category: "fastfood", price: 48, placeholder: true, image: "pizza",
    name: { ar: "بيتزا مارغريتا", en: "Margherita Pizza" },
    desc: { ar: "صلصة طماطم وموزاريلا وريحان.", en: "Tomato sauce, mozzarella and basil." } },
  { id: "f4", category: "fastfood", price: 18, placeholder: true,
    name: { ar: "بطاطس مقلية", en: "French Fries" },
    desc: { ar: "بطاطس ذهبية مقرمشة.", en: "Golden, crispy fries." } },

  // Pasta
  { id: "p1", category: "pasta", price: 52, placeholder: true,
    name: { ar: "فيتوتشيني ألفريدو", en: "Fettuccine Alfredo" },
    desc: { ar: "صلصة كريمية بالجبن مع دجاج.", en: "Creamy cheese sauce with chicken." } },
  { id: "p2", category: "pasta", price: 46, placeholder: true,
    name: { ar: "بيني أرابياتا", en: "Penne Arrabbiata" },
    desc: { ar: "صلصة طماطم حارة بالثوم.", en: "Spicy tomato and garlic sauce." } },

  // Beverages
  { id: "b1", category: "beverages", price: 16, placeholder: true,
    name: { ar: "عصير برتقال طازج", en: "Fresh Orange Juice" },
    desc: { ar: "برتقال معصور عند الطلب.", en: "Squeezed to order." } },
  { id: "b2", category: "beverages", price: 18, placeholder: true,
    name: { ar: "ليمون بالنعناع", en: "Lemon Mint" },
    desc: { ar: "ليمون منعش مع نعناع طازج.", en: "Refreshing lemon with fresh mint." } },
  { id: "b3", category: "beverages", price: 12, placeholder: true,
    name: { ar: "قهوة عربية", en: "Arabic Coffee" },
    desc: { ar: "تُقدّم مع التمر.", en: "Served with dates." } },

  // Desserts
  { id: "d1", category: "desserts", price: 32, placeholder: true,
    name: { ar: "كنافة بالجبن", en: "Cheese Kunafa" },
    desc: { ar: "كنافة ذهبية محشوة بالجبن مع القطر.", en: "Golden kunafa filled with cheese, finished with syrup." } },
  { id: "d2", category: "desserts", price: 28, placeholder: true,
    name: { ar: "كيكة الشوكولاتة", en: "Chocolate Cake" },
    desc: { ar: "كيكة شوكولاتة غنية.", en: "Rich chocolate cake." } },
  { id: "d3", category: "desserts", price: 26, placeholder: true,
    name: { ar: "أم علي", en: "Om Ali" },
    desc: { ar: "حلى دافئ بالحليب والمكسرات.", en: "Warm pastry pudding with milk and nuts." } }
];
