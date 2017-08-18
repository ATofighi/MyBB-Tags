<?php

function tags_upgrade_1($lower) {
    global $db;
    echo 'Remove hash column...';
    @ob_flush();
    @flush();
    if($db->field_exists('hash', 'tags')) {
        $db->drop_column('tags', 'hash');
    }
    echo 'Done.<br>';
    @ob_flush();
    @flush();
    return array(
        1,
        0
    );
}

function tags_upgrade_2($lower) {
    global $db;

    $limit = 5000;
    $upper = $lower + $limit;

    $query = $db->simple_select('tags', 'COUNT(id) as cnt');
    $cnt = $db->fetch_array($query);
    if($upper > $cnt['cnt'])
    {
    	$upper = $cnt['cnt'];
    }

    $remaining = $upper-$cnt['cnt'];

    echo "<p>Inserting Tag Slugs {$lower} to {$upper} ({$cnt['cnt']} Total)</p>";
    @ob_flush();
    @flush();

    $query = $db->simple_select("tags", "name", "", array('limit_start' => $lower, 'limit' => $limit));

    $tags = array();

    while($row = $db->fetch_array($query)) {
    	$tags = $row['name'];
    }

    DBTagsSlug::newSlugs($tags);

    echo "<p>Done.</p>";
    return array(
        $limit,
        $remaining
    );
}
