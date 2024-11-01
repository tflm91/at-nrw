<?php
require_once get_stylesheet_directory() . "/table-names.php";

/* the shortcode for displaying the assistive technologies */
function show_aids() {
    global $wpdb;
    $product_categories_table_name = ASSISTIVE_TECHNOLOGY_CATEGORY_TABLE;
    $connection_table_name = CATEGORY_OF_PRODUCT_TABLE;
    $product_table_name = PRODUCT_TABLE;

    $disability_categories = $wpdb->get_results("SELECT * FROM $product_categories_table_name");
    $output = "<div>\n";
    if ($disability_categories) {
        foreach ($disability_categories as $category) {
            $heading_id = $category->id;
            $output .= "<h2 id='category-" . $heading_id . "'>" . esc_html($category->name) . "</h2>\n";

            $stmt = "SELECT $product_table_name.id AS id, $product_table_name.name AS name FROM $connection_table_name"
                . " INNER JOIN $product_table_name ON $connection_table_name.productId = $product_table_name.id"
                . "WHERE $connection_table_name.categoryId = %d";

            $products = $wpdb->get_results($wpdb->prepare($stmt, $category->id));

            if ($products) {
                $output .= "<ul>\n";
                foreach ($products as $product) {
                    $output .= "<li>" . $product->name . "</li>\n";
                }
                $output .= "</ul>\n";
            } else {
                $output .= "<p>Keine passenden Produkte gefunden.</p>\n";
            }
        }
    } else {
        $output .= "<p>Keine Hilfsmittel vorhanden</p>\n";
    }
    $output .= "</div>\n";
    return $output;
}

add_shortcode("aids", "show_aids");
?>