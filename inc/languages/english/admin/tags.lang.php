<?php
/**
 * MyBB-Tags 1.8 English Language Pack
 * Copyright 2014 My-BB.Ir Group, All Rights Reserved
 * 
 * Author: AliReza_Tofighi - http://my-bb.ir
 *
 */


$l['tags_pluginname'] = "Tags";

// Settings
$l['setting_group_tags'] = 'Tags Plugin';
$l['setting_group_tags_desc'] = "Settings for Tags Plugin.";

$l['setting_tags_enabled'] = "Enable Plugin?";
$l['setting_tags_enabled_decs'] = 'Set "on" if you want Enable this plugin.';
$l['setting_tags_seo'] = "SEO Friendly URL";
$l['setting_tags_seo_desc'] = 'Do you want to use SEO URLs (ex: tags-***.html) for tags?<br />
You must add these codes to ".htaccess" file before set it "On":
<pre style="background: #f7f7f7;border: 1px solid #ccc;padding: 6px;border-radius: 3px;direction: ltr;text-align: left;font-size: 12px;">
RewriteEngine <strong>on</strong>
RewriteRule <strong>^tag-(.*?)\.html$ tag.php?name=$1</strong> <em>[L,QSA]</em>
RewriteRule <strong>^tag\.html$ tag.php</strong> <em>[L,QSA]</em>
</pre>';
$l['setting_tags_per_page'] = "Tags per page";
$l['setting_tags_per_page_desc'] = 'How many tags shown in "Tags" page?';
$l['setting_tags_limit'] = 'Limit Tags in "Index Page" and "Forum Display Page"';
$l['setting_tags_limit_desc'] = 'How many tags shown in "Index Page" and "Forum Display Page" ?';
$l['setting_tags_index'] = 'Show tags in Index Page?';
$l['setting_tags_index_desc'] = 'Do you want tags shown in Index Page?';
$l['setting_tags_forumdisplay'] = 'Show tags in "Forum Display" Page?';
$l['setting_tags_forumdisplay_desc'] = 'Do you want tags shown in "Forum Display" Page?';

