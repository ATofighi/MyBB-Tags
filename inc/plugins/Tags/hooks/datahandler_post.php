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

	if($mybb->settings['tags_enabled'] == 0 || tags_in_disforum($datahandler->thread_insert_data['fid']) || ($mybb->settings['tags_groups'] != -1 && !is_member($mybb->settings['tags_groups'])) || !$mybb->get_input('tags'))
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
				'name' => $db->escape_string($tag),
				'hash' => md5($tag)
			), $tags_insert))
		{
			array_push($tags_insert, array(
				'tid' => $tid,
				'name' => $db->escape_string($tag),
				'hash' => md5($tag)
			));
		}
	}


	if(count($tags_insert) > 0)
	{
		$db->delete_query("tags", "tid='{$tid}'");
		$db->insert_query_multiple("tags", $tags_insert);
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