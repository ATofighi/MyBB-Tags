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
require_once MYBB_ROOT.'inc/plugins/Tags/upgrade.php';
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
// TODO: cli support
if($mybb->get_input('action') == 'admin' && $mybb->usergroup['cancp']) {
	if($mybb->get_input('action2') == 'upgrade') {
		$from = $mybb->get_input('from', MyBB::INPUT_INT);
		if(!$from) {
			$from = 1; // TODO: select version
		}
		header("location: tag.php?action=admin&action2=upgrade_proccess&step={$from}&start=0");
	}
	elseif($mybb->get_input('action2') == 'upgrade_proccess') {
		@set_time_limit(300);
		echo '<h1>Tags Upgrade:</h1>';
		@ob_flush();
		@flush();
		$func = 'tags_upgrade_'.$mybb->get_input('step', MyBB::INPUT_INT);
		$start = $mybb->get_input('start', MyBB::INPUT_INT);
		if($start < 0) {
			$start = 0;
		}
		if(function_exists($func)) {
			list($limit, $remaining) = $func($start);
			if($remaining) {
				$next = $lower + $limit;
				echo '<script src="jscripts/jquery.js"></script>';
				echo "<form method=\"post\"><input type=\"hidden\" name=\"ipstart\" value=\"$next\" /><input type=\"submit\" class=\"submit_button\">";
				echo "<script type=\"text/javascript\">$(document).ready(function() { var button = $('.submit_button'); if(button) { button.val('Automatically Redirecting...'); button.prop('disabled', true); button.css('color', '#aaa'); button.css('border-color', '#aaa'); document.forms[0].submit(); } });</script>";
				@ob_flush();
				@flush();
			}
			else {
				$next = $mybb->get_input('step', MyBB::INPUT_INT) + 1;
				if($next < TAGS_LAST_REVERSION) {
					echo "<a href=\"tag.php?action=admin&action2=upgrade_proccess&step={$next}&start=0\">Next Step</a>";
				}
				else {
					echo '<a href="index.php">Back to site.</a>';
				}
			}
		}
		else {
			echo 'Upgrade is not avalible.';
		}
	}
	elseif($mybb->get_input('action2') == 'make_tags') {
		@set_time_limit(300);
		echo '<h1>Create Tags:</h1>';
		@ob_flush();
		@flush();
		$start = $mybb->get_input('start', MyBB::INPUT_INT);
		if($start < 0) {
			$start = 0;
		}

		$limit = 5000;
	    $upper = $lower + $limit;

	    $query = $db->simple_select('threads', 'COUNT(tid) as cnt');
	    $cnt = $db->fetch_array($query);
	    if($upper > $cnt['cnt'])
	    {
	    	$upper = $cnt['cnt'];
	    }

	    $remaining = $upper-$cnt['cnt'];

	    echo "<p>Inserting Tags {$lower} to {$upper} ({$cnt['cnt']} Total)</p>";
	    @ob_flush();
	    @flush();

	    $query = $db->simple_select("threads", "tid, subject", "", array('limit_start' => $lower, 'limit' => $limit));
		
	    $tags = array();
		$insert = array();
		$tids = array();

	    while($row = $db->fetch_array($query)) {
			$tids[] = $row['tid'];
	    	$subject = $row['subject'];
			$newTags = preg_replace("#([".preg_quote("+,./-%")."]+)#is", " ", $subject);
			$newTags = trim($newTags);
			$newTags = array_unique(explode(' ', $newTags));
			foreach($newTags as $tag) {
				if($tag) {
					$tags[] = $tag;
					$insert[] = array(
						'tid' => $row['tid'],
						'name' => $tag
					);
				}
			}
	    }

		$currentTags = array();
		if(!empty($tids))
		{
			$query = $db->simple_select("tags", "tid, name", "tid IN (".implode(",", $tids).")");
			while($row = $db->fetch_array($query))
			{
				$currentTags[] = array(
					'tid' => $row['tid'],
					'name' => $row['name']
				);
			}
		}

		array_unique($tags);

		$insert = array_diff($insert, $currentTags);

		if(!empty($insert))
		{
			$db->insert_query_multiple('tags', $insert);
		}
	    DBTagsSlug::newSlugs($tags);

	    echo "<p>Done.</p>";


		if($remaining) {
			$next = $lower + $limit;
			echo '<script src="jscripts/jquery.js"></script>';
			echo "<form method=\"post\"><input type=\"hidden\" name=\"ipstart\" value=\"$next\" /><input type=\"submit\" class=\"submit_button\">";
			echo "<script type=\"text/javascript\">$(document).ready(function() { var button = $('.submit_button'); if(button) { button.val('Automatically Redirecting...'); button.prop('disabled', true); button.css('color', '#aaa'); button.css('border-color', '#aaa'); document.forms[0].submit(); } });</script>";
			@ob_flush();
			@flush();
		}
	}
	elseif($mybb->get_input('action2') == 'remove_empties') {
		$db->delete_query('tags', "name = ''");
		$db->delete_query('tags_slug', "name = ''");
		echo 'Done.';
	}
	elseif($mybb->get_input('action2') == 'recount') {// TODO: move to admincp
		@set_time_limit(300);
		echo '<h1>Recount Tags:</h1>';
		@ob_flush();
		@flush();
		$start = $mybb->get_input('start', MyBB::INPUT_INT);
		if($start < 0) {
			$start = 0;
		}

		$limit = 5000;
	    $upper = $lower + $limit;

	    $query = $db->simple_select('tags_slug', 'COUNT(slug) as cnt');
	    $cnt = $db->fetch_array($query);
	    if($upper > $cnt['cnt'])
	    {
	    	$upper = $cnt['cnt'];
	    }

	    $remaining = $upper-$cnt['cnt'];

	    echo "<p>Fix Tags Count {$lower} to {$upper} ({$cnt['cnt']} Total)</p>";
	    @ob_flush();
	    @flush();

	    $query = $db->simple_select("tags_slug", "*", "", array('limit_start' => $lower, 'limit' => $limit));

	    $names = array();
		$count = array();

	    while($row = $db->fetch_array($query)) {
			$names[] = $row['name'];
			$count[$row['name']] = 0;
	    }

			if(!empty($names) && !empty($count)) {

				$query = $db->simple_select("tags", "*", "name IN (".tags_in_query($names).")");
				
				while($row = $db->fetch_array($query)) {
					$count[$row['name']]++;
				}
				
				
				foreach($count as $name => $val) {
					$db->update_query("tags_slug", array("count" => (int)$val),
					"name='".$db->escape_string($name)."'");
				}
				
			}

	    echo "<p>Done.</p>";


		if($remaining) {
			$next = $lower + $limit;
			echo '<script src="jscripts/jquery.js"></script>';
			echo "<form method=\"post\"><input type=\"hidden\" name=\"ipstart\" value=\"$next\" /><input type=\"submit\" class=\"submit_button\">";
			echo "<script type=\"text/javascript\">$(document).ready(function() { var button = $('.submit_button'); if(button) { button.val('Automatically Redirecting...'); button.prop('disabled', true); button.css('color', '#aaa'); button.css('border-color', '#aaa'); document.forms[0].submit(); } });</script>";
			@ob_flush();
			@flush();
		}

	}
	elseif($mybb->get_input('action2') == 'remove_bads') {
		// TODO: move to tasks
		echo '<h1>Remove Bad Tags:</h1>';
		$db->delete_query('tags', tags_getbads(false, false, false));
		$db->delete_query('tags_slug', tags_getbads(false, false, false));
		echo 'Done.';
	}
	// TODO: remove duplicated tags.

	exit;
}

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


	$query = $db->simple_select('tags_slug', 'COUNT(slug) as slugsCount', "", array("limit" => 1));
	$count = $db->fetch_field($query, 'slugsCount');
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
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
{$sitemaps}
</sitemapindex>
";
	exit;
}
elseif($mybb->get_input('action') == 'sitemap')
{
	$query = $db->simple_select('tags_slug', 'MAX(count) as maxcount', "", array("limit" => 1));
	$maxCount = $db->fetch_field($query, 'maxcount');
	$page = $mybb->get_input('page', MyBB::INPUT_INT);
	if($page < 1)
		$page = 1;

	$start = ($page-1) * 300;

	$query = DBTagsSlug::get('', array(
		'orderBy' => 'count',
		'orderType' => 'DESC',
		'limit' => "{$start}, 300"
	));

	$sitemaps = '';
	while($tag = $db->fetch_array($query))
	{
		$url = get_tag_link($tag['slug']);
		$priority = min(round($tag['count']/$maxCount, 2), 1);
		$sitemaps .= "
  <url>
    <loc>{$mybb->settings['bburl']}/{$url}</loc>
    <priority>{$priority}</priority>
  </url>
";
	}

	header('Content-type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
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