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

	$query = DBTags::get("tags.name, slugs.slug",
						 "threads.tid = '{$tid}'",
						 array(
				 			'groupBy' => 'slugs.slug'
						)
					);
	while($tag = $db->fetch_array($query))
	{
		$thread['tags'][] = $tag;
	}
	sort($thread['tags']);

	$tags = '';
	$comma = '';
	$i = 0;
	foreach($thread['tags'] as $tagData)
	{
		$oldTag = $tagData;
		$tag = htmlspecialchars_uni($tagData['name']);
		$tag_link = get_tag_link($tagData['slug']);
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
