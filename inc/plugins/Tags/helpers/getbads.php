<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

function tags_getbads($and = true, $prefix = true)
{
	global $mybb;
	$b = $mybb->settings['tags_bad'];
	$b = str_replace(array("\r\n", "\n", "\r"), ',', $b);
	$b = tags_string2tag($b);
	$tags = explode(',', $b);
	$tags_hash = array();
	foreach($tags as $tag)
	{
		if($tag == '')
		{
			continue;
		}

		if($tag && !in_array("'".md5($tag)."'", $tags_hash))
		{
			array_push($tags_hash, "'".md5($tag)."'");
		}
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
	$r .= 'hash NOT IN ('.implode(', ', $tags_hash).')';
	if(count($tags_hash))
	{
		return $r;
	}
	else
	{
		return '';
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