<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

function tags_getsize($v)
{
	global $mybb, $db, $mybb_tags_my_maxcnt;
	if(!isset($mybb_tags_my_maxcnt))
	{
		$query = $db->simple_select('tags_slug', 'MAX(count) as maxcnt', "", array("limit" => 1));
		$maxviews = $db->fetch_field($query, 'maxcnt');
		$mybb_tags_my_maxcnt = $maxviews;
	}
	else
	{
		$maxviews = $mybb_tags_my_maxcnt;
	}

	if($v >= $maxviews)
	{
		return 1;
	}
	if($v >= $maxviews/2)
	{
		return 2;
	}
	if($v >= $maxviews/4)
	{
		return 3;
	}
	if($v >= $maxviews/7)
	{
		return 4;
	}
	if($v >= $maxviews/15)
	{
		return 5;
	}
	return 6;
}
