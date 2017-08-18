<?php

function tags_in_query($arr) {
    global $db;
    $escaped = array();
    foreach($arr as $val) {
        $escaped[] = "'".$db->escape_string($val)."'";
    }
    return implode(', ', $escaped);
}
