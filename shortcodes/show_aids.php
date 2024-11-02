<?php
require_once get_stylesheet_directory() . "/table-names.php";

/* list the products of the specified category */
function list_products($wpdb, $category_id) {
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
            $details_url = site_url('/hilfsmittel/' . $product->id);
            $output .= '<li><a href="' . $details_url . '">' . esc_html($product->name) . '</a></li>';
        }
        $output .= "</ul>\n";
    } else {
        $output .= "<p>Keine passenden Produkte gefunden.</p>\n";
    }

    return $output;
}

/* list all categories of assistive technologies delt with in the database */
function list_categories($wpdb) {
    $product_categories_table_name = ASSISTIVE_TECHNOLOGY_CATEGORY_TABLE;
    $disability_categories = $wpdb->get_results("SELECT * FROM $product_categories_table_name");
    $output = "<div>\n";
    if ($disability_categories) {
        foreach ($disability_categories as $category) {
            $output .= "<h2 id='category-" . $category->id . "'>" . esc_html($category->name) . "</h2>\n";
            $output .= list_products($wpdb, $category->id);
        }
    } else {
        $output .= "<p>Keine Hilfsmittel vorhanden</p>\n";
    }
    $output .= "</div>\n";
    return $output;
}

/* list universities which offer the specified product */
function list_universities_with_product($wpdb, $product_id) {
    $connection_table_name = AVAILABILITY_TABLE;
    $university_table_name = UNIVERSITY_TABLE;

    $stmt = "SELECT $university_table_name.id AS id, $university_table_name.name AS name FROM $connection_table_name"
        . " INNER JOIN $university_table_name ON $connection_table_name.universityId = $university_table_name.id"
        . " WHERE $connection_table_name.productId = %d";
    $universities = $wpdb->get_results($wpdb->prepare($stmt, $product_id));

    $output = "<div>\n";
    if ($universities) {
        $output .= "<p>Folgende Hochschulen in Nordrhein-Westfalen bieten dieses Hilfsmittel an: </p>\n";
        $output .= "<ul>\n";
        foreach ($universities as $university) {
            $output .= "<li><a href='" . site_url("/university/" . esc_html($university->id)) ."'>" . esc_html($university->name) . "</li>\n";
        }
        $output .= "</ul>\n";
    } else {
        $output .= "<p>Dieses Hilfsmittel wird in NRW leider von keiner Hochschule angeboten. </p>\n";
    }
    $output .= "</div>\n";
    return $output;
}

/* show detailed information about a specified product */
function show_detailed_product_information($wpdb, $product_id) {
    $product_table_name = PRODUCT_TABLE;
    $stmt = "SELECT * FROM $product_table_name WHERE id = %d";
    $product = $wpdb->get_row($wpdb->prepare($stmt, $product_id));

    $output = "<div>\n";
    if ($product) {
       $output .= "<h2>" . esc_html($product->name) . "</h2>\n";
       $output .= '<a href="' . esc_url($product->manufacturerURL) . '">' . esc_html($product->manufacturerAlt) . '</a>';
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