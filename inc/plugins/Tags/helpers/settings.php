<?php
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