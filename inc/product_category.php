<?php
require_once get_stylesheet_directory() . '/inc/helpers.php';

/* list suitable categories of aids for the specified impairment */
function list_product_categories($connection_table, $impairment_id) {
    global $wpdb;
    $category_table = PRODUCT_CATEGORY_TABLE;

    $stmt = "SELECT $category_table.id AS id, $category_table.name AS name FROM $connection_table"
        . " INNER JOIN $category_table ON $connection_table.categoryId = $category_table.id"
        . " WHERE $connection_table.impairmentId = %d";

    $categories = $wpdb->get_results($wpdb->prepare($stmt, $impairment_id));

    return generate_item_list($categories,
        "hilfsmittel",
        error: "Keine Hilfsmittel gefunden. ",
        id_prefix: "category"
    );
}