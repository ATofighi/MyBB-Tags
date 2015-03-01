<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

function tags_string2tag($s)
{
	global $mybb;

	$s = my_strtolower($s);
	$s = ltrim(rtrim(trim($s)));
	$s = str_replace(array("`","~","!","@","#","$","%","^","&","*","(",")","_","+","-","=","\\","|","]","[","{","}",'"',"'",";",":","/","."," ",">","<"), ",", $s);
	$s = ltrim(rtrim(trim($s, ','),','),',');
	$s = preg_replace("#([,]+)#si", ',', $s);

	// https://github.com/ATofighi/MyBB-Tags/issues/33
	$mybb->settings['tags_charreplace'] = ltrim(rtrim(trim(str_replace(array("\r\n", "\r"),"\n", $mybb->settings['tags_charreplace']))));
	$translations = explode("\n", $mybb->settings['tags_charreplace']);
	foreach($translations as $translation)
	{
		$translation = explode('=>', $translation, 2);
		$s = str_replace($translation[0], $translation[1], $s);
	}

	return $s;
}

function tags_current_url()
{
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}
	else
	{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function get_tag_link($name='')
{
	if($name == '')
	{
		$link = TAG_URL_PAGE;
	}
	else
	{
		$link = str_replace("{name}", $name, TAG_URL);
	}
	return htmlspecialchars_uni($link);
}