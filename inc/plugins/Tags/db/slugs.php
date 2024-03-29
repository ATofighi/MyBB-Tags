<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

class DBTagsSlug
{
	static function get($where = '', $opt = array())
	{
		global $db;
		$unviewable = DBTags::getUnviewable('threads');;

		$opt = array_merge(array(
			'limit' => '',
			'orderBy' => '',
			'orderType' => 'asc',
		), $opt);

		if(!$where)
		{
			$where = '1=1';
		}

		$where = "({$where}) AND threads.tid != '0' AND threads.visible = '1' AND threads.closed NOT LIKE 'moved|%' AND {$unviewable}".tags_getbads();

		$query = "SELECT slugs.slug, slugs.name, slugs.count, COUNT(threads.tid) as tcount FROM ".TABLE_PREFIX."tags_slug slugs\n";
		$query .= "LEFT JOIN ".TABLE_PREFIX."tags tags on(tags.name = slugs.name)\n";
		$query .= "LEFT JOIN ".TABLE_PREFIX."threads threads on(tags.tid = threads.tid)\n";
		$query .= "WHERE ".$where."\n";

		$query .= "group by slugs.slug, slugs.name, slugs.count \n";
		$query .= "having tcount > 0 \n";
		if($opt['orderBy'])
		{
			if(strstr($opt['orderBy'], '.')) {
				$opt['orderBy'] = ''.TABLE_PREFIX.$opt['orderBy'].'';
			}
			$query .= "order by {$opt['orderBy']} {$opt['orderType']}\n";
		}
		if($opt['limit'])
		{
			$query .= "limit {$opt['limit']}\n";
		}

		return $db->query($query);
	}

	static function find($id)
	{
		global $db;
		$dbTags = new DBTagsSlug;
		$query = $dbTags->get('slugs.id = '.(int)$id);
		return $db->fetch_array($query);
	}

	static function findBySlug($slug)
	{
		global $db;
		$dbTags = new DBTagsSlug;
		$query = $dbTags->get("slugs.slug = '".$db->escape_string($slug)."'");
		return $db->fetch_array($query);
	}


	static function findByName($name)
	{
		global $db;
		$dbTags = new DBTagsSlug;
		$query = $dbTags->get("slugs.name = '".$db->escape_string($name)."'");
		return $db->fetch_array($query);
	}

	static function getNewSlugNumber($slug) {
		global $db;
		$query = $db->simple_select('tags_slug', 'MAX(slug) as last',
					"slug = '".$db->escape_string($slug)."'
					or slug LIKE '".$db->escape_string($slug)."--%'");
		if($db->num_rows($query) == 0) {
			return 0;
		}
		$last = $db->fetch_field($query, 'last');
		if(!$last) {
			return 0;
		}
		$last = str_replace($slug, '', $last);
		$last = str_replace('--', '', $last);
		$last = (int)$last;
		if($last == 0) $last = 1;
		return $last + 1;

	}

	static function insert($array) {
		global $db;

		$db->insert_query('tags_slug', $array);
	}

	static function plusPlus($names) {
		global $db;
		$db->query("UPDATE ".TABLE_PREFIX."tags_slug
					SET count = count + 1
					WHERE name IN (".tags_in_query($names).")");
	}

	static function minusMinus($names) {
		global $db;
		$db->query("UPDATE ".TABLE_PREFIX."tags_slug
					SET count = count - 1
					WHERE name IN (".tags_in_query($names).")");
	}

	static function removeEmpties() {
		global $db;
		$db->delete_query('tags_slug', 'count = 0');
	}

	static function newSlugs($tags) {
		global $db;

		foreach($tags as $tag) {
			if(!DBTagsSlug::findByName($tag)) {
				$slug = tags_slug($tag);
				if(!$slug) {
					$slug = 'empty';
				}
				$last = DBTagsSlug::getNewSlugNumber($slug);
				if(!$last) {
					$insert = array(
						'name' => $db->escape_string($tag),
						'slug' => $db->escape_string($slug),
						'count' => 0
					);
				}
				else {
					$insert = array(
						'name' => $db->escape_string($tag),
						'slug' => $db->escape_string($slug.'--'.$last),
						'count' => 0
					);
				}
				DBTagsSlug::insert($insert);

			}
		}

	}

}
