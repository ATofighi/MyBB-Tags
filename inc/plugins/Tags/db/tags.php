<?php
// Make sure we can't access this file directly from the browser.
if(!defined("IN_MYBB"))
{
	die("This file cannot be accessed directly.");
}

class DBTags
{
	function getUnviewable($tableName = '')
	{
		global $db;

		if($tableName)
		{
			$tableName = $tableName.'.';
		}
		$wheres = array();

		$wheres[] = '1=1';


		// get forums user cannot view
		$unviewable = get_unviewable_forums(true);
		if($unviewable)
		{
			$wheres[] = "{$tableName}fid NOT IN ($unviewable)";
		}

		// get inactive forums
		$inactive = get_inactive_forums();
		if($inactive)
		{
			$wheres[] = "{$tableName}fid NOT IN ($inactive)";
		}

		// get disallowed forums
		$disallowedforums = $db->escape_string($mybb->settings['tags_disallowedforums']);
		if($disallowedforums)
		{
			$wheres[] = "{$tableName} NOT IN ($disallowedforums)";
		}

		return implode(' AND ', $wheres);
	}

	static function get($select = '*', $where = '', $opt = array())
	{
		$dbTags = new DBTags;
		$unviewable = $dbTags->getUnviewable('threads');

		$opt = array_merge(array(
			'limit' => '',
			'orderBy' => '',
			'orderType' => 'asc',
			'groupBy' => 'tags.hash'
		), $opt);

		if(!$where)
		{
			$where = '1=1';
		}
		$where = "({$where}) AND threads.tid != '0' AND threads.visible = '1' AND threads.closed NOT LIKE 'moved|%' AND {$unviewable}".tags_getbads();

		$query = "SELECT {$select} FROM `".TABLE_PREFIX."tags` tags\n";
		$query .= "LEFT JOIN `".TABLE_PREFIX."threads` threads on(tags.tid = threads.tid)\n";
		$query .= "LEFT JOIN `".TABLE_PREFIX."posts` posts on(threads.firstpost = posts.pid)\n";
		$query .= "WHERE ".$where."\n";

		if($opt['groupBy'])
		{
			$query .= "group by {$opt['groupBy']}\n";
		}
		if($opt['orderBy'])
		{
			if(strstr($opt['orderBy'], '.')) {
				$opt['orderBy'] = '`'.TABLE_PREFIX.$opt['orderBy'].'`';
			}
			$query .= "order by {$opt['orderBy']} {$opt['orderType']}\n";
		}
		if($opt['limit'])
		{
			$query .= "limit {$opt['limit']}\n";
		}

		global $db;
		return $db->query($query);
	}

	static function count($where = '', $select = '')
	{
		global $db;
		if($select)
		{
			$select .= ', ';
		}
		$dbTags = new DBTags;
		$query = $dbTags->get($select.'COUNT(tags.id) as tagsCount', $where, array(
			'groupBy' => ''
		));

		return $db->fetch_field($query, 'tagsCount');
	}

	static function countThreads($where = '', $select = '')
	{
		global $db;
		if($select)
		{
			$select .= ', ';
		}
		$dbTags = new DBTags;
		$query = $dbTags->get($select.'COUNT(threads.tid) as threadsCount', $where, array(
			'groupBy' => ''
		));

		return $db->fetch_field($query, 'threadsCount');
	}

	static function find($id)
	{
		global $db;
		$dbTags = new DBTags;
		$query = $dbTags->get('*', 'tags.id = '.(int)$id);
		return $db->fetch_array($query);
	}

	static function findByHash($hash)
	{
		global $db;
		$dbTags = new DBTags;
		$query = $dbTags->get('*', "tags.hash = '".$db->escape_string($hash)."'");
		return $db->fetch_array($query);
	}

	static function findByName($name)
	{
		global $db;
		$dbTags = new DBTags;
		$query = $dbTags->get('*', "tags.name = '".$db->escape_string($name)."'");
		return $db->fetch_array($query);
	}

	static function findByTid($tid)
	{
		global $db;
		$dbTags = new DBTags;
		$query = $dbTags->get('*', 'tags.tid = '.(int)$tid);
		return $db->fetch_array($query);
	}
}