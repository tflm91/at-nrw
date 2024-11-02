
<?php
require_once get_stylesheet_directory() . '/table-names.php';

/**
 * Lists the products of the specified category.
 *
 * @param object $wpdb WordPress database object.
 * @param int $category_id ID of the category to fetch products for.
 * @return string HTML output of the product list.
 */
function atnrw_list_products($wpdb, $category_id) {
    $product_table_name = PRODUCT_TABLE;
    $connection_table_name = CATEGORY_OF_PRODUCT_TABLE;

    $stmt = "SELECT $product_table_name.id AS id, $product_table_name.name AS name FROM $connection_table_name"
        . " INNER JOIN $product_table_name ON $connection_table_name.productId = $product_table_name.id"
        . " WHERE $connection_table_name.categoryId = %d";

    $products = $wpdb->get_results($wpdb->prepare($stmt, $category_id));

    $output = "";
    if ($products) {
        $output .= "<ul>\n";
        foreach ($products as $product) {
            $details_url = esc_url(site_url('/hilfsmittel/' . $product->id));
            $output .= '<li><a href="' . $details_url . '">' . esc_html($product->name) . '</a></li>';
        }
        $output .= "</ul>\n";
    } else {
        $output .= "<p>Keine passenden Produkte gefunden.</p>\n";
    }

    return $output;
}

/**
 * Lists all categories of assistive technologies in the database.
 *
 * @param object $wpdb WordPress database object.
 * @return string HTML output of the categories list.
 */
function atnrw_list_categories($wpdb) {
    $product_categories_table_name = ASSISTIVE_TECHNOLOGY_CATEGORY_TABLE;
    $disability_categories = $wpdb->get_results("SELECT * FROM $product_categories_table_name");

    $output = "<div>\n";
    if ($disability_categories) {
        foreach ($disability_categories as $category) {
            $output .= "<h2 id='category-" . esc_attr($category->id) . "'>" . esc_html($category->name) . "</h2>\n";
        }
    } else {
        $output .= "<p>Keine Kategorien gefunden.</p>\n";
    }
    $output .= "</div>\n";

    return $output;
}
?>
