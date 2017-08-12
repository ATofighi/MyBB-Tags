<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

$plugins->add_hook("datahandler_post_insert_thread_end", "tags_thread");
$plugins->add_hook("datahandler_post_update_end", "tags_thread");

function tags_thread(&$datahandler)
{
	global $mybb, $db;

	if($mybb->settings['tags_enabled'] == 0 || tags_in_disforum($datahandler->thread_insert_data['fid']) || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])))
	{
		return;
	}

	$thread = $datahandler->data;
	$tid = $datahandler->tid;

	$tags = $mybb->get_input('tags', MyBB::INPUT_ARRAY);
	array_unique($tags);


	$oldTags = array();
	$query = DBTags::get("*", "threads.tid = '{$tid}'");
	while($tag = $db->fetch_array($query))
	{
		if($tag['name'])
		{
			$oldTags[] = $tag['name'];
		}
	}
	array_unique($oldTags);

	$tagsInsert = array();
	$tagsRemove = array();
	foreach($tags as $tag)
	{
		if($tag)
		{
			if(!in_array($tag, $oldTags))
				$tagsInsert[] = array(
					'tid' => $tid,
					'name' => $db->escape_string($tag),
				);
		}
	}
	foreach($oldTags as $tag)
	{
		if($tag)
		{
			if(!in_array($tag, $tags)) {
				$tagsRemove[] = "'".$db->escape_string($tag)."'";
			};
		}
	}

	if(count($tagsRemove) > 0) {
		$tagsRemove = implode(',', $tagsRemove);
		$db->delete_query("tags", "tid='{$tid}' and name IN ({$tagsRemove})");
	}
	if(count($tagsInsert) > 0)
	{
		$db->insert_query_multiple("tags", $tagsInsert);
	}
}

$plugins->add_hook("datahandler_post_validate_thread", "tags_validate");
$plugins->add_hook("datahandler_post_validate_post", "tags_validate");

function tags_validate(&$datahandler)
{
	global $mybb, $db, $thread, $lang;

	if($mybb->settings['tags_enabled'] == 0 || tags_in_disforum($datahandler->fid) || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])))
	{
		return;
	}

	$lang->load('tags');
	$mybb->settings['tags_max_thread'] = (int)$mybb->settings['tags_max_thread'];

	if(count($mybb->get_input('tags', MyBB::INPUT_ARRAY)) > 0 && ($datahandler->action == 'thread' || (is_array($thread) && $datahandler->data['pid'] == $thread['firstpost'])))
	{
		$tags = $mybb->get_input('tags', MyBB::INPUT_ARRAY);
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
