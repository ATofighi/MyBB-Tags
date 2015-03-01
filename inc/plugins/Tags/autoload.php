<?php
/**
 * MyBB-Tags
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

// Helpers:
require_once TAGS_ROOT.'/helpers/getbads.php';
require_once TAGS_ROOT.'/helpers/getsize.php';
require_once TAGS_ROOT.'/helpers/settings.php';
require_once TAGS_ROOT.'/helpers/url.php';

// Database:
require_once TAGS_ROOT.'/db/tags.php';

// Install:
require_once TAGS_ROOT.'/install/active.php';
require_once TAGS_ROOT.'/install/deactive.php';
require_once TAGS_ROOT.'/install/install.php';
require_once TAGS_ROOT.'/install/is_installed.php';
require_once TAGS_ROOT.'/install/uninstall.php';

//Hooks:
require_once TAGS_ROOT.'/hooks/global.php';
require_once TAGS_ROOT.'/hooks/index.php';
require_once TAGS_ROOT.'/hooks/newthread.php';
require_once TAGS_ROOT.'/hooks/editpost.php';
require_once TAGS_ROOT.'/hooks/showthread.php';
require_once TAGS_ROOT.'/hooks/forumdisplay.php';
require_once TAGS_ROOT.'/hooks/datahandler_post.php';
require_once TAGS_ROOT.'/hooks/admin/config.php';