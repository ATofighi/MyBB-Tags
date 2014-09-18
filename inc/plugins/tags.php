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

$plugins->add_hook("global_start", "tags_global");
$plugins->add_hook("newthread_start", "tags_newthread");
$plugins->add_hook("newthread_do_newthread_end", "tags_newthread_done");
$plugins->add_hook("editpost_end", "tags_editpost");
$plugins->add_hook("editpost_do_editpost_end", "tags_editpost_done");
$plugins->add_hook("showthread_start", "tags_showthread");

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

	if(!$db->field_exists("tags", "threads"))
	{
		$db->write_query("ALTER TABLE ".TABLE_PREFIX."threads ADD `tags` text NOT NULL, ADD `tags_hash` text NOT NULL");
	}
	elseif(!$db->field_exists("tags", "tags_hash"))
	{
		$db->write_query("ALTER TABLE ".TABLE_PREFIX."threads ADD `tags_hash` text NOT NULL");
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
	
	if($db->field_exists("tags", "threads"))
	{
		$db->query("ALTER TABLE ".TABLE_PREFIX."threads DROP tags, DROP tags_hash");
	}
}

function tags_string2tag($s)
{
	$s = my_strtolower($s);
	$s = ltrim(rtrim(trim($s)));
	$s = str_replace(array("`","~","!","@","#","$","%","^","&","*","(",")","_","+","-","=","\\","|","]","[","{","}",'"',"'",";",":","/","."," ",">","<"), ",", $s);
	$s = ltrim(rtrim(trim($s, ','),','),',');
	return $s;
}

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

function tags_newthread_done()
{
	global $mybb, $db, $tid;

	$tags_value = $mybb->get_input('tags');
	$tags_value = tags_string2tag($tag_value);
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
	$db->update_query("threads", array(
		"tags" => ','.$tags_value.',',
		"tags_hash" => ','.$tags_hash.','
	), "tid='{$tid}'", 1);
}

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
		$tags_value = $thread['tags'];
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
		$tags_hash_arr[] = md5(my_strtolower($tag));
		array_push($tags_insert, array(
			'tid' => $tid,
			'name' => $tag,
			'hash' => md5(my_strtolower($tag))
		));
	}

	$tags_hash = implode(',', $tags_hash_arr);

	$db->delete_query("tags", "tid='{$tid}'");
	$db->insert_query_multiple("tags", $tags_insert);
	$db->update_query("threads", array(
		"tags" => ','.$tags_value.',',
		"tags_hash" => ','.$tags_hash.','
	), "tid='{$tid}'", 1);
}

function tags_showthread()
{
	global $mybb, $db, $theme, $thread, $tags;
	$subject = $thread['subject'];
	$tid = $thread['tid'];

	$thread['tags'] = trim(ltrim(rtrim($thread['tags'],','),','),',');
	if($thread['tags'] == '')
	{
		$subject = str_replace(array(" ", "-", "_"), ',', $subject);
		$subject = ltrim(rtrim(trim($subject)));
		$subject = ltrim(rtrim(trim($subject, ','), ','), ',');
		$tags = explode(',', $subject);
		$tags_hash_arr = array();

		$tags_insert = array();
		foreach($tags as $tag)
		{
			$tags_hash_arr[] = md5(my_strtolower($tag));
			array_push($tags_insert, array(
				'tid' => $tid,
				'name' => $tag,
				'hash' => md5(my_strtolower($tag))
			));
		}

		$tags_hash = implode(',', $tags_hash_arr);

		$db->delete_query("tags", "tid={$tid}");
		$db->insert_query_multiple("tags", $tags_insert);
		$db->update_query("threads", array(
			"tags" => ','.$subject.',',
			"tags_hash" => ','.$tags_hash.','
		), "tid='{$tid}'", 1);
		$thread['tags'] = ','.$subject.',';
		$thread['tags_hash'] = ','.$tags_hash.',';
	}

	$tags = '';
	$thread['tags'] = explode(',', $thread['tags']);
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
	<tr>
		<td class="thead">
			<strong>Tags</strong>
		</td>
	</tr>
	<tr>
		<td class="trow1">
			{$tags}
		</td>
	</tr>
</table>
<br class="clear" />
		
EOT;
}