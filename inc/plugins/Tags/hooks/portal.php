<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

$plugins->add_hook("portal_start", "tags_portal_start");

function tags_portal_start()
{
	global $mybb, $db, $tags, $theme, $templates, $lang, $collapsedimg, $collapsed;

	if($mybb->settings['tags_enabled'] == 0 || $mybb->settings['tags_portal'] == 0)
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

	$query = DBTagsSlug::get(
		"*",
		"",
		array(
			'groupBy' => 'slugs.slug',
			'orderBy' => $order_by,
			'orderType' => '',
			'limit' => "0, {$mybb->settings['tags_limit']}"
		)
	);

	$tags = $comma = '';

	while($tag = $db->fetch_array($query))
	{
		$tag['name'] = htmlspecialchars_uni($tag['name']);
		$tag['tag_link'] = get_tag_link($tag['slug']);
		$tag['size'] = tags_getsize($tag['count']);
		eval('$tags .= "'.$templates->get('tags_box_tag_sized').'";');
		$comma = $lang->comma;
	}

	if($tags != '')
	{
		eval('$tags = "'.$templates->get('tags_box').'";');
	}
}
