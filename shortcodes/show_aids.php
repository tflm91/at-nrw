<?php
require_once get_stylesheet_directory() . "/table-names.php";

/* list the products of the specified category */
function list_products($wpdb, $category_id) {
    $product_table = PRODUCT_TABLE;
    $connection_table = CATEGORY_OF_PRODUCT_TABLE;

    $stmt_of_category = "SELECT $product_table.id AS id, $product_table.name AS name FROM $connection_table"
        . " INNER JOIN $product_table ON $connection_table.productId = $product_table.id"
        . " WHERE $connection_table.categoryId = %d";

    if ($category_id == 23) {
        $stmt_without_category = "SELECT $product_table.id AS id, $product_table.name AS name FROM $product_table"
            . " LEFT JOIN $connection_table ON $product_table.id = $connection_table.productId"
            . " WHERE $connection_table.categoryId IS NULL";

        $stmt = "$stmt_of_category UNION $stmt_without_category";
    } else {
        $stmt = $stmt_of_category;
    }

    $products = $wpdb->get_results($wpdb->prepare($stmt, $category_id));

    $output = "";
    if ($products) {
        $output .= "<ul>\n";
        foreach ($products as $product) {
            $details_url = site_url('/hilfsmittel/' . esc_attr($product->id));
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
    $product_categories_table = PRODUCT_CATEGORY_TABLE;
    $disability_categories = $wpdb->get_results("SELECT * FROM $product_categories_table");
    $output = "<div>\n";
    if ($disability_categories) {
        foreach ($disability_categories as $category) {
            $output .= "<h2 id='category-" . $category->id . "'>" . esc_html($category->name) . "</h2>\n";
            if ($category->description && $category->description != "") {
                $output .= "<p>" . esc_html($category->description) . "</p>\n";
            }
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
    $connection_table = AVAILABILITY_TABLE;
    $university_table = UNIVERSITY_TABLE;

    $stmt = "SELECT $university_table.id AS id, $university_table.name AS name FROM $connection_table"
        . " INNER JOIN $university_table ON $connection_table.universityId = $university_table.id"
        . " WHERE $connection_table.productId = %d";
    $universities = $wpdb->get_results($wpdb->prepare($stmt, $product_id));

    $output = "<div>\n";
    if ($universities) {
        $output .= "<p>Folgende Hochschulen in Nordrhein-Westfalen bieten dieses Hilfsmittel an: </p>\n";
        $output .= "<ul>\n";
        foreach ($universities as $university) {
            $output .= "<li><a href='" . site_url("/hochschulen/" . esc_attr($university->id)) ."'>" . esc_html($university->name) . "</li>\n";
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

    $output .= "<p><b>Link zur Hilfsmittelseite: </b></p>";
    $output .= "<a href='" . site_url("/hilfsmittel") . "'>Zur Übersicht aller Hilfsmittel</a>\n";
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