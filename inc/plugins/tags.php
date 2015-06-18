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
	global $lang;
	$lang->load('tags');
	return array(
		"name"			=> $lang->tags_pluginname,
		"description"	=> '',
		"website"		=> "http://myb-b.ir",
		"author"		=> "My-BB.Ir Group",
		"authorsite"	=> "http://my-bb.ir",
		"version"		=> "3.0.1",
		"compatibility" => "18*"
	);
}

require_once TAGS_ROOT.'/autoload.php';