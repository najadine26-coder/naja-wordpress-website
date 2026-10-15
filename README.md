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

## বিকল্প: GitHub Actions + FTP

`.github/workflows/deploy.yml` push-এর সময় FTP দিয়েও থিম আপলোড করতে পারে। এটা চালাতে GitHub → **Settings → Secrets and variables → Actions**-এ `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD` (আর দরকার হলে `FTP_SERVER_DIR`) দিন। Hostinger-এর Deploy from GitHub ব্যবহার করলে এটার দরকার নেই।

## ফাইল কাঠামো

```
style.css            থিমের তথ্য
functions.php        থিম সেটআপ, CSS/JS লোড
header.php, footer.php, index.php, page.php
front-page.php       হোমপেজ
page-templates/menu.php  মেনু পেজ (WhatsApp অর্ডার)
inc/                 customizer, dishes, SEO head, helpers
css/                 ডিজাইন
js/                  অ্যানিমেশন, অনুবাদ (translations.js), মেনু ডেটা
assets/              ফন্ট ও ছবি
```
