<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

function tags_setting_value($setting, $value)
{
	global $mybb;
	if(isset($mybb->settings[$setting]))
	{
		return $mybb->settings[$setting];
	}
	else
	{
		return $value;
	}
}