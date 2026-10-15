# Naja WordPress Website

Naja Restaurant-এর WordPress থিম (`naja-restaurant`)। আরবি (RTL) প্রধান, ইংরেজিতে বদলানো যায়। এই repository-র মূল ফোল্ডারটাই থিম, আর `main` branch থেকে Hostinger-এ deploy হয়।

থিমের বিস্তারিত (Customizer, Dishes, SEO) `readme.txt`-এ আছে।

## Hostinger-এ deploy (Deploy from GitHub)

hPanel → website → **Deploy from GitHub**:

| ঘর | যা দেবেন |
|---|---|
| Repository | `naja-wordpress-website` |
| Branch | `main` |
| Directory | `public_html/wp-content/themes/naja-restaurant` |

> ⚠️ Directory `public_html` রাখবেন না, তাহলে থিমের ফাইল সাইটের মূল ফোল্ডারে গিয়ে WordPress ভেঙে যেতে পারে।

Deploy শেষে:
1. WordPress admin → **Appearance → Themes** → **Naja Restaurant** activate করুন।
2. **Settings → Permalinks** → "Post name" বেছে সেভ করুন।
3. LiteSpeed Cache থাকলে সব cache purge করুন।

## GitHub Sync (WordPress-এর ভেতরে)

WordPress admin → **Appearance → GitHub Sync**:

- **সাইটে আছে** আর **GitHub-এ সর্বশেষ** commit পাশাপাশি দেখায়: commit নম্বর (`#4 · a1b2c3d`), বার্তা আর সময়।
- নতুন commit এলে জানায়:
  - wp-admin-এর সব পাতায় হলুদ নোটিশ "🔔 থিমের নতুন আপডেট এসেছে"
  - উপরের admin bar-এ "🔔 থিম আপডেট (২)"
  - মেনুতে GitHub Sync-এর পাশে সংখ্যা
  - প্রতি ঘণ্টায় চেক করে, প্রতিটা নতুন commit-এর জন্য admin email-এ একবার ইমেইল পাঠায় (সেটিংস থেকে বন্ধ করা যায়)
- **এখনই Sync করুন**: GitHub থেকে সর্বশেষ commit নামিয়ে থিম ফোল্ডারটা বদলে দেয়।
- **এটাই সাইটে আছে**: সাইটে কোন commit আছে জানা না থাকলে (যেমন Hostinger দিয়ে deploy করার পর), সর্বশেষ commit-কে "সাইটে আছে" হিসেবে চিহ্নিত করে।
- Sync-এর পর CSS/JS-এর `?ver=` এ commit যোগ হয়, তাই ব্রাউজার পুরোনো ফাইল দেখায় না।

কোড: `inc/github-sync.php`।

## বিকল্প: GitHub Actions + FTP

`.github/workflows/deploy.yml` push-এর সময় FTP দিয়েও থিম আপলোড করতে পারে। এটা চালাতে GitHub → **Settings → Secrets and variables → Actions**-এ `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD` (আর দরকার হলে `FTP_SERVER_DIR`) দিন। Hostinger-এর Deploy from GitHub ব্যবহার করলে এটার দরকার নেই।

## ফাইল কাঠামো

```
style.css            থিমের তথ্য
functions.php        থিম সেটআপ, CSS/JS লোড
header.php, footer.php, index.php, page.php
front-page.php       হোমপেজ
page-templates/menu.php  মেনু পেজ (WhatsApp অর্ডার)
page-templates/contact.php  যোগাযোগ পেজ (ফর্ম: inc/contact.php, বার্তা admin-এর Messages মেনুতে)
page-templates/gallery.php  গ্যালারি পেজ (ফিল্টার + lightbox); ছবি ও ক্যাটেগরি js/gallery-data.js-এ
inc/                 customizer, dishes, SEO head, helpers
css/                 ডিজাইন (gallery.css শুধু গ্যালারি পেজে)
js/                  অ্যানিমেশন, অনুবাদ (translations.js), মেনু ডেটা
assets/              ফন্ট ও ছবি
```
