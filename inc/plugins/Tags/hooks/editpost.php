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


	$tags = $mybb->input['tags'];
	if(!isset($tags))
	{
		$bad_tags = tags_getbads(true, false);
		$query = $db->simple_select('tags', '*', "tid='{$thread['tid']}'{$bad_tags}");
		$thread['tags'] = array();
		while($tag = $db->fetch_array($query))
		{
			$tags[] = $tag['name'];
		}
	}
	$tags = (array)$tags;

	$tagInputs = '';
	$tagsData = array();
	foreach($tags as $tag) {
		$tagsData[] = array(
			'id' => $tag,
			'text' => $tag
		);
		$tag = htmlspecialchars_uni($tag);
		eval('$tagInputs .= "'.$templates->get('tags_input_hidden').'";');
	}
	$tagsJson = json_encode($tags);
	$tagsData = json_encode($tagsData);

	eval('$tags = "'.$templates->get('tags_input').'";');
}
