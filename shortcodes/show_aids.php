<?php

require_once get_stylesheet_directory() . "/classes/ProductCategory.php";
require_once get_stylesheet_directory() . "/classes/Product.php";
require_once get_stylesheet_directory() . "/table-names.php";
require_once get_stylesheet_directory() . "/inc/database.php";

add_shortcode("aids", "show_aids");

/* the shortcode for displaying the assistive technologies */
function show_aids(): string {
    $product_id = get_query_var('product_id');
    if($product_id) {
        return show_detailed_product_information($product_id);
    }
    return list_categories();
}

/* show detailed information about a specified product */
function show_detailed_product_information($product_id): string {
    $row = select_one(PRODUCT_TABLE, $product_id);

    $output = "<div>\n";
    if ($row) {
        $product = new Product(
            $row->id ?? 0,
            $row->name ?? 'Unbekannt',
            $row->manufacturerURL ?? '',
            $row->manufacturerAlt ?? '',
            $row->description ?? 'Unbekannt');
        $output .= $product->display();
    } else {
        $output .= "<p>Dieses Produkt wurde nicht gefunden. </p>\n";
    }

    $output .= "<a href='". site_url('/hilfsmittel') ."'>Zur Übersicht aller Hilfsmittel</a>\n";
    $output .= "</div>\n";
    return $output;
}

/* list all categories of assistive technologies delt with in the database */
function list_categories(): string {
    $rows = select_all(PRODUCT_CATEGORY_TABLE);
    $output = "<div>\n";
    if ($rows) {
        foreach ($rows as $row) {
            $output .= display_product_category_information($row);
        }
    } else {
        $output .= "<p>Keine Hilfsmittel vorhanden</p>\n";
    }
    $output .= list_products_without_category();
    $output .= "</div>\n";
    return $output;
}

/* display detailed information about a specific product category */
function display_product_category_information($row): string {
    $number_of_products = count_items(CATEGORY_OF_PRODUCT_TABLE, $row->id);
    $output = "";

    if ($number_of_products > 0) {
        $category = new ProductCategory(
            $row->id ?? 0,
            $row->name ?? 'Unbekannt',
            $row->description ?? 'Unbekannt');
        $output .= $category->display();
    }

    return $output;
}

/* list all products without category */
function list_products_without_category(): string {
    $products = find_products_without_category();
    $heading = "<h2>Nicht zugeordnete Produkte. </h2>\n";
    $description = "<p>Folgende Produkte können für Studierende mit Behinderung "
        . "hilfreich sein, gehören aber zu keiner der genannten Kategorien. </p>\n";;
    $before_html = $heading . $description;

    return generate_item_list(
        $products,
        "hilfsmittel",
        $before_html
    );
}

/* find products without product category */
function find_products_without_category() {
    return select_without_category(
        CATEGORY_OF_PRODUCT_TABLE,
        "categoryId",
        PRODUCT_TABLE,
        "productId"
    );
}