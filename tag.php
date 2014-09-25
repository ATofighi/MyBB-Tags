<?php
/**
 * MyBB-Tags 2
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

$lang->load('tags');


if($mybb->settings['tags_enabled'] == 0)
{
	error($lang->tags_disabled);
}


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

if($mybb->get_input('action') == 'sitemap-index')
{
	$bad_tags = tags_getbads(true);

	$query = $db->query("SELECT tag.id from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 WHERE thread.tid > 0{$bad_tags} and thread.visible='1'{$tunviewwhere}{$tinactivewhere} AND thread.closed NOT LIKE 'moved|%'
						 GROUP BY tag.hash");
	$count = $db->num_rows($query);
	unset($query);
	$pages = $count / 300;
	$pages = ceil($pages);
	$sitemaps = '';
	for($i = 1; $i <= $pages; $i++)
	{
		$sitemaps .= <<<EOT
  <sitemap>
    <loc>{$mybb->settings['bburl']}/tag.php?action=sitemap&amp;page={$i}</loc>
  </sitemap>
EOT;
	}

	header('Content-type: text/xml');
	echo <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$sitemaps}
</sitemapindex>
EOT;
	exit;
}
elseif($mybb->get_input('action') == 'sitemap')
{
	$query = $db->simple_select('threads', 'MAX(views) as maxviews', "", array("limit" => 1));
	$maxviews = $db->fetch_field($query, 'maxviews');
	$page = $mybb->get_input('page', 1);
	if($page < 1)
		$page = 1;

	$start = ($page-1) * 300;

	$bad_tags = tags_getbads(true);

	$query = $db->query("SELECT MAX(thread.dateline) as lastmod, SUM(views) as sumviews, tag.* from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 WHERE thread.tid > 0{$bad_tags} and thread.visible='1'{$tunviewwhere}{$tinactivewhere} AND thread.closed NOT LIKE 'moved|%'
						 GROUP BY tag.hash
						 ORDER BY sumviews DESC
						LIMIT {$start}, 300");
	$sitemaps = '';
	while($tag = $db->fetch_array($query))
	{
		$url = get_tag_link(urldecode(str_replace(',','-',$tag['name'])));
		$lastmod = date('c', $tag['lastmod']);
		$priority = min(round($tag['sumviews']/$maxviews, 2), 1);
		$sitemaps .= <<<EOT
  <url>
    <loc>{$mybb->settings['bburl']}/{$url}</loc>
    <lastmod>{$lastmod}</lastmod>
    <priority>{$priority}</priority>
  </url>
EOT;
	}

	header('Content-type: text/xml');
	echo <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$sitemaps}
</urlset>
EOT;
	exit;
}

$page = $mybb->get_input('page', 1);

$name = $mybb->get_input('name');
$name = $parser->parse_badwords($name);
$name = tags_string2tag($name);
$name = htmlspecialchars_uni($name);
$url_name = urlencode(str_replace(',', '-', $name));
$names = explode(',', $name);
$hash = array();
foreach($names as $n)
{
	array_push($hash, "'".md5($n)."'");
}

$hash = implode(', ', $hash);

$mybb->settings['tags_per_page'] = (int)($mybb->settings['tags_per_page']);
if($mybb->settings['tags_per_page'] <= 0 || $mybb->settings['tags_per_page'] >= 100)
{
	$mybb->settings['tags_per_page'] = 10;
}

add_breadcrumb($lang->tags, get_tag_link());

$tag_link = get_tag_link();


if($name && $mybb->settings['tags_seo'] && tags_current_url() != $mybb->settings['bburl'].'/'.get_tag_link($url_name) && tags_current_url() != $mybb->settings['bburl'].'/'.get_tag_link($url_name)."?page={$page}")
{
	if($page)
	{
		header("location: ".get_tag_link($url_name)."?page={$page}");
	}
	else
	{
		header("location: ".get_tag_link($url_name));
	}
	exit;
}

if(!$name || !$hash)
{

	eval('$tag = "'.$templates->get('tags_search').'";');
	output_page($tag);
}
else
{
	add_breadcrumb($name, get_tag_link($name));

	$bad_tags = tags_getbads(true);

	$query = $db->query("SELECT COUNT(thread.tid) as numrows from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 WHERE tag.hash IN ({$hash}) And thread.tid > 0{$bad_tags} and thread.visible='1'{$tunviewwhere}{$tinactivewhere} AND thread.closed NOT LIKE 'moved|%'
						 limit 1");
	$nums = $db->fetch_array($query);
	$count = $nums['numrows'];
	$pages = $count / $mybb->settings['tags_per_page'];
	$pages = ceil($pages);

	if($page > $pages || $page <= 0)
	{
		$page = 1;
	}

	if($page)
	{
		$start = ($page-1) * $mybb->settings['tags_per_page'];
	}
	else
	{
		$start = 0;
		$page = 1;
	}

	$multipage = multipage($count, $mybb->settings['tags_per_page'], $page, get_tag_link($name));

	$bad_tags = tags_getbads(true);

	$query = $db->query("SELECT thread.tid, post.message, post.username, post.uid, thread.subject, thread.views, thread.replies from `".TABLE_PREFIX."tags` tag
						 LEFT JOIN `".TABLE_PREFIX."threads` thread on(tag.tid = thread.tid)
						 LEFT JOIN `".TABLE_PREFIX."posts` post on(thread.firstpost = post.pid)
						 WHERE tag.hash IN ({$hash}) And thread.tid > 0{$bad_tags} And post.pid > 0 and thread.visible='1'{$tunviewwhere}{$tinactivewhere} AND thread.closed NOT LIKE 'moved|%'
						 GROUP BY thread.tid
						LIMIT {$start}, {$mybb->settings['tags_per_page']}");

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
		eval('$tags .= "'.$templates->get('tags_thread').'";');
	}

	if($tags == '')
	{
		eval('$tags = "'.$templates->get('tags_notags').'";');
	}

	eval('$tag = "'.$templates->get('tags_viewtag').'";');
	output_page($tag);
}