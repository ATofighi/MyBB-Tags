<?php
/**
 * MyBB-Tags
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

if($mybb->get_input('action') == 'sitemap-index')
{
	$bad_tags = tags_getbads(true);

	$count = DBTags::count();
	$pages = $count / 300; // TODO: sitemap per page
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
	echo "
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
{$sitemaps}
</sitemapindex>
";
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

	$query = DBTags::get("MAX(threads.dateline) as lastmod, SUM(views) as sumviews, tags.*", '', array(
		'orderBy' => 'sumviews',
		'orderType' => 'DESC',
		'limit' => "{$start}, 300"
	));

	$sitemaps = '';
	while($tag = $db->fetch_array($query))
	{
		$url = get_tag_link(urldecode(str_replace(',','-',$tag['name'])));
		$lastmod = date('c', $tag['lastmod']);
		$priority = min(round($tag['sumviews']/$maxviews, 2), 1);
		$sitemaps .= "
  <url>
    <loc>{$mybb->settings['bburl']}/{$url}</loc>
    <lastmod>{$lastmod}</lastmod>
    <priority>{$priority}</priority>
  </url>
";
	}

	header('Content-type: text/xml');
	echo "
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
{$sitemaps}
</urlset>
";
	exit;
}

$page = $mybb->get_input('page', 1);

$name = $mybb->get_input('name');
if(!$name && $mybb->get_input('slug')) {
	$slug = $mybb->get_input('slug');
	$name = DBTags::getNameBySlug($slug);
}

$name = $parser->parse_badwords($name);
$name = htmlspecialchars_uni($name);


$mybb->settings['tags_per_page'] = (int)($mybb->settings['tags_per_page']);
if($mybb->settings['tags_per_page'] <= 0 || $mybb->settings['tags_per_page'] >= 100)
{
	$mybb->settings['tags_per_page'] = 10;
}

add_breadcrumb($lang->tags, get_tag_link());

$tag_link = get_tag_link();


if($name && $mybb->settings['tags_seo'] && $mybb->settings['tags_forceseo'] && tags_current_url() != $mybb->settings['bburl'].'/'.get_tag_link($url_name) && tags_current_url() != $mybb->settings['bburl'].'/'.get_tag_link($url_name)."?page={$page}")
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

if(!$name)
{

	eval('$tag = "'.$templates->get('tags_search').'";');
	output_page($tag);
}
else
{
	add_breadcrumb($name, get_tag_link($name));

	$bad_tags = tags_getbads(true);

	$count = DBTags::countThreads("tags.name = '".$db->escape_string($name)."'");

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

	$query = DBTags::get(
		"threads.tid, posts.message, posts.username, posts.uid, threads.subject, threads.views, threads.replies",
		"tags.name = '".$db->escape_string($name)."'",
		array(
//			'groupBy' => 'threads.tid',
			'limit' => "{$start}, {$mybb->settings['tags_per_page']}"
		)
	);

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
