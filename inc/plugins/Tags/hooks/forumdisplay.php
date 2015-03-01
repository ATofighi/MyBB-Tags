<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

$plugins->add_hook("forumdisplay_end", "tags_forumdisplay_end");

function tags_forumdisplay_end()
{
	global $mybb, $db, $lang, $templates, $tags, $theme, $collapsedimg, $collapsed, $fid;

	if($mybb->settings['tags_enabled'] == 0 || $mybb->settings['tags_forumdisplay'] == 0)
	{
		return;
	}

	$lang->load('tags');

	$mybb->settings['tags_limit'] = (int)($mybb->settings['tags_limit']);

	$order_by = 'RAND()';
	if($db->type == 'pgsql' || $db->type == 'sqlite')
	{
		$order_by = 'RANDOM()';
	}

	$query = DBTags::get(
		"SUM(threads.views) as sumviews, tags.name",
		"threads.fid = '{$fid}'",
		array(
			'orderBy' => $order_by,
			'orderType' => '',
			'limit' => "0, {$mybb->settings['tags_limit']}"
		)
	);
	$tags = $comma = '';

	while($tag = $db->fetch_array($query))
	{
		if(!$tag['name'])
		{
			continue;
		}

		$tag['name'] = htmlspecialchars_uni($tag['name']);
		$tag['tag_link'] = get_tag_link($tag['name']);
		$tag['size'] = tags_getsize($tag['sumviews']);
		eval('$tags .= "'.$templates->get('tags_box_tag_sized').'";');
		$comma = ', ';
	}

	if($tags != '')
	{
		eval('$tags = "'.$templates->get('tags_box').'";');
	}
}