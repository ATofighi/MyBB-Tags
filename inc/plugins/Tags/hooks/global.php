<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

$plugins->add_hook("global_start", "tags_global_start");

function tags_global_start()
{
	global $mybb;
	if($mybb->settings['tags_seo'])
	{
		if($mybb->settings['tags_urlscheme'] && strstr($mybb->settings['tags_urlscheme'], '{name}'))
		{
			define('TAG_URL', $mybb->settings['tags_urlscheme']);
		}
		else
		{
			define('TAG_URL', "tag-{name}.html");
		}
		define('TAG_URL_PAGE', "tag.html");
	}
	else
	{
		define('TAG_URL', "tag.php?name={name}");
		define('TAG_URL_PAGE', "tag.php");
	}
	
	if($mybb->settings['tags_disallowedforums'] == -1)
	{
		$mybb->settings['tags_enabled'] = 0;
	}
}