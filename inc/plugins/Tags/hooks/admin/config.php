<?php
$plugins->add_hook("admin_config_settings_begin", "tags_admin_config_settings_begin");

function tags_admin_config_settings_begin()
{
	global $lang;
	$lang->load('tags');
}

$plugins->add_hook("admin_settings_print_peekers", "tags_admin_settings_print_peekers");

function tags_admin_settings_print_peekers(&$peekers)
{
	$peekers[] = 'new Peeker($(".setting_tags_seo"), $("#row_setting_tags_forceseo"), 1, true)';
	return $peekers;
}