=== Naja Restaurant ===
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
License: GPLv2 or later

Arabic-first (RTL) restaurant theme with an English switch, built for Naja Restaurant, Dammam.

== Installation ==
1. Appearance → Themes → Add New → Upload Theme → choose naja-restaurant-theme.zip → Install → Activate.
2. On activation the theme creates a page called "القائمة" (slug: menu) that uses the "Naja Menu" template.
   The homepage design is used automatically for the site's front page.
3. Settings → Permalinks → "Post name" is recommended.
4. If a caching plugin is active (LiteSpeed Cache, etc.), purge all caches after installing or updating.

== Customizing ==
* Appearance → Customize → Naja Restaurant
  - Contact & location: WhatsApp, phone, street address (Arabic/English), Google Maps link.
  - Opening hours: one line per row, "Days | Time", in both languages.
  - Social profiles: Instagram, TikTok, Snapchat, X (empty = hidden).
  - Menu & prices: show or hide prices of sample dishes.
  - Photos: replace each placeholder photo (set "Alt text" in the Media Library).
* Dishes (admin menu): add the real menu. Title = Arabic name; fill in English name,
  descriptions, category, price; set the photo as Featured image; "Order" arranges dishes.
  Untick "Sample dish" once a dish is confirmed. Tick "Signature dish" (with a photo) to
  feature it on the homepage (first 4). As soon as one dish is published, it replaces the
  built-in sample menu everywhere.
* Text: all Arabic/English wording is in js/translations.js.

== SEO ==
The theme prints titles, descriptions, Open Graph and Restaurant/FAQ structured data.
If Rank Math, Yoast, AIOSEO or SEOPress is active, titles and descriptions are left to that plugin.

== Credits ==
* GSAP 3.12.5 + ScrollTrigger — GreenSock Standard License (https://gsap.com/standard-license)
* Fustat, Inter, Kaushan Script fonts — SIL Open Font License 1.1 (see assets/fonts)
* Placeholder photos load from Unsplash until replaced in the Customizer.
