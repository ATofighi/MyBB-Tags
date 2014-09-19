<?php
/**
 * MyBB 1.8
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
		"version"		=> "1.0",
		"compatibility" => "18*"
	);
}

function tags_activate()
{
	if(!function_exists('find_replace_templatesets'))
	{
		require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	}
	

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
<script src="{$mybb->asset_url}/jscripts/tags/jquery.tagsinput.js"></script>
<link rel="stylesheet" type="text/css" href="{$mybb->asset_url}/jscripts/tags/jquery.tagsinput.css" />
<script type="text/javascript">
	$("#tags").tagsInput({
		\'height\': \'40px\',
		\'width\': \'auto\',
		\'defaultText\': \'{$lang->tags_placeholder}\'
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
{$comma}<a href="{$mybb->settings[\'bburl\']}/{$tag_link}" title="{$tag}">{$tag}</a>
'),
			"sid" => "-1"
		),
		array(
			"title" => 'tags_box_tag_sized',
			"template" => $db->escape_string('{$comma}<a href="{$tag[\'tag_link\']}" style="font-size:{$tag[\'size\']}px">{$tag[\'name\']}</a>'),
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
	
	// add settings
	$i = 0;
	$settings = array(
		array(
			"name"			=> "tags_enabled",
			"title"			=> "Enable Plugin",
			"description"	=> $db->escape_string('Set to "on" if you want Enable this plugin.'),
			"optionscode"	=> "onoff",
			"value"			=> 1,
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
			"value"			=> 0,
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_per_page",
			"title"			=> "Tags per page",
			"description"	=> $db->escape_string('How many tags shown in "Tags" page?'),
			"optionscode"	=> "text",
			"value"			=> 10,
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_limit",
			"title"			=> $db->escape_string('Limit Tags in "Index Page" and "Forum Display Page"'),
			"description"	=> $db->escape_string('How many tags shown in "Index Page" and "Forum Display Page" ?'),
			"optionscode"	=> "text",
			"value"			=> 50,
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_index",
			"title"			=> $db->escape_string('Show tags in Index Page?'),
			"description"	=> $db->escape_string('Do you want tags shown in Index Page?'),
			"optionscode"	=> "yesno",
			"value"			=> 1,
			"disporder"		=> ++$i,
			"gid"			=> $gid
		),
		array(
			"name"			=> "tags_forumdisplay",
			"title"			=> $db->escape_string('Show tags in "Forum Display" Page?'),
			"description"	=> $db->escape_string('Do you want tags shown in "Forum Display" Page?'),
			"optionscode"	=> "yesno",
			"value"			=> 1,
			"disporder"		=> ++$i,
			"gid"			=> $gid
		)
	);
	$db->insert_query_multiple("settings", $settings);
	

	rebuild_settings();
	
	// Create our entries table
	$collation = $db->build_create_table_collation();
	
	if(!$db->table_exists("tags"))
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

function tags_is_installed()
{
	global $db;

	if($db->table_exists("tags"))
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
	
	rebuild_settings();
	
	if($db->table_exists('tags'))
	{
		$db->drop_table('tags');
	}

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

	if($mybb->settings['tags_enabled'] == 0)
	{
		return;
	}

	$lang->load('tags');

	$tags_value = $mybb->get_input('tags');
	$tags_value = htmlspecialchars_uni(tags_string2tag($tags_value));

	eval('$tags = "'.$templates->get('tags_input').'";');
}

$plugins->add_hook("newthread_do_newthread_end", "tags_newthread_done");

function tags_newthread_done()
{
	global $mybb, $db, $tid;

	if($mybb->settings['tags_enabled'] == 0)
	{
		return;
	}

	$tags_value = $mybb->get_input('tags');
	$tags_value = tags_string2tag($tags_value);
	$tags = explode(',', $tags_value);
	$subject = $mybb->get_input('subject');
	$subject = tags_string2tag($subject);
	$subject = explode(',', $subject);

	$tags = $tags + $subject;
	$tags_value = implode(',', $tags);

	$tags_insert = array();
	foreach($tags as $tag)
	{
		if($tag != '')
		{
			$tags_hash_arr[] = md5(my_strtolower($tag));
			array_push($tags_insert, array(
				'tid' => $tid,
				'name' => $tag,
				'hash' => md5(my_strtolower($tag))
			));
		}
	}

	$tags_hash = implode(',', $tags_hash_arr);

	$db->insert_query_multiple("tags", $tags_insert);
}


$plugins->add_hook("editpost_end", "tags_editpost");

function tags_editpost()
{
	global $mybb, $db, $lang, $templates, $thread, $post, $tags, $tags_value;

	if($mybb->settings['tags_enabled'] == 0)
	{
		return;
	}

	$lang->load('tags');

	if($thread['firstpost'] != $post['pid'])
	{
		return;
	}

	$tags_value = $mybb->get_input('tags');
	if(!$tags_value)
	{
		$query = $db->simple_select('tags', '*', "tid='{$thread['tid']}'");
		$thread['tags'] = array();
		while($tag = $db->fetch_array($query))
		{
			array_push($thread['tags'], $tag['name']);
		}		
		$tags_value = implode(',',$thread['tags']);
	}
	$tags_value = htmlspecialchars_uni(tags_string2tag($tags_value));

	eval('$tags = "'.$templates->get('tags_input').'";');
}


$plugins->add_hook("editpost_do_editpost_end", "tags_editpost_done");

function tags_editpost_done()
{
	global $mybb, $db, $tid, $thread, $post;

	if($mybb->settings['tags_enabled'] == 0)
	{
		return;
	}

	if($thread['firstpost'] != $post['pid'])
	{
		return;
	}

	$tags_value = $mybb->get_input('tags');
	$tags_value = tags_string2tag($tags_value);
	$tags_hash_arr = array();
	$tags = explode(',', $tags_value);
	$subject = $mybb->get_input('subject');
	$subject = tags_string2tag($subject);
	$subject = explode(',', $subject);

	$tags = $tags + $subject;
	$tags_value = implode(',', $tags);

	$tags_insert = array();
	foreach($tags as $tag)
	{
		array_push($tags_insert, array(
			'tid' => $tid,
			'name' => $tag,
			'hash' => md5(my_strtolower($tag))
		));
	}


	$db->delete_query("tags", "tid='{$tid}'");
	$db->insert_query_multiple("tags", $tags_insert);
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

	$query = $db->simple_select('tags', '*', "tid='{$tid}'");
	while($tag = $db->fetch_array($query))
	{
		array_push($thread['tags'], $tag['name']);
	}
	if($db->num_rows($query) == 0)
	{
		$subject = tags_string2tag($subject);
		$tags = explode(',', $subject);

		$tags_insert = array();
		foreach($tags as $tag)
		{
			array_push($tags_insert, array(
				'tid' => $tid,
				'name' => $tag,
				'hash' => md5(my_strtolower($tag))
			));
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
		$comma = ', ';
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

	$query = $db->query("SELECT SUM(thread.views) as sumviews, tag.name from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 LEFT JOIN `".TABLE_PREFIX."posts` post on(thread.firstpost = post.pid)
						 WHERE thread.tid > 0 And post.pid > 0 and thread.visible='1'{$tunviewwhere}{$tinactivewhere} AND thread.closed NOT LIKE 'moved|%'
						 GROUP BY tag.hash
						 ORDER BY RAND()
						 LIMIT 0, {$mybb->settings['tags_limit']}");
	$tags = $comma = '';

	while($tag = $db->fetch_array($query))
	{
		$tag['name'] = htmlspecialchars_uni($tag['name']);
		$tag['tag_link'] = get_tag_link($tag['name']);
		if($tag['sumviews'] > 5000)
		{
			$tag['size'] = '48';
		}
		elseif($tag['sumviews'] > 3000)
		{
			$tag['size'] = '40';
		}
		elseif($tag['sumviews'] > 1000)
		{
			$tag['size'] = '32';
		}
		elseif($tag['sumviews'] > 500)
		{
			$tag['size'] = '24';
		}
		elseif($tag['sumviews'] > 250)
		{
			$tag['size'] = '18';
		}
		elseif($tag['sumviews'] > 100)
		{
			$tag['size'] = '16';
		}
		elseif($tag['sumviews'] > 50)
		{
			$tag['size'] = '14';
		}
		elseif($tag['sumviews'] > 10)
		{
			$tag['size'] = '12';
		}
		else
		{
			$tag['size'] = '8';
		}
		eval('$tags .= "'.$templates->get('tags_box_tag_sized').'";');
		$comma = ', ';
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

	$query = $db->query("SELECT SUM(thread.views) as sumviews, tag.name from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 LEFT JOIN `".TABLE_PREFIX."posts` post on(thread.firstpost = post.pid)
						 WHERE thread.tid > 0 And post.pid > 0 and thread.visible='1' AND thread.fid = '{$fid}' AND thread.closed NOT LIKE 'moved|%'
						 GROUP BY tag.hash
						 ORDER BY RAND()
						 LIMIT 0, {$mybb->settings['tags_limit']}");
	$tags = '';

	while($tag = $db->fetch_array($query))
	{
		$tag['name'] = htmlspecialchars_uni($tag['name']);
		$tag['tag_link'] = get_tag_link($tag['name']);
		if($tag['sumviews'] > 5000)
		{
			$tag['size'] = '48';
		}
		elseif($tag['sumviews'] > 3000)
		{
			$tag['size'] = '40';
		}
		elseif($tag['sumviews'] > 1000)
		{
			$tag['size'] = '32';
		}
		elseif($tag['sumviews'] > 500)
		{
			$tag['size'] = '24';
		}
		elseif($tag['sumviews'] > 250)
		{
			$tag['size'] = '18';
		}
		elseif($tag['sumviews'] > 100)
		{
			$tag['size'] = '16';
		}
		elseif($tag['sumviews'] > 50)
		{
			$tag['size'] = '14';
		}
		elseif($tag['sumviews'] > 10)
		{
			$tag['size'] = '12';
		}
		else
		{
			$tag['size'] = '8';
		}
		eval('$tags .= "'.$templates->get('tags_box_tag_sized').'";');
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