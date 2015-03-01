<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

$plugins->add_hook("editpost_end", "tags_editpost_end");

function tags_editpost_end()
{
	global $mybb, $db, $lang, $templates, $thread, $post, $tags, $tags_value;

	if($mybb->settings['tags_enabled'] == 0 || tags_in_disforum($thread['fid']) || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])))
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