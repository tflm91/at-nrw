<?php

/* select all objects of a specified table */
function select_all($table_name) {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name");
}

/* select an object specified by its ID */
function select_one($table_name, $object_id) {
    global $wpdb;
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id=%d", $object_id));
}

/* select all objects of a specified category */
function select_of_category($table_name, $category_id) {
    global $wpdb;
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE categoryId = %d ORDER BY name",
        $category_id
    ));
}

/* select all m:n-connected objects of a specified object */
function select_connected(
    $connection_table,
    $search_column,
    $target_table,
    $connection_column,
    $search_id
) {
    global $wpdb;
    $stmt = "SELECT {$target_table}.id AS id, {$target_table}.name AS name FROM {$connection_table}"
    . " INNER JOIN {$target_table} ON {$target_table}.id = {$connection_table}.{$connection_column}"
    . " WHERE {$connection_table}.{$search_column} = %d"
    . " ORDER BY {$target_table}.name";


    return $wpdb->get_results($wpdb->prepare($stmt, $search_id));
}


/* select all objects without category */
function select_without_category(
    $connection_table,
    $search_column,
    $target_table,
    $connection_column
) {
    global $wpdb;
    $stmt = "SELECT {$target_table}.id AS id, {$target_table}.name AS name FROM {$target_table}"
        . " LEFT JOIN {$connection_table} ON {$target_table}.id = {$connection_table}.{$connection_column}"
        . " WHERE {$connection_table}.{$search_column} IS NULL";
    return $wpdb->get_results($stmt);
}

/* count all items of a category specified by its ID */
function count_items($connection_table, $category_id) {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $connection_table WHERE categoryId = %d", $category_id));
}

/* select only items which satisfy a condition */
function select_conditional($table_name, $conditional_column) {
    global $wpdb;
    return $wpdb->get_results("SELECT id, name FROM $table_name WHERE $conditional_column = TRUE");
}
?>
