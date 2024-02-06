<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

// hook into search
$plugins->add_hook("search_do_search_process", "tags_search_process");


function tags_search_process() {
	// searcharray from search
	global $mybb, $db, $searcharray;

	// check settings or return with nothing
	if(!$mybb->settings['tags_hooksearch'] || !$mybb->settings['tags_enabled'])
		return;

	// build query
	$query = DBTags::get(
		"threads.tid",
		"tags.name = '".$db->escape_string($searcharray['keywords'])."'"
	);

	// cache already matching threads	
	$foundThreadsWithTag = array();
	if(!empty($searcharray['threads'])){
		$foundThreadsWithTag = explode(",",$searcharray['threads']);
	}

	// push new found threads
	while($tag = $db->fetch_array($query))
	{
		if($tag['tid'])
			array_push($foundThreadsWithTag, $tag['tid']);
	}

	// set back found tids into searcharray
	if(!empty($foundThreadsWithTag)){
		$tidlist= implode(",",array_unique($foundThreadsWithTag, SORT_NUMERIC));
		$searcharray['threads'] = $tidlist;
	}
}