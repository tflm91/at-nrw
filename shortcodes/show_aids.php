<?php
require_once get_stylesheet_directory() . "/table-names.php";

/* the shortcode for displaying the assistive technologies */
function show_aids() {
    global $wpdb;
    $product_categories_table_name = ASSISTIVE_TECHNOLOGY_CATEGORY_TABLE;
    $disability_categories = $wpdb->get_results("SELECT * FROM $product_categories_table_name");
    $output = "<div>\n";
    if ($disability_categories) {
        foreach ($disability_categories as $category) {
            $heading_id = $category->id;
            $output .= "<h2 id='category-" . $heading_id . "'> . $category->name . </h2>\n";
        }
    } else {
        $output .= "<p>Keine Hilfsmittel vorhanden</p>\n";
    }
    $output .= "</div>\n";
    return $output;
}

add_shortcode("aids", "show_aids");
?>