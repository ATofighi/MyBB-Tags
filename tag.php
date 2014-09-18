<?php
/**
 * MyBB 1.8
 * Copyright 2014 My-BB.Ir Group, All Rights Reserved
 *
 * By: AliReza_Tofighi
 * Website: http://my-bb.ir
 *
 */

define('IN_MYBB', 1);
define('NO_ONLINE', 1);
define('THIS_SCRIPT', 'index.php');

$templatelist = "";

require_once './global.php';
require_once MYBB_ROOT.'inc/functions_forumlist.php';
require_once MYBB_ROOT.'inc/class_parser.php';
$parser = new postParser;
$parser_options = array();
$parser_options['allow_html'] = 0;
$parser_options['allow_mycode'] = 1;
$parser_options['allow_smilies'] = 1;
$parser_options['allow_imgcode'] = 0;
$parser_options['allow_videocode'] = 0;
$parser_options['me_username'] = 0;
$parser_options['filter_badwords'] = 1;

$dir = 'left';
$no_dir = 'right';
if($lang->settings['rtl'])
{
	$dir = 'right';
	$no_dir = 'left';
}

// get forums user cannot view
$unviewable = get_unviewable_forums(true);
if($unviewable)
{
	$unviewwhere = " AND fid NOT IN ($unviewable)";
	$tunviewwhere = " AND thread.fid NOT IN ($unviewable)";
}
else
{
	$unviewwhere = '';
}

// get inactive forums
$inactive = get_inactive_forums();
if($inactive)
{
	$inactivewhere = " AND fid NOT IN ($inactive)";
	$tinactivewhere = " AND thread.fid NOT IN ($inactive)";
}
else
{
	$inactivewhere = '';
}

$name = $mybb->get_input('name');
$name = $parser->parse_badwords($name);
$name = ltrim(rtrim(trim($name)));
$name = my_strtolower($name);
$name = htmlspecialchars_uni($name);
$hash = md5($name);
add_breadcrumb('Tags', get_tag_link());

$tag_link = get_tag_link();

if(!$name)
{

	$tag = <<<EOT
	<html>
		<head>
			<title>{$mybb->settings['bbname']} - Tags</title>
			{$headerinclude}
		</head>
		<body>
			{$header}
			<form action="{$tag_link}" method="get">
			<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder clear">
				<tr>
					<td class="thead" colspan="2">
						<strong>Search</strong>
					</td>
				</tr>
				<tr>
					<td class="trow1">
						<input type="text" class="textbox" placeholder="Please enter a tag..." name="name" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" />
					</td>
					<td class="trow1" width="50">
						<input type="submit" class="button" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="Go" />
					</td>
				</tr>
			</table>
			</form>
			{$footer}
		</body>
	</html>
EOT;
	output_page($tag);
}
else
{
	add_breadcrumb($name, get_tag_link($name));

	$query = $db->query("SELECT COUNT(thread.tid) as numrows from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 WHERE tag.hash = '{$hash}' And thread.tid > 0 and thread.visible='1'{$tunviewwhere}{$tinactivewhere} AND thread.closed NOT LIKE 'moved|%'
						 limit 1");
	$nums = $db->fetch_array($query);
	$count = $nums['numrows'];
	$page = $mybb->get_input('page', 1);
	$pages = $count / 10;
	$pages = ceil($pages);

	if($page > $pages || $page <= 0)
	{
		$page = 1;
	}

	if($page)
	{
		$start = ($page-1) * 10;
	}
	else
	{
		$start = 0;
		$page = 1;
	}

	$multipage = multipage($count, 10, $page, get_tag_link($name));

	$query = $db->query("SELECT thread.tid, post.message, post.username, post.uid, thread.subject, thread.views, thread.replies from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 LEFT JOIN `".TABLE_PREFIX."posts` post on(thread.firstpost = post.pid)
						 WHERE tag.hash = '{$hash}' And thread.tid > 0 And post.pid > 0 and thread.visible='1'{$tunviewwhere}{$tinactivewhere} AND thread.closed NOT LIKE 'moved|%'
						 GROUP BY thread.tid
						LIMIT {$start}, 10");

	$tags = '';
	while($tag = $db->fetch_array($query))
	{
		if(!$tag['tid'])
			continue;


		if($mybb->seo_support == true)
		{
			$highlight = "?highlight=".urlencode($name);
		}
		else
		{
			$highlight = "&amp;highlight=".urlencode($name);
		}

		$tag['message'] = my_substr($tag['message'], 0, 500);
		$tag['message'] = $parser->parse_message($tag['message'], $parser_options);
		$tag['subject'] = htmlspecialchars_uni($parser->parse_badwords($tag['subject']));
		$tag['threadlink'] = get_thread_link($tag['tid']);
		$tag['profilelink'] = build_profile_link($tag['username'], $tag['uid']);
		$tags .= <<<EOT
	<tr>
		<td class="tcat" colspan="2">
			<div class="float_{$no_dir}">
				By: {$tag['profilelink']} - Replies: <a href="javascript:MyBB.whoPosted({$tag['tid']});">{$tag['replies']}</a> - Views: {$tag['views']}
			</div>
			<a href="{$tag['threadlink']}{$highlight}"><strong>{$tag['subject']}</strong></a>
		</td>
	</tr>
	<tr>
		<td class="trow1" colspan="2">
			<div style="max-height:100px;overflow:auto">
				{$tag['message']}
			</div>
		</td>
	</tr>
EOT;
	}

	if($tags == '')
	{
		error("We can't find anything for this Tag");
	}

	$tag = <<<EOT
	<html>
		<head>
			<title>Tags - {$name}</title>
			{$headerinclude}
		</head>
		<body>
			{$header}
			<form action="{$tag_link}" method="get">
			<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder clear">
				<tr>
					<td class="thead" colspan="2">
						<strong>Tags</strong>
					</td>
				</tr>
				<tr>
					<td class="trow1">
						<input type="text" class="textbox" name="name" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="{$name}" />
					</td>
					<td class="trow1" width="50">
						<input type="submit" class="button" style="width:100%;box-sizing:border-box;padding:5px 8px;font-size:16px;" value="Go" />
					</td>
				</tr>
				{$tags}
			</table>
			</form>
			{$multipage}
			{$footer}
		</body>
	</html>
EOT;
	output_page($tag);
}
