<?php
/* list suitable categories of aids for the specified impairment */
function list_product_categories($connection_table_name, $impairment_id) {
    global $wpdb;
    $category_table_name = ASSISTIVE_TECHNOLOGY_CATEGORY_TABLE;

    $stmt = "SELECT $category_table_name.id AS id, $category_table_name.name AS name FROM $connection_table_name"
        . " INNER JOIN $category_table_name ON $connection_table_name.assistiveTechnologyCategoryId = $category_table_name.id"
        . " WHERE $connection_table_name.impairmentId = %d";

    $categories = $wpdb->get_results($wpdb->prepare($stmt, $impairment_id));

    $output = "<div>\n";

    if ($categories) {
        $output .= "<ul>\n";
        foreach ($categories as $category) {
            $category_url = 'https://at-nrw.dobus.tu-dortmund.de/hilfsmittel' . '#category-' . $category->id;
            $output .= "<li><a href='" . esc_url($category_url) . "'>" . esc_html($category->name) . "</a></li>\n";
        }
        $output .= "</ul>\n";
    } else {
        $output .= "<p>Keine passenden Hilfsmittel gefunden. </p>\n";
    }
    $output .= "</div>\n";
    return $output;
}