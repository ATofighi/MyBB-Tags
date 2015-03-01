<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

$plugins->add_hook("showthread_start", "tags_showthread");

function tags_showthread()
{
	global $mybb, $db, $theme, $lang, $templates, $thread, $tags, $collapsedimg, $collapsed;

	if($mybb->settings['tags_enabled'] == 0 || tags_in_disforum($thread['fid']))
	{
		return;
	}

	$lang->load('tags');

	$subject = $thread['subject'];
	$tid = $thread['tid'];
	$thread['tags'] = array();

	$query = DBTags::get("*", "threads.tid = '{$tid}'");
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
			$db->delete_query("tags", "tid={$tid}");
			$db->insert_query_multiple("tags", $tags_insert);
		}
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

	$thread['tags_meta'] = htmlspecialchars_uni(implode(', ', $thread['tags']));

	if($tags != '')
	{
		eval('$tags = "'.$templates->get('tags_box').'";');
	}
}