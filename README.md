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

## Auto deploy (প্রতি push-এ নিজে থেকে আপডেট)

1. hPanel → GIT → repository-র পাশে **Auto Deployment** চাপুন, সেখানে একটা **Webhook URL** দেখাবে। সেটা কপি করুন।
2. GitHub → এই repository → **Settings → Webhooks → Add webhook**।
3. **Payload URL** এ ওই webhook URL পেস্ট করুন, বাকি সব default রেখে **Add webhook** চাপুন।

এরপর `main` branch-এ push করলেই সাইটে থিম আপডেট হয়ে যাবে।

## ফাইল কাঠামো

```
style.css          থিমের তথ্য (নাম, ভার্সন)
functions.php      থিম সেটআপ, মেনু, CSS লোড
header.php         সাইটের উপরের অংশ
footer.php         সাইটের নিচের অংশ
index.php          মূল টেমপ্লেট
assets/css/main.css  ডিজাইন
```
