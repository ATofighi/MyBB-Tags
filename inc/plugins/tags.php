<?php
/**
 * MyBB-Tags 2
 * Copyright 2014 My-BB.Ir Group, All Rights Reserved
 *
 * Website: http://my-bb.ir
 * Author: AliReza_Tofighi
 *
 */

// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}
	

if(defined('THIS_SCRIPT') && in_array(THIS_SCRIPT, array('tag.php', 'showthread.php', 'index.php', 'forumdisplay.php')))
{
    global $templatelist;
    if(isset($templatelist) && $templatelist != '')
    {
        $templatelist .= ',';
    }
	else
	{
		$templatelist = '';
	}

    $templatelist .= 'tags_input,tags_box,tags_box_tag,tags_box_tag_sized,tags_search,tags_thread,tags_notags,tags_viewtag';
}


function tags_info()
{
	global $lang;
	$lang->load('tags');
	return array(
		"name"			=> $lang->tags_pluginname,
		"description"	=> '',
		"website"		=> "http://myb-b.ir",
		"author"		=> "My-BB.Ir Group",
		"authorsite"	=> "http://my-bb.ir",
		"version"		=> "2.0",
		"compatibility" => "18*"
	);
}

function tags_activate()
{
	global $mybb, $db;
	if(!function_exists('find_replace_templatesets'))
	{
		require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	}
	
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
			"description"	=> $db->escape_string('Do you want to use SEO URLs (ex: tags-***.html) for tags?<br />
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
		)
	);

	$db->delete_query('settings', "name LIKE 'tags\_%'");
	$db->insert_query_multiple("settings", $settings);

	rebuild_settings();

	find_replace_templatesets('newthread', '#'.preg_quote('{$posticons}').'#', '{$tags}{$posticons}');
	find_replace_templatesets('editpost', '#'.preg_quote('{$posticons}').'#', '{$tags}{$posticons}');
	find_replace_templatesets('showthread', '#'.preg_quote('{$ratethread}').'#', '{$ratethread}{$tags}');
	find_replace_templatesets('index', '#'.preg_quote('{$forums}').'#', '{$forums}{$tags}');
	find_replace_templatesets('forumdisplay', '#'.preg_quote('{$threadslist}').'#', '{$threadslist}{$tags}');
}

function tags_deactivate()
{
	if(!function_exists('find_replace_templatesets'))
	{
		require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	}
	
	find_replace_templatesets('newthread', '#'.preg_quote('{$tags}').'#', '');
	find_replace_templatesets('editpost', '#'.preg_quote('{$tags}').'#', '');
	find_replace_templatesets('showthread', '#'.preg_quote('{$tags}').'#', '');
	find_replace_templatesets('index', '#'.preg_quote('{$tags}').'#', '');
	find_replace_templatesets('forumdisplay', '#'.preg_quote('{$tags}').'#', '');
}

function tags_install()
{
	global $db, $lang, $mybb;
	
	$templatearray = array(
		array(
			"title" => 'tags_input',
			"template" => $db->escape_string('<tr>
	<td class="trow2" width="20%" valign="top"><strong>{$lang->tags}:</strong></td>
	<td class="trow2"><input type="text" class="textbox" name="tags" size="40" maxlength="85" value="{$tags_value}" tabindex="2" id="tags" /></td>
</tr>
<script src="{$mybb->asset_url}/jscripts/tags/jquery.tagsinput.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$mybb->asset_url}/jscripts/tags/jquery.tagsinput.css" />
<script type="text/javascript">
	$("#tags").tagsInput({
		\'height\': \'40px\',
		\'width\': \'auto\',
		\'defaultText\': \'{$lang->tags_placeholder}\',
		\'minChars\': {$mybb->settings[\'tags_minchars\']},
		\'maxChars\': {$mybb->settings[\'tags_maxchars\']}
	});

	$("#tags").on(\'change\', function()
	{
		$(this).importTags($(this).val());
	});
</script>'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_box',
			"template" => $db->escape_string('<br class="clear" />
<style type="text/css">
.tag {
	display: inline-block;
	vertical-align: middle;
	box-sizing: content-box;
	word-wrap: normal;
	word-spacing: normal;
	position: relative;
	height: 24px;
	font-size: 11px;
	padding:0 10px 0 12px;
	background:#0089e0;
	text-shadow: -1px -1px 3px #555;
	color:#fff;
	text-decoration:none;
	-moz-border-radius-bottomright:4px;
	-webkit-border-bottom-right-radius:4px;	
	border-bottom-right-radius:4px;
	-moz-border-radius-topright:4px;
	-webkit-border-top-right-radius:4px;	
	border-top-right-radius:4px;	
}

.tag:link, .tag:hover, .tag:visited, .tag:active {
	color:#fff;
	text-decoration: none;
}

.tag:before{
	content:"";
	float:left;
	position:absolute;
	top:0;
	right: 100%;
	width:0;
	height:0;
	border-color:transparent #0089e0 transparent transparent;
	border-style:solid;
	border-width:12px 12px 12px 0;		
}

.tag:after{
	content:"";
	position:absolute;
	top:50%;
	left:0;
	margin-top:-2px;
	float:left;
	width:4px;
	height:4px;
	-moz-border-radius:2px;
	-webkit-border-radius:2px;
	border-radius:2px;
	background:#fff;
	-moz-box-shadow:-1px -1px 2px #004977;
	-webkit-box-shadow:-1px -1px 2px #004977;
	box-shadow:-1px -1px 2px #004977;
}

.tag:hover{
	background:#555;
}

.tag:hover:before{
	border-color: transparent #555 transparent transparent;
}

.tag.tag-h1 {
	font-size: 32px;
	height: 42px;
	margin-left: 21px;
}

.tag.tag-h1:before {
	border-width: 21px;
	border-left-width:0;
}

.tag.tag-h2 {
	font-size: 24px;
	height: 34px;
	margin-left: 17px;
}

.tag.tag-h2:before {
	border-width: 17px;
	border-left-width:0;
}

.tag.tag-h3 {
	font-size: 20px;
	height: 28px;
	margin-left: 14px;
}

.tag.tag-h3:before {
	border-width: 14px;
	border-left-width:0;
}

.tag.tag-h4 {
	font-size: 17px;
	height: 24px;
	margin-left: 12px;
}

.tag.tag-h4:before {
	border-width: 12px;
	border-left-width:0;
}

.tag.tag-h5 {
	font-size: 14px;
	height: 20px;
	margin-left:10px;
}

.tag.tag-h5:before {
	border-width: 10px;
	border-left-width:0;
}

.tag.tag-h6 {
	font-size: 11px;
	height: 16px;
	margin-left:8px
}

.tag.tag-h6:before {
	border-width: 8px;
	border-left-width:0;
}
</style>
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder tfixed clear">
	<thead>
	<tr>
		<td class="thead">
			<div class="expcolimage"><img src="{$theme[\'imgdir\']}/collapse{$collapsedimg[\'tags\']}.png" id="tags_img" class="expander" alt="[-]" title="[-]" /></div>
			<strong>{$lang->tags}</strong>
		</td>
	</tr>
	</thead>
	<tbody style="{$collapsed[\'tags_e\']}" id="tags_e">
	<tr>
		<td class="trow1">
			{$tags}
		</td>
	</tr>
	</tbody>
</table>
<br class="clear" />
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_box_tag',
			"template" => $db->escape_string('
 <a href="{$mybb->settings[\'bburl\']}/{$tag_link}" title="{$tag}" class="tag tag-h5">{$tag}</a>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_box_tag_sized',
			"template" => $db->escape_string(' <a href="{$tag[\'tag_link\']}" class="tag tag-h{$tag[\'size\']}">{$tag[\'name\']}</a>'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_search',
			"template" => $db->escape_string('	<html>
		<head>
			<title>{$mybb->settings[\'bbname\']} - Tags</title>
			{$headerinclude}
		</head>
		<body>
			{$header}
			<form action="{$tag_link}" method="get">
			<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder clear">
				<tr>
					<td class="thead" colspan="2">
						<strong>{$lang->tags_search}</strong>
					</td>
				</tr>
				<tr>
					<td class="trow2">
						<input type="text" class="textbox" placeholder="{$lang->tags_search_placeholder}" name="name" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" />
					</td>
					<td class="trow2" width="50">
						<input type="submit" class="button" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="{$lang->tags_go}" />
					</td>
				</tr>
			</table>
			</form>
			{$footer}
		</body>
	</html>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_thread',
			"template" => $db->escape_string('
	<tr>
		<td class="tcat" colspan="2">
			<div class="float_{$no_dir}">
				{$lang->tags_author}: <strong>{$tag[\'profilelink\']}</strong> - {$lang->tags_replies}: <a href="javascript:MyBB.whoPosted({$tag[\'tid\']});">{$tag[\'replies\']}</a> - {$lang->tags_views}: {$tag[\'views\']}
			</div>
			<a href="{$tag[\'threadlink\']}{$highlight}"><strong>{$tag[\'subject\']}</strong></a>
		</td>
	</tr>
	<tr>
		<td class="trow1" colspan="2">
			<div style="max-height:100px;overflow:auto">
				{$tag[\'message\']}
			</div>
		</td>
	</tr>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_notags',
			"template" => $db->escape_string('
<tr>
	<td class="trow1" colspan="2">
		{$lang->tags_notags}
	</td>
</tr>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_viewtag',
			"template" => $db->escape_string('
	<html>
		<head>
			<title>{$lang->tags} - {$name}</title>
			{$headerinclude}
		</head>
		<body>
			{$header}
			<form action="{$tag_link}" method="get">
			<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder clear">
				<tr>
					<td class="thead" colspan="2">
						<strong>{$lang->tags}</strong>
					</td>
				</tr>
				<tr>
					<td class="trow2">
						<input type="text" class="textbox" name="name" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="{$name}" />
					</td>
					<td class="trow2" width="50">
						<input type="submit" class="button" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="{$lang->tags_go}" />
					</td>
				</tr>
				{$tags}
			</table>
			</form>
			{$multipage}
			{$footer}
		</body>
	</html>
'),
			"sid" => "-1"
		)
	);

	$db->insert_query_multiple("templates", $templatearray);

	// create settings group
	$insertarray = array(
		'name' => 'tags', 
		'title' => 'Tags Plugin', 
		'description' => "Settings for Tags Plugin.", 
		'disporder' => 100,
		'isdefault' => 0
	);
	$gid = $db->insert_query("settinggroups", $insertarray);
	
	// Create our entries table
	$collation = $db->build_create_table_collation();
	
	if(!$db->table_exists('tags'))
	{
		if($db->type == 'pgsql')
		{
			$db->write_query("CREATE TABLE `".TABLE_PREFIX."tags` (
					`id` serial,
					`tid` int NOT NULL default '0',
					`name` varchar(200)  NOT NULL default '',
					`hash` varchar(200)  NOT NULL default '',
					PRIMARY KEY  (`id`)
				) ENGINE=MyISAM{$collation}");
		}
		else
		{
			$db->write_query("CREATE TABLE `".TABLE_PREFIX."tags` (
					`id` int(10) UNSIGNED NOT NULL auto_increment,
					`tid` int(100) UNSIGNED NOT NULL default '0',
					`name` varchar(200)  NOT NULL default '',
					`hash` varchar(200)  NOT NULL default '',
					PRIMARY KEY  (`id`)
				) ENGINE=MyISAM{$collation}");
		}
	}
}

function tags_is_installed()
{
	global $db, $mybb;

	if(isset($mybb->settings['tags_enabled']))
	{
		return true;
	}

	return false;
}

function tags_uninstall()
{
	global $db, $mybb;
	
	$db->delete_query('templates', "title LIKE 'tags\_%' AND sid='-1'");

	$db->delete_query("settinggroups", "name = 'tags'");
	
	$db->delete_query('settings', "name LIKE 'tags\_%'");
	
	if($mybb->settings['tags_droptable'])
	{
		$db->drop_table('tags');
	}
	
	rebuild_settings();

}

function tags_setting_value($setting, $value)
{
	global $mybb;
	if(isset($mybb->settings[$setting]))
	{
		return $mybb->settings[$setting];
	}
	else
	{
		return $value;
	}
}

function tags_getbads($and = true, $prefix = true)
{
	global $mybb;
	$b = $mybb->settings['tags_bad'];
	$b = str_replace(array("\r\n", "\n", "\r"), ',', $b);
	$b = tags_string2tag($b);
	$tags = explode(',', $b);
	$tags_hash = array();
	foreach($tags as $tag)
	{
		if($tag == '')
		{
			continue;
		}

		if($tag && !in_array("'".md5($tag)."'", $tags_hash))
		{
			array_push($tags_hash, "'".md5($tag)."'");
		}
	}
	$r = '';
	if($and)
	{
		$r .= ' AND ';
	}
	if($prefix)
	{
		$r .= 'tag.';
	}
	$r .= 'hash NOT IN ('.implode(', ', $tags_hash).')';
	if(count($tags_hash))
	{
		return $r;
	}
	else
	{
		return '';
	}
}

function tags_getsize($v)
{
	global $mybb, $db, $mybb_tags_my_maxviews;
	if(!isset($mybb_tags_my_maxviews))
	{
		$query = $db->simple_select('threads', 'MAX(views) as maxviews', "", array("limit" => 1));
		$maxviews = $db->fetch_field($query, 'maxviews');
		$mybb_tags_my_maxviews = $maxviews;
	}
	else
	{
		$maxviews = $mybb_tags_my_maxviews;
	}

	if($v >= $maxviews)
	{
		return 1;
	}
	if($v >= $maxviews/2)
	{
		return 2;
	}
	if($v >= $maxviews/4)
	{
		return 3;
	}
	if($v >= $maxviews/7)
	{
		return 4;
	}
	if($v >= $maxviews/15)
	{
		return 5;
	}
	return 6;
}

function tags_string2tag($s)
{
	$s = my_strtolower($s);
	$s = ltrim(rtrim(trim($s)));
	$s = str_replace(array("`","~","!","@","#","$","%","^","&","*","(",")","_","+","-","=","\\","|","]","[","{","}",'"',"'",";",":","/","."," ",">","<"), ",", $s);
	$s = ltrim(rtrim(trim($s, ','),','),',');
	$s = preg_replace("#([,]+)#si", ',', $s);
	return $s;
}

function tags_current_url()
{
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}
	else
	{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

$plugins->add_hook("global_start", "tags_global");

function tags_global()
{
	global $mybb;
	if($mybb->settings['tags_seo'])
	{
		define('TAG_URL', "tag-{name}.html");
		define('TAG_URL_PAGE', "tag.html");
	}
	else
	{
		define('TAG_URL', "tag.php?name={name}");
		define('TAG_URL_PAGE', "tag.php");
	}
}

function get_tag_link($name='')
{
	if($name == '')
	{
		$link = TAG_URL_PAGE;
	}
	else
	{
		$link = str_replace("{name}", $name, TAG_URL);
	}
	return htmlspecialchars_uni($link);
}

$plugins->add_hook("newthread_start", "tags_newthread");

function tags_newthread()
{
	global $mybb, $db, $templates, $tags, $tags_value, $lang;

	if($mybb->settings['tags_enabled'] == 0 || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])))
	{
		return;
	}

	$lang->load('tags');

	$tags_value = $mybb->get_input('tags');
	$tags_value = htmlspecialchars_uni(tags_string2tag($tags_value));

	eval('$tags = "'.$templates->get('tags_input').'";');
}

$plugins->add_hook("editpost_end", "tags_editpost");

function tags_editpost()
{
	global $mybb, $db, $lang, $templates, $thread, $post, $tags, $tags_value;

	if($mybb->settings['tags_enabled'] == 0 || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])))
	{
		return;
	}

	$lang->load('tags');

	if($thread['firstpost'] != $mybb->get_input('pid', 1))
	{
		return;
	}

	$tags_value = $mybb->get_input('tags');
	if(!$tags_value)
	{
		$bad_tags = tags_getbads(true, false);
		$query = $db->simple_select('tags', '*', "tid='{$thread['tid']}'{$bad_tags}");
		$thread['tags'] = array();
		while($tag = $db->fetch_array($query))
		{
			if(!in_array($tag['name'], $thread['tags']) && $tag['name'] != '')
			{
				array_push($thread['tags'], $tag['name']);
			}
		}		
		$tags_value = implode(',',$thread['tags']);
	}
	$tags_value = htmlspecialchars_uni(tags_string2tag($tags_value));

	eval('$tags = "'.$templates->get('tags_input').'";');
}


$plugins->add_hook("datahandler_post_insert_thread_end", "tags_thread");
$plugins->add_hook("datahandler_post_update_end", "tags_thread");

function tags_thread(&$datahandler)
{
	global $mybb, $db;

	if($mybb->settings['tags_enabled'] == 0 || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])))
	{
		return;
	}
	
	$thread = $datahandler->data;
	$tid = $datahandler->tid;

	$tags_value = $mybb->get_input('tags');
	$tags_value = tags_string2tag($tags_value);
	$tags_hash_arr = array();
	$tags = explode(',', $tags_value);
	$subject = $thread['subject'];
	$subject = tags_string2tag($subject);
	$subject = explode(',', $subject);

	$tags = array_merge($tags, $subject);

	$tags_insert = array();
	foreach($tags as $tag)
	{
		if($tag && !in_array(array(
				'tid' => $tid,
				'name' => $tag,
				'hash' => md5($tag)
			), $tags_insert))
		{
			array_push($tags_insert, array(
				'tid' => $tid,
				'name' => $tag,
				'hash' => md5($tag)
			));
		}
	}


	$db->delete_query("tags", "tid='{$tid}'");
	$db->insert_query_multiple("tags", $tags_insert);
}

$plugins->add_hook("datahandler_post_validate_thread", "tags_validate");
$plugins->add_hook("datahandler_post_validate_post", "tags_validate");

function tags_validate(&$datahandler)
{
	global $mybb, $db, $thread, $lang;
	
	if($mybb->settings['tags_enabled'] == 0 || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])))
	{
		return;
	}

	$lang->load('tags');
	$mybb->settings['tags_max_thread'] = (int)$mybb->settings['tags_max_thread'];

	if($mybb->get_input('tags') != '' && ($datahandler->action == 'thread' || (is_array($thread) && $datahandler->data['pid'] == $thread['firstpost'])))
	{
		$tags_value = $mybb->get_input('tags');
		$tags_value = tags_string2tag($tags_value);
		$tags = explode(',', $tags_value);
		if(count($tags) > $mybb->settings['tags_max_thread'] && $mybb->settings['tags_max_thread'] > 0)
		{
			$lang->many_tags = $lang->sprintf($lang->many_tags, $mybb->settings['tags_max_thread']);
			$datahandler->set_error($lang->many_tags);
			return;
		}
		foreach($tags as $tag)
		{
			if(my_strlen($tag) > 0 && my_strlen($tag) < $mybb->settings['tags_minchars'])
			{
				$datahandler->set_error($lang->tags_too_short);
				return;
			}
			elseif(my_strlen($tag) > $mybb->settings['tags_maxchars'] && $mybb->settings['tags_maxchars'] > 0)
			{
				$datahandler->set_error($lang->tags_too_long);
				return;
			}
		}
	}
}

$plugins->add_hook("showthread_start", "tags_showthread");

function tags_showthread()
{
	global $mybb, $db, $theme, $lang, $templates, $thread, $tags, $collapsedimg, $collapsed;

	if($mybb->settings['tags_enabled'] == 0)
	{
		return;
	}

	$lang->load('tags');

	$subject = $thread['subject'];
	$tid = $thread['tid'];
	$thread['tags'] = array();

	$bad_tags = tags_getbads(true, false);
	$query = $db->simple_select('tags', '*', "tid='{$tid}'{$bad_tags}");
	while($tag = $db->fetch_array($query))
	{
		if($tag['name'] && !in_array($tag['name'], $thread['tags']))
		{
			array_push($thread['tags'], $tag['name']);
		}
	}
	if($db->num_rows($query) == 0)
	{
		$subject = tags_string2tag($subject);
		$tags = explode(',', $subject);

		$tags_insert = array();
		foreach($tags as $tag)
		{
			if($tag && !in_array(array(
					'tid' => $tid,
					'name' => $tag,
					'hash' => md5($tag)
				), $tags_insert))
			{
				array_push($tags_insert, array(
					'tid' => $tid,
					'name' => $tag,
					'hash' => md5($tag)
				));
			}
		}

		$db->delete_query("tags", "tid={$tid}");
		$db->insert_query_multiple("tags", $tags_insert);
		$thread['tags'] = $tags;
	}


	$tags = '';
	$comma = '';
	$i = 0;
	foreach($thread['tags'] as $tag)
	{
		if($tag == '' || $i >= 25)
			continue;

		$tag = htmlspecialchars_uni($tag);
		$tag_link = get_tag_link($tag);
		eval('$tags .= "'.$templates->get('tags_box_tag').'";');
		$comma = $lang->comma;
		$i++;
	}

	if($tags != '')
	{
	eval('$tags = "'.$templates->get('tags_box').'";');
	}
}


$plugins->add_hook("index_start", "tags_index");

function tags_index()
{
	global $mybb, $db, $tags, $theme, $templates, $lang, $collapsedimg, $collapsed;

	if($mybb->settings['tags_enabled'] == 0 || $mybb->settings['tags_index'] == 0)
	{
		return;
	}

	$lang->load('tags');
	
	$mybb->settings['tags_limit'] = (int)($mybb->settings['tags_limit']);

	// get forums user cannot view
	$unviewable = get_unviewable_forums(true);
	if($unviewable)
	{
		$unviewwhere = " AND fid NOT IN ($unviewable)";
		$tunviewwhere = " AND thread.fid NOT IN ($unviewable)";
	}
	else
	{
		$unviewwhere = '';
	}

	// get inactive forums
	$inactive = get_inactive_forums();
	if($inactive)
	{
		$inactivewhere = " AND fid NOT IN ($inactive)";
		$tinactivewhere = " AND thread.fid NOT IN ($inactive)";
	}
	else
	{
		$inactivewhere = '';
	}

	$order_by = 'RAND()';
	if($db->type == 'pgsql' || $db->type == 'sqlite')
	{
		$order_by = 'RANDOM()';
	}

	$bad_tags = tags_getbads(true);

	$query = $db->query("SELECT SUM(thread.views) as sumviews, tag.name from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 LEFT JOIN `".TABLE_PREFIX."posts` post on(thread.firstpost = post.pid)
						 WHERE thread.tid > 0 And post.pid > 0 and thread.visible='1'{$bad_tags}{$tunviewwhere}{$tinactivewhere} AND thread.closed NOT LIKE 'moved|%'
						 GROUP BY tag.hash
						 ORDER BY {$order_by}
						 LIMIT 0, {$mybb->settings['tags_limit']}");
	$tags = $comma = '';

	while($tag = $db->fetch_array($query))
	{
		if(!$tag['name'])
		{
			continue;
		}
		$tag['name'] = htmlspecialchars_uni($tag['name']);
		$tag['tag_link'] = get_tag_link($tag['name']);
		$tag['size'] = tags_getsize($tag['sumviews']);
		eval('$tags .= "'.$templates->get('tags_box_tag_sized').'";');
		$comma = $lang->comma;
	}

	if($tags != '')
	{
	eval('$tags = "'.$templates->get('tags_box').'";');
	}
}
$plugins->add_hook("forumdisplay_end", "tags_forumdisplay");

function tags_forumdisplay()
{
	global $mybb, $db, $lang, $templates, $tags, $theme, $collapsedimg, $collapsed, $fid;

	if($mybb->settings['tags_enabled'] == 0 || $mybb->settings['tags_forumdisplay'] == 0)
	{
		return;
	}

	$lang->load('tags');

	$mybb->settings['tags_limit'] = (int)($mybb->settings['tags_limit']);

	// get forums user cannot view
	$unviewable = get_unviewable_forums(true);
	if($unviewable)
	{
		$unviewwhere = " AND fid NOT IN ($unviewable)";
		$tunviewwhere = " AND thread.fid NOT IN ($unviewable)";
	}
	else
	{
		$unviewwhere = '';
	}

	// get inactive forums
	$inactive = get_inactive_forums();
	if($inactive)
	{
		$inactivewhere = " AND fid NOT IN ($inactive)";
		$tinactivewhere = " AND thread.fid NOT IN ($inactive)";
	}
	else
	{
		$inactivewhere = '';
	}

	$order_by = 'RAND()';
	if($db->type == 'pgsql' || $db->type == 'sqlite')
	{
		$order_by = 'RANDOM()';
	}

	$bad_tags = tags_getbads(true);

	$query = $db->query("SELECT SUM(thread.views) as sumviews, tag.name from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 LEFT JOIN `".TABLE_PREFIX."posts` post on(thread.firstpost = post.pid)
						 WHERE thread.tid > 0{$bad_tags} And post.pid > 0 and thread.visible='1' AND thread.fid = '{$fid}' AND thread.closed NOT LIKE 'moved|%'
						 GROUP BY tag.hash
						 ORDER BY {$order_by}
						 LIMIT 0, {$mybb->settings['tags_limit']}");
	$tags = $comma = '';

	while($tag = $db->fetch_array($query))
	{
		if(!$tag['name'])
		{
			continue;
		}

		$tag['name'] = htmlspecialchars_uni($tag['name']);
		$tag['tag_link'] = get_tag_link($tag['name']);
		$tag['size'] = tags_getsize($tag['sumviews']);
		eval('$tags .= "'.$templates->get('tags_box_tag_sized').'";');
		$comma = ', ';
	}

	if($tags != '')
	{
		eval('$tags = "'.$templates->get('tags_box').'";');
	}
}

$plugins->add_hook("admin_config_settings_begin", "tags_settings");

function tags_settings()
{
	global $lang;
	$lang->load('tags');
}