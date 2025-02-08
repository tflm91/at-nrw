<?php
require_once get_stylesheet_directory() . "/table-names.php";
require_once get_stylesheet_directory() . "/inc/connection_list.php";

/* list the products of the specified category */
function list_products($wpdb, $category_id) {
    $product_table = PRODUCT_TABLE;
    $connection_table = CATEGORY_OF_PRODUCT_TABLE;

    $stmt = "SELECT $product_table.id AS id, $product_table.name AS name FROM $connection_table"
        . " INNER JOIN $product_table ON $connection_table.productId = $product_table.id"
        . " WHERE $connection_table.categoryId = %d";

    $products = $wpdb->get_results($wpdb->prepare($stmt, $category_id));

    return generate_item_list(
        $products,
        "hilfsmittel",
        null,
        null,
        "Keine Hilfsmittel zu dieser Kategorie gefunden. "
    );
}

/* list all products without category */
function list_products_without_category() {
    $products = select_without_category(
        CATEGORY_OF_PRODUCT_TABLE,
        "categoryId",
        PRODUCT_TABLE,
        "productId"
    );

    $heading = "<h2>Nicht zugeordnete Produkte. </h2>\n";
    $description = "<p>Folgende Produkte können für Studierende mit Behinderung "
        . "hilfreich sein, gehören aber zu keiner der genannten Kategorien. </p>\n";;
    $before_html = $heading . $description;
    return generate_item_list(
        $products,
        "hilfsmittel",
        null,
        $before_html,
        null
    );
}

/* list all categories of assistive technologies delt with in the database */
function list_categories($wpdb) {
    $product_categories_table = PRODUCT_CATEGORY_TABLE;
    $connection_table = CATEGORY_OF_PRODUCT_TABLE;

    $disability_categories = $wpdb->get_results("SELECT * FROM $product_categories_table ORDER BY name");
    $output = "<div>\n";
    if ($disability_categories) {
        foreach ($disability_categories as $category) {
            $number_of_products_stmt = $wpdb->prepare("SELECT COUNT(*) FROM $connection_table WHERE categoryId = %d", $category->id);
            $number_of_products = $wpdb->get_var($number_of_products_stmt);
            if ($number_of_products > 0) {
                $output .= "<h2 id='category-" . $category->id . "'>" . esc_html($category->name) . "</h2>\n";
                if ($category->description && $category->description != "") {
                    $output .= "<p>" . esc_html($category->description) . "</p>\n";
                }
                $output .= list_products($wpdb, $category->id);
            }
        }
    } else {
        $output .= "<p>Keine Hilfsmittel vorhanden</p>\n";
    }
    $output .= list_products_without_category();
    $output .= "</div>\n";
    return $output;
}

/* list universities which offer the specified product */
function list_universities_with_product($wpdb, $product_id) {
    $connection_table = AVAILABILITY_TABLE;
    $university_table = UNIVERSITY_TABLE;

    $stmt = "SELECT $university_table.id AS id, $university_table.name AS name FROM $connection_table"
        . " INNER JOIN $university_table ON $connection_table.universityId = $university_table.id"
        . " WHERE $connection_table.productId = %d";
    $universities = $wpdb->get_results($wpdb->prepare($stmt, $product_id));

    $before_html = "<p>Folgende Hochschulen in Nordrhein-Westfalen bieten dieses Hilfsmittel an: </p>\n";
    $error = "Dieses Hilfsmittel wird in NRW leider von keiner Hochschule angeboten.";

    return generate_item_list(
        $universities,
        "hochschulen",
        null,
        $before_html,
        $error
    );
}

/* show detailed information about a specified product */
function show_detailed_product_information($wpdb, $product_id) {
    $product_table = PRODUCT_TABLE;
    $stmt = "SELECT * FROM $product_table WHERE id = %d";
    $product = $wpdb->get_row($wpdb->prepare($stmt, $product_id));

    $output = "<div>\n";
    if ($product) {
       $output .= "<h2>" . esc_html($product->name) . "</h2>\n";
       $output .= "<p>" . esc_html($product->description) . "</p>\n";
       if ($product->manufacturerURL) {
           $output .= '<p><a href="' . esc_url($product->manufacturerURL) . '">' . esc_html($product->manufacturerAlt) . '</a></p>';
       } else {
           $output .= '<p>Kein Link zur Herstellerwebsite vorhanden. </p>';
       }
       $output .= list_universities_with_product($wpdb, $product_id);
    } else {
        $output .= "<p>Dieses Produkt wurde nicht gefunden. </p>\n";
    }
    $back_url = site_url('/hilfsmittel');
    $output .= "<a href='". $back_url ."'>Zur Übersicht aller Hilfsmittel</a>\n";
    $output .= "</div>\n";
    return $output;
}

/* the shortcode for displaying the assistive technologies */
function show_aids() {
    global $wpdb;
    $product_id = get_query_var('product_id');
    if($product_id) {
        return show_detailed_product_information($wpdb, $product_id);
    }
    return list_categories($wpdb);
}

add_shortcode("aids", "show_aids");
?>