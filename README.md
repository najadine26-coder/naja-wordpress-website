# Naja WordPress Website

Naja Dine Agency-র কাস্টম WordPress থিম (`naja-theme`)। এই repository Hostinger Git-এর মাধ্যমে সরাসরি WordPress সাইটে deploy হয়।

## Hostinger Git-এর সাথে connect করার তথ্য

| ঘর | যা দেবেন |
|---|---|
| Repository | `https://github.com/najadine26-coder/naja-wordpress-website.git` |
| Branch | `main` |
| Directory | `wp-content/themes/naja-theme` |

## ধাপগুলো

1. Hostinger **hPanel** এ লগইন করুন → আপনার website → **Advanced → GIT**।
2. **Create a New Repository** অংশে উপরের টেবিলের তিনটা তথ্য দিন, তারপর **Create** চাপুন।
   - Directory অবশ্যই খালি (নতুন) হতে হবে। `wp-content/themes/naja-theme` ফোল্ডারটা আগে থেকে না থাকলে Hostinger নিজেই বানিয়ে নেবে।
   - Repository টা public, তাই কোনো SSH key লাগবে না।
3. Repository লিস্টে **Deploy** চাপুন। ফাইলগুলো থিম ফোল্ডারে চলে আসবে।
4. WordPress admin → **Appearance → Themes** থেকে **Naja Theme** activate করুন।

## Auto deploy (webhook ছাড়া, GitHub Actions + FTP)

`main` branch-এ push করলেই `.github/workflows/deploy.yml` থিমটা FTP দিয়ে Hostinger-এ আপলোড করে দেয়। এই পদ্ধতিতে hPanel-এর GIT অংশ বা webhook কোনোটাই লাগে না।

একবার শুধু GitHub-এ FTP-র তথ্য দিতে হবে:

1. hPanel → **Files → FTP Accounts** থেকে FTP তথ্য দেখুন (দরকার হলে পাসওয়ার্ড বদলে নিন)।
2. GitHub → এই repository → **Settings → Secrets and variables → Actions → New repository secret**। নিচের secret গুলো যোগ করুন:

| Secret নাম | মান |
|---|---|
| `FTP_SERVER` | FTP IP বা hostname (যেমন `ftp.yourdomain.com`) |
| `FTP_USERNAME` | FTP username |
| `FTP_PASSWORD` | FTP password |
| `FTP_SERVER_DIR` | (ঐচ্ছিক) থিম ফোল্ডারের পথ, শেষে `/` সহ। না দিলে `public_html/wp-content/themes/naja-theme/` ধরা হবে। |

3. GitHub → **Actions → Deploy to Hostinger → Run workflow** চাপুন (অথবা যেকোনো push করুন)। সবুজ টিক আসলে deploy সফল।

## ফাইল কাঠামো

```
style.css          থিমের তথ্য (নাম, ভার্সন)
functions.php      থিম সেটআপ, মেনু, CSS লোড
header.php         সাইটের উপরের অংশ
footer.php         সাইটের নিচের অংশ
index.php          মূল টেমপ্লেট
assets/css/main.css  ডিজাইন
```
