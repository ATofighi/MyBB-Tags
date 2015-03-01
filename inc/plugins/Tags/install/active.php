<?php

function tags_activate()
{
	global $mybb, $db;
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	
	// add settings
	
	$query = $db->simple_select('settinggroups', 'gid', "name='tags'");
	$gid = $db->fetch_field($query, 'gid');

	$i = 0;
	$settings = array(
		array(
			"name"			=> "tags_enabled",
			"title"			=> "Enable Plugin",
			"description"	=> $db->escape_string('Set to "on" if you want Enable this plugin.'),
			"optionscode"	=> "onoff",
			"value"			=> tags_setting_value("tags_enabled", 1),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_droptable",
			"title"			=> $db->escape_string('Drop table?'),
			"description"	=> $db->escape_string('Do you want the "tags" table droped when you uninstall this plugin?'),
			"optionscode"	=> "yesno",
			"value"			=> tags_setting_value("tags_droptable", 1),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_seo",
			"title"			=> "SEO Friendly URL",
			"description"	=> $db->escape_string('Do you want to use SEO URLs (ex: tag-***.html) for tags?<br />
You must add these codes to ".htaccess" file before set it to "On":
<pre style="background: #f7f7f7;border: 1px solid #ccc;padding: 6px;border-radius: 3px;direction: ltr;text-align: left;font-size: 12px;">
RewriteEngine <strong>on</strong>
RewriteRule <strong>^tag-(.*?)\.html$ tag.php?name=$1</strong> <em>[L,QSA]</em>
RewriteRule <strong>^tag\.html$ tag.php</strong> <em>[L,QSA]</em>
</pre>'),
			"optionscode"	=> "yesno",
			"value"			=> tags_setting_value("tags_seo", 0),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_forceseo",
			"title"			=> "Force users to use seo URLs?",
			"description"	=> $db->escape_string('Do you want to force users to use SEO URLs (ex: tag-***.html) for tags?'),
			"optionscode"	=> "yesno",
			"value"			=> tags_setting_value("tags_forceseo", 0),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_urlscheme",
			"title"			=> "Tags URL scheme",
			"description"	=> $db->escape_string('Enter the Tag URL scheme. By default this is tag-{name}.html. Please note that if you change this, you will also need to add a new rewrite rule in your .htaccess file.'),
			"optionscode"	=> "text",
			"value"			=> tags_setting_value("tags_urlscheme", 'tag-{name}.html'),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_per_page",
			"title"			=> "Tags per page",
			"description"	=> $db->escape_string('How many tags shown in "Tags" page?'),
			"optionscode"	=> "text",
			"value"			=> tags_setting_value("tags_per_page", 10),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_limit",
			"title"			=> $db->escape_string('Limit Tags in "Index Page" and "Forum Display Page"'),
			"description"	=> $db->escape_string('How many tags shown in "Index Page" and "Forum Display Page" ?'),
			"optionscode"	=> "text",
			"value"			=> tags_setting_value("tags_limit", 50),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_index",
			"title"			=> $db->escape_string('Show tags in Index Page?'),
			"description"	=> $db->escape_string('Do you want tags shown in Index Page?'),
			"optionscode"	=> "yesno",
			"value"			=> tags_setting_value("tags_index", 1),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_forumdisplay",
			"title"			=> $db->escape_string('Show tags in "Forum Display" Page?'),
			"description"	=> $db->escape_string('Do you want tags shown in "Forum Display" Page?'),
			"optionscode"	=> "yesno",
			"value"			=> tags_setting_value("tags_forumdisplay", 1),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_max_thread",
			"title"			=> $db->escape_string('Maximun tags for a thread'),
			"description"	=> $db->escape_string('Please enter the maximum number of tags for threads. Set it to 0 for unlimited.'),
			"optionscode"	=> "text",
			"value"			=> tags_setting_value("tags_max_thread", 20),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_groups",
			"title"			=> $db->escape_string('Tags Moderators'),
			"description"	=> $db->escape_string('Please select the groups can edit "tags". please note who can edit tags, that can edit thread.'),
			"optionscode"	=> "groupselect",
			"value"			=> tags_setting_value("tags_groups", -1),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_bad",
			"title"			=> $db->escape_string('Bad Tags'),
			"description"	=> $db->escape_string('Please enter the bad tags, this tags don\'t shown in tags list. enter each tags in new line'),
			"optionscode"	=> "textarea",
			"value"			=> tags_setting_value("tags_bad", ''),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_maxchars",
			"title"			=> $db->escape_string('Maximum tag length'),
			"description"	=> $db->escape_string('Please enter the maximum length that a tag can have'),
			"optionscode"	=> "text",
			"value"			=> tags_setting_value("tags_maxchars", 20),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_minchars",
			"title"			=> $db->escape_string('Minimum tag length'),
			"description"	=> $db->escape_string('Please enter the minimum length that a tag can have'),
			"optionscode"	=> "text",
			"value"			=> tags_setting_value("tags_minchars", 0),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_charreplace",
			"title"			=> $db->escape_string('Character Translation'),
			"description"	=> $db->escape_string('If you want translate some characters to other characters, you can use this setting.<br />
For example if you want replace "a" to "b" and "c" to "d" use this code:<br />
<pre style="background: #f7f7f7;border: 1px solid #ccc;padding: 6px;border-radius: 3px;direction: ltr;text-align: left;font-size: 12px;">
a=>b
c=>d
</pre>'),
			"optionscode"	=> "textarea",
			"value"			=> tags_setting_value("tags_charreplace", ''),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_disallowedforums",
			"title"			=> $db->escape_string('Disallowed forums'),
			"description"	=> $db->escape_string('Please select the forums you want "Tags" don\'t work on these.'),
			"optionscode"	=> "forumselect",
			"value"			=> tags_setting_value("tags_disallowedforums", 0),
			"disporder"		=> ++$i,
			"gid"			=> $gid
		)
	);

	$db->delete_query('settings', "gid = '{$gid}'");
	$db->insert_query_multiple("settings", $settings);

	rebuild_settings();

	find_replace_templatesets('newthread', '#'.preg_quote('{$posticons}').'#', '{$tags}{$posticons}');
	find_replace_templatesets('editpost', '#'.preg_quote('{$posticons}').'#', '{$tags}{$posticons}');
	find_replace_templatesets('showthread', '#'.preg_quote('{$ratethread}').'#', '{$ratethread}{$tags}');
	find_replace_templatesets('showthread', '#'.preg_quote('{$headerinclude}').'#', '<meta name="keywords" content="{$thread[\'tags_meta\']}" />{$headerinclude}');
	find_replace_templatesets('index', '#'.preg_quote('{$forums}').'#', '{$forums}{$tags}');
	find_replace_templatesets('forumdisplay', '#'.preg_quote('{$threadslist}').'#', '{$threadslist}{$tags}');
}