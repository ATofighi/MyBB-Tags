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
	
/*
if(defined('THIS_SCRIPT') && THIS_SCRIPT== 'index.php')
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

    $templatelist .= 'hello_index';
}
*/

function tags_info()
{
	global $lang;
	//$lang->load('tags');
	return array(
		"name"			=> 'Tags',
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
	
/*	$templatearray = array(
		"tid" => "NULL",
		"title" => 'tags_index',
		"template" => $db->escape_string('
<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<thead>
	<tr>
		<td class="thead">
			<strong>{$lang->hello}</strong>
		</td>
	</tr>
</thead>
<tbody>
	<tr>
		<td class="tcat">
			<form method="POST" action="misc.php">
				<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
				<input type="hidden" name="action" value="hello" />
				{$lang->hello_add_message}: <input type="text" name="message" class="textbox" /> <input type="submit" name="submit" value="{$lang->hello_add}" />
			</form>
		</td>
	</tr>
	<tr>
		<td class="trow1">
		{$messages}
		</td>
	</tr>
</tbody>
</table>
<br />'),
		"sid" => "-1",
	);

	$db->insert_query("templates", $templatearray);

	// create settings group
	$insertarray = array(
		'name' => 'hello', 
		'title' => 'Test Plugin', 
		'description' => "Settings for Test Plugin.", 
		'disporder' => 100,
		'isdefault' => 0
	);
	$gid = $db->insert_query("settinggroups", $insertarray);
	
	// add settings
	$setting = array(
		"name"			=> "hello_display1",
		"title"			=> "Display Message Index",
		"description"	=> "Set to no if you do not want to display the messages on index.",
		"optionscode"	=> "yesno",
		"value"			=> 1,
		"disporder"		=> 1,
		"gid"			=> $gid
	);
	$db->insert_query("settings", $setting);
	
	// add settings
	$setting = array(
		"name"			=> "hello_display2",
		"title"			=> "Display Message Postbit",
		"description"	=> "Set to no if you do not want to display the messages below every post.",
		"optionscode"	=> "yesno",
		"value"			=> 1,
		"disporder"		=> 2,
		"gid"			=> $gid
	);
	$db->insert_query("settings", $setting);*/
	

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
	
/*	$db->delete_query('templates', "title IN ('tags_index') AND sid='-1'");

	$db->delete_query("settinggroups", "name = 'tags'");
	
	$db->delete_query('settings', "name IN ('hello_display1', 'hello_display2')");*/
	
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

$plugins->add_hook("global_start", "tags_global");

function tags_global()
{
	global $mybb;
	if($mybb->seo_support)
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
	global $mybb, $db, $tags, $tags_value;

	$tags_value = $mybb->get_input('tags');
	$tags_value = htmlspecialchars_uni(tags_string2tag($tags_value));

	$tags = <<<EOT
<tr>
	<td class="trow2" width="20%" valign="top"><strong>Tags:</strong></td>
	<td class="trow2"><input type="text" class="textbox" name="tags" size="40" maxlength="85" value="{$tags_value}" tabindex="2" id="tags" /></td>
</tr>
<script src="{$mybb->asset_url}/jscripts/tags/jquery.tagsinput.js"></script>
<link rel="stylesheet" type="text/css" href="{$mybb->asset_url}/jscripts/tags/jquery.tagsinput.css" />
<script type="text/javascript">
	$("#tags").tagsInput({
		'height': '40px',
		'width': 'auto',
		'defaultText': 'Add tags here'
	});

	$("#tags").on('change', function()
	{
		$(this).importTags($(this).val());
	});
</script>
EOT;
}

$plugins->add_hook("newthread_do_newthread_end", "tags_newthread_done");

function tags_newthread_done()
{
	global $mybb, $db, $tid;

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
	global $mybb, $db, $thread, $post, $tags, $tags_value;

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

	$tags = <<<EOT
<tr>
	<td class="trow2" width="20%" valign="top"><strong>Tags:</strong></td>
	<td class="trow2"><input type="text" class="textbox" name="tags" size="40" maxlength="85" value="{$tags_value}" tabindex="2" id="tags" /></td>
</tr>
<script src="{$mybb->asset_url}/jscripts/tags/jquery.tagsinput.js"></script>
<link rel="stylesheet" type="text/css" href="{$mybb->asset_url}/jscripts/tags/jquery.tagsinput.css" />
<script type="text/javascript">
	$("#tags").tagsInput({
		'height': '40px',
		'width': 'auto',
		'defaultText': 'Add tags here'
	});

	$("#tags").on('change', function()
	{
		$(this).importTags($(this).val());
	});
</script>
EOT;
}


$plugins->add_hook("editpost_do_editpost_end", "tags_editpost_done");

function tags_editpost_done()
{
	global $mybb, $db, $tid, $thread, $post;

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
	global $mybb, $db, $theme, $thread, $tags, $collapsedimg, $collapsed;
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
		$tags .= <<<EOT
{$comma}<a href="{$mybb->settings['bburl']}/{$tag_link}" title="{$tag}">{$tag}</a> 
EOT;
		$comma = ', ';
		$i++;
	}

	$tags = <<<EOT
<br class="clear" />
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder tfixed clear">
	<thead>
	<tr>
		<td class="thead">
			<div class="expcolimage"><img src="{$theme['imgdir']}/collapse{$collapsedimg['tags']}.png" id="tags_img" class="expander" alt="[-]" title="[-]" /></div>
			<strong>Tags</strong>
		</td>
	</tr>
	</thead>
	<tbody style="{$collapsed['tags_e']}" id="tags_e">
	<tr>
		<td class="trow1">
			{$tags}
		</td>
	</tr>
	</tbody>
</table>
<br class="clear" />
		
EOT;
}


$plugins->add_hook("index_start", "tags_index");

function tags_index()
{
	global $mybb, $db, $tags, $theme, $collapsedimg, $collapsed;

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
						 LIMIT 0, 80");
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
		$tags .= <<<EOT
<a href="{$tag['tag_link']}" style="font-size:{$tag['size']}px">{$tag['name']}</a> 
EOT;
	}

	$tags = <<<EOT
<br class="clear" />
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder tfixed clear">
	<thead>
	<tr>
		<td class="thead">
			<div class="expcolimage"><img src="{$theme['imgdir']}/collapse{$collapsedimg['tags']}.png" id="tags_img" class="expander" alt="[-]" title="[-]" /></div>
			<strong>Tags</strong>
		</td>
	</tr>
	<thead>
	<tbody style="{$collapsed['tags_e']}" id="tags_e">
	<tr>
		<td class="trow1">
			{$tags}
	</tr>
	</tbody>
</table>
		</td>
<br class="clear" />
EOT;
}
$plugins->add_hook("forumdisplay_end", "tags_forumdisplay");

function tags_forumdisplay()
{
	global $mybb, $db, $tags, $theme, $collapsedimg, $collapsed, $fid;

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
						 LIMIT 0, 80");
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
		$tags .= <<<EOT
<a href="{$tag['tag_link']}" style="font-size:{$tag['size']}px">{$tag['name']}</a> 
EOT;
	}

	if($tags != '')
	{
	$tags = <<<EOT
<br class="clear" />
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder tfixed clear">
	<thead>
	<tr>
		<td class="thead">
			<div class="expcolimage"><img src="{$theme['imgdir']}/collapse{$collapsedimg['tags']}.png" id="tags_img" class="expander" alt="[-]" title="[-]" /></div>
			<strong>Tags</strong>
		</td>
	</tr>
	<thead>
	<tbody style="{$collapsed['tags_e']}" id="tags_e">
	<tr>
		<td class="trow1">
			{$tags}
	</tr>
	</tbody>
</table>
		</td>
<br class="clear" />
EOT;
	}
}