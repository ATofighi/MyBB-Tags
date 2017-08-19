<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}


function tags_getbads($and = true, $prefix = true, $not = true)
{
	global $mybb;
	$b = $mybb->settings['tags_bad'];
	$b = str_replace(array("\r\n", "\n", "\r"), ',', $b);
	$tags = explode(',', $b);
	array_unique($tags);
	$queryTags = array();
	foreach($tags as $tag)
	{
		if($tag == '')
		{
			continue;
		}
		$queryTags[] = $db->escape_string($tag);
	}
	$r = '';
	if($and)
	{
		$r .= ' AND ';
	}
	if($prefix)
	{
		$r .= 'tags.';
	}
	$r .= 'name';
	if($not) {
		$r .= ' NOT ';
	}
	$r .= ' IN ('.implode(', ', $queryTags).')';
	if(count($queryTags))
	{
		return $r;
	}
	else
	{
		if($not) {
			return '';
		} else {
			return '1=0';
		}
	}
}


function tags_in_disforum($forum)
{
	global $mybb;

	$forums = $mybb->settings['tags_disallowedforums'];
	if($forums == -1)
	{
		return true;
	}
	elseif($forums == 0)
	{
		return false;
	}

	$forums = explode(',', $forums);

	return in_array($forum, $forums);
}
