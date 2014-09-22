<?php
/**
 * MyBB-Tags 2 Persian Language Pack
 * Copyright 2014 My-BB.Ir Group, All Rights Reserved
 * 
 * Author: AliReza_Tofighi - http://my-bb.ir
 *
 */


$l['tags_pluginname'] = "کلمات کلیدی";

// Settings
$l['setting_group_tags'] = 'پلاگین کلمات کلیدی';
$l['setting_group_tags_desc'] = "تنظیمات برای پلاگین کلمات‌کلیدی.";

$l['setting_tags_enabled'] = "فعال‌سازی پلاگین؟";
$l['setting_tags_enabled_decs'] = 'اگر می‌خواهید این پلاگین را فعال‌سازید بر روی «روشن» قرار دهید.';
$l['setting_tags_seo'] = "آدرس‌های دوستانه برای SEO";
$l['setting_tags_seo_desc'] = 'آیا شما می‌خواهید که از آدرس‌های دوستانه برای موتور‌های جتسجو (مثل: tags-***.html) برای کلمات کلیدی استفاده شود؟<br />
به‌یاد داشته باشید که قبل از فعال‌سازی این پلاگین باید کد‌های زیر را در فایل ".htaccess" قرار دهید:
<pre style="background: #f7f7f7;border: 1px solid #ccc;padding: 6px;border-radius: 3px;direction: ltr;text-align: left;font-size: 12px;">
RewriteEngine <strong>on</strong>
RewriteRule <strong>^tag-(.*?)\.html$ tag.php?name=$1</strong> <em>[L,QSA]</em>
RewriteRule <strong>^tag\.html$ tag.php</strong> <em>[L,QSA]</em>
</pre>';
$l['setting_tags_per_page'] = "کلمات‌کلیدی در هر صفحه";
$l['setting_tags_per_page_desc'] = 'چه تعداد کلمه‌ی کلیدی در هر صفحه از صفحه‌ی کلمات کلیدی نمایش داده شود؟';
$l['setting_tags_limit'] = 'محدودیت تعداد کلمات کلیدی برای "صفحه‌ی نخست" و "صفحه‌ی نمایش انجمن"';
$l['setting_tags_limit_desc'] = 'حداکثر چه تعداد کلمه‌ی کلیدی در «صفحه ی نخست» و «صفحه‌ی نمایش انجمن» نشان داده‌شوند؟';
$l['setting_tags_index'] = 'نمایش کلمات کلیدی در صفحه‌ی نخست';
$l['setting_tags_index_desc'] = 'آیا شما می‌خواهید که کلمات کلیدی در صفحه‌ی نخست نمایش داده شوند؟';
$l['setting_tags_forumdisplay'] = 'نمایش کلمات کلیدی در صفحه‌ی نمایش انجمن';
$l['setting_tags_forumdisplay_desc'] = 'آیا شما می‌خواهید که کلمات کلیدی در صفحه‌ی نمایش انجمن نمایش داده شوند؟';
$l['setting_tags_max_thread'] = 'حداکثر کلمه‌ی کلیدی برای یک موضوع';
$l['setting_tags_max_thread_desc'] = 'لطفا تعداد حداکثر کلمه‌ی کلیدی‌ای که هرفرد بتواند برای موضوعات وارد کند را وارد فرمائید. برای ایجاد نکردن محدودیت بر روی 0 قرار دهید.';