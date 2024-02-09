<?php
/**
 * MyBB-Tags 3
 * Copyright 2014-2015 My-BB.Ir Group, All Rights Reserved
 *
 * Website: http://my-bb.ir
 * Author: AliReza_Tofighi
 *
 */

// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

define('TAGS_ROOT', MYBB_ROOT.'/inc/plugins/Tags');
define('TAGS_LAST_REVERSION', 3);	

if(defined('THIS_SCRIPT') && in_array(THIS_SCRIPT, array('tag.php', 'showthread.php', 'index.php', 'forumdisplay.php')))
{
    global $templatelist;
    if(isset($templatelist) && $templatelist != '')
    {
        $templatelist .= ',';
    }
	else
	{
		$templatelist = '';
	}

    $templatelist .= 'tags_input,tags_box,tags_box_tag,tags_box_tag_sized,tags_search,tags_thread,tags_notags,tags_viewtag';
}


function tags_info()
{
	global $lang, $mybb;
	$lang->load('tags');
	$info = array(
		"name"			=> $lang->tags_pluginname,
		"description"	=> '',
		"website"		=> "https://github.com/ATofighi/MyBB-Tags",
		"author"		=> "Alireza Tofighi",
		"authorsite"	=> "",
		"version"		=> "3.0.3",
		"compatibility" => "18*"
	);

	if (tags_is_installed() && $mybb->settings['tags_enabled'])
	{
		$info['description'] .= '<br /><Strong>Admin Actions:</strong><br /><ul>';
		$info['description'] .= '<li><a href="'.$mybb->settings['bburl'].'/tag.php?action=admin&action2=recreateSlugs" target="_blank">Create missing Slugs</a></li>';
		$info['description'] .= '<li><a href="'.$mybb->settings['bburl'].'/tag.php?action=admin&action2=make_tags" target="_blank">Create tags for current topics</a></li>';
		$info['description'] .= '<li><a href="'.$mybb->settings['bburl'].'/tag.php?action=admin&action2=recount" target="_blank">Recount tags</a></li>';
		$info['description'] .= '</ul>';
	}
	return $info;
	
}

require_once TAGS_ROOT.'/autoload.php';
