<?php
function tags_uninstall()
{
	global $db, $mybb;
	
	$query = $db->simple_select('settinggroups', 'gid', "name='tags'");
	$gid = $db->fetch_field($query, 'gid');

	$db->delete_query('templates', "title LIKE 'tags\_%' AND sid='-1'");
	$db->delete_query("settinggroups", "name = 'tags'");

	$db->delete_query('settings', "gid = '{$gid}'");
	
	if($mybb->settings['tags_droptable'])
	{
		$db->drop_table('tags');
	}
	
	rebuild_settings();

}