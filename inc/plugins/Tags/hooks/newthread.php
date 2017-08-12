<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

$plugins->add_hook("newthread_start", "tags_newthread_start");

function tags_newthread_start()
{
	global $mybb, $db, $templates, $tags, $tags_value, $lang, $fid;

	if($mybb->settings['tags_enabled'] == 0 || tags_in_disforum($fid) || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])))
	{
		return;
	}

	$lang->load('tags');

	$tags = $mybb->get_input('tags', MyBB::INPUT_ARRAY);

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
