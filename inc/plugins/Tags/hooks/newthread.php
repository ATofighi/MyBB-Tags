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

	$tags_value = $mybb->get_input('tags');
	$tags_value = htmlspecialchars_uni(tags_string2tag($tags_value));

	eval('$tags = "'.$templates->get('tags_input').'";');
}