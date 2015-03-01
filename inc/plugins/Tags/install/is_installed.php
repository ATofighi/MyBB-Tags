<?php
function tags_is_installed()
{
	global $mybb;
	return isset($mybb->settings['tags_enabled']);
}
