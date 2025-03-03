<?php

/* select all objects of a specified table */
function select_all($table_name, $order_by_name = true) {
    global $wpdb;
    $stmt = "SELECT * FROM $table_name";
    if ($order_by_name) {
        $stmt .= " ORDER BY name";
    }
    return $wpdb->get_results($stmt);
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

/* select only products which satisfy a condition */
function select_conditional_product($table_name, $conditional_column) {
    global $wpdb;
    return $wpdb->get_results("SELECT id, name FROM $table_name WHERE $conditional_column = TRUE");
}

/* select only products which do not satisfy a condition */
function select_non_conditional_product($table_name, $conditional_column) {
    global $wpdb;
    return $wpdb->get_results("SELECT id, name FROM $table_name WHERE $conditional_column = FALSE");
}


/* select only comprehensive links */
function select_conditional_links($table_name, $conditional_column) {
    global $wpdb;
    return $wpdb->get_results("SELECT URL, altText FROM $table_name WHERE $conditional_column = TRUE");
}

/* select additional links for disability category or product category */
function select_connected_links(
    $connection_table,
    $linkTable,
    $itemForeignKey,
    $linkForeignKey,
    $itemId
) {
    global $wpdb;
    $stmt = "SELECT $linkTable.URL AS URL, $linkTable.altText AS altText FROM $connection_table"
        . " INNER JOIN $linkTable ON $linkTable.id = {$connection_table}.{$linkForeignKey}"
        . " WHERE {$connection_table}.{$itemForeignKey} = %d";
    return $wpdb->get_results($wpdb->prepare($stmt, $itemId));
}

/* delete a specified element */
function delete_element($table_name, $object_id): void {
    global $wpdb;
    $wpdb->delete($table_name, ['id' => $object_id]);
}

/* select all ids associated to a specific object */
function select_associated_ids(
    $table_name,
    $object_id_column,
    $associated_id_column,
    $object_id) {
    global $wpdb;
    $stmt = "SELECT {$associated_id_column} FROM $table_name"
        . " WHERE {$object_id_column} = %d";
    return $wpdb->get_col($wpdb->prepare($stmt, $object_id));
}
