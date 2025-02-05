<?php
require_once get_stylesheet_directory() . '/inc/connection_list.php';

/* list suitable categories of aids for the specified impairment */
function list_product_categories($connection_table, $impairment_id) {
    global $wpdb;
    $category_table = PRODUCT_CATEGORY_TABLE;

    $stmt = "SELECT $category_table.id AS id, $category_table.name AS name FROM $connection_table"
        . " INNER JOIN $category_table ON $connection_table.categoryId = $category_table.id"
        . " WHERE $connection_table.impairmentId = %d";

    $categories = $wpdb->get_results($wpdb->prepare($stmt, $impairment_id));

    $output = "<div>\n";

    if ($categories) {
        $output .= "<ul>\n";
        foreach ($categories as $category) {
            $output .= "<li>" . generate_item_link($category, 'hilfsmittel', 'category') . "</li>\n";
        }
        $output .= "</ul>\n";
    } else {
        $output .= "<p>Keine passenden Hilfsmittel gefunden. </p>\n";
    }
    $output .= "</div>\n";
    return $output;
}