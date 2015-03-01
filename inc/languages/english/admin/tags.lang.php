<?php
/**
 * MyBB-Tags 2 English Language Pack
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
$l['setting_tags_enabled_desc'] = 'Set "on" if you want Enable this plugin.';
$l['setting_tags_seo'] = "SEO Friendly URL";
$l['setting_tags_seo_desc'] = 'Do you want to use SEO URLs (ex: tag-***.html) for tags?<br />
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
$l['setting_tags_max_thread'] = 'Maximun tags for a thread';
$l['setting_tags_max_thread_desc'] = 'Please enter the maximum number of tags for threads. Set it to 0 for unlimited.';
$l['setting_tags_groups'] = 'Tags Moderators';
$l['setting_tags_groups_desc'] = 'Please select the groups can edit "tags". please note who can edit tags, that can edit thread.';
$l['setting_tags_bad'] = 'Bad Tags';
$l['setting_tags_bad_desc'] = 'Please enter the bad tags, this tags don\'t shown in tags list. enter each tags in new line';
$l['setting_tags_droptable'] = 'Drop table?';
$l['setting_tags_droptable_desc'] = 'Do you want the "tags" table droped when you uninstall this plugin?';
$l['setting_tags_maxchars'] = 'Maximum tag length';
$l['setting_tags_maxchars_desc'] = 'Please enter the maximum length that a tag can have';
$l['setting_tags_minchars'] = 'Minimum tag length';
$l['setting_tags_minchars_desc'] = 'Please enter the minimum length that a tag can have';
$l['setting_tags_charreplace'] = 'Character Translation';
$l['setting_tags_charreplace_desc'] = 'If you want translate some characters to other characters, you can use this setting.<br />
For example if you want replace "a" to "b" and "c" to "d" use this code:<br />
<pre style="background: #f7f7f7;border: 1px solid #ccc;padding: 6px;border-radius: 3px;direction: ltr;text-align: left;font-size: 12px;">
a=>b
c=>d
</pre>';
$l['settings_tags_disallowedforums'] = 'Disallowed forums';
$l['settings_tags_disallowedforums_desc'] = 'Please select the forums you want "Tags" don\'t work on these.';
$l['setting_tags_forceseo'] = 'Force users to use seo URLs?';
$l['setting_tags_forceseo_desc'] = 'Do you want to force users to use SEO URLs (ex: tag-***.html) for tags?';
$l['setting_tags_urlscheme'] = 'Tags URL scheme';
$l['setting_tags_urlscheme_desc'] = 'Enter the Tag URL scheme. By default this is tag-{name}.html. Please note that if you change this, you will also need to add a new rewrite rule in your .htaccess file.';