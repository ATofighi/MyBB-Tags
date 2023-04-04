<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

$plugins->add_hook("forumdisplay_end", "tags_forumdisplay_end");
$plugins->add_hook("forumdisplay_thread_end", "tags_forumdisplay_thread_end");

function tags_forumdisplay_end()
{
	global $mybb, $db, $lang, $templates, $tags, $theme, $collapsedimg, $collapsed, $fid;

	if($mybb->settings['tags_enabled'] == 0 || $mybb->settings['tags_forumdisplay'] == 0)
	{
		return;
	}

	$lang->load('tags');

	// TODO: cache

	$mybb->settings['tags_limit'] = (int)($mybb->settings['tags_limit']);

	$order_by = 'RAND()';
	if($db->type == 'pgsql' || $db->type == 'sqlite')
	{
		$order_by = 'RANDOM()';
	}

	$query = DBTags::get(
		"*",
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
		$tag['tag_link'] = get_tag_link($tag['slug']);
		$tag['size'] = tags_getsize($tag['count']);
		eval('$tags .= "'.$templates->get('tags_box_tag_sized').'";');
		$comma = ', ';
	}

	if($tags != '')
	{
		eval('$tags = "'.$templates->get('tags_box').'";');
	}
}



function tags_forumdisplay_thread_end() {
	global $tagsCache, $thread, $tids, $db, $templates, $mybb, $lang;
	$lang->load('tags');

	if(!$mybb->settings['tags_forumdisplay_thread']) {
		return;
	}

	if(!isset($tagsCache)) {
		$tagsCache = array();
		$query = DBTags::get(
			'tags.name, slugs.slug, threads.tid',
			"threads.tid IN ({$tids})"
		);

		while($row = $db->fetch_array($query)) {
			$tagsCache[$row['tid']][] = $row;
		}
	}

	$comma = '';
	$thread['tags'] = '';
	$thread['more_tags_count'] = 0;
	if(!empty($tagsCache[$thread['tid']])) {
		$cnt = 0;
		foreach($tagsCache[$thread['tid']] as $tagData) {
			if(!$tagData['name'])
			{
				continue;
			}
			$tag['name'] = htmlspecialchars_uni($tagData['name']);
			$tag['tag_link'] = get_tag_link($tagData['slug']);
			$tag['size'] = 6;

			if($cnt < $mybb->settings['tags_forumdisplay_thread_limit']) {
				eval('$thread[\'first_tags\'] .= "'.$templates->get('tags_forumdisplay_thread_tag').'";');
			}
			else {
				eval('$thread[\'more_tags\'] .= "'.$templates->get('tags_forumdisplay_thread_tag').'";');
				$thread['more_tags_count']++;
			}
			$comma = ', ';
			$cnt++;
		}
		$lang->more_tags = $lang->sprintf($lang->tags_more, $thread['more_tags_count']);
		eval('$thread[\'tags\'] = "'.$templates->get('tags_forumdisplay_thread').'";');
	}

}
