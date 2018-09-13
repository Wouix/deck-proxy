<?php

function getElementsByClass(&$parent_node, $tag_name, $class_name) {
    $nodes = array();

    $child_node_list = $parent_node->getElementsByTagName($tag_name);
    for ($i = 0; $i < $child_node_list->length; $i++) {
        $temp = $child_node_list->item($i);
        if (stripos($temp->getAttribute('class'), $class_name) !== false) {
            $nodes[] = $temp;
        }
    }

    return $nodes;
}

function dd($m) {
    echo '<pre>';
    print_r($m);
    die();
}