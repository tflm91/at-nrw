<?php
require_once get_stylesheet_directory() . "/table-names.php";
require_once get_stylesheet_directory() . "/inc/helpers.php";
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
    /* get product from database */
    $product = select_one(PRODUCT_TABLE, $product_id);

    /* create output div*/
    $output = "<div>\n";
    if ($product) {
        /* display name and description of product */
        $output .= "<h2>" . esc_html($product->name) . "</h2>\n";
        $output .= "<p>" . esc_html($product->description) . "</p>\n";

        /* display manufacturer link */
        if ($product->manufacturerURL) {
            $output .= '<p><a href="'
                . esc_url($product->manufacturerURL) . '">'
                . esc_html($product->manufacturerAlt) . '</a></p>';
        } else {
            /* error output when no manufacturer link available */
            $output .= '<p>Kein Link zur Herstellerwebsite vorhanden. </p>';
        }

        /* list offering universities */
        $output .= list_universities_with_product($product_id);
    } else {
        /* error when product cannot be found */
        $output .= "<p>Dieses Produkt wurde nicht gefunden. </p>\n";
    }

    /* create back url and finish and return output */
    $output .= "<a href='". site_url('/hilfsmittel') ."'>Zur Übersicht aller Hilfsmittel</a>\n";
    $output .= "</div>\n";
    return $output;
}

/* list universities which offer the specified product */
function list_universities_with_product($product_id): string {
    $universities = select_connected(
        AVAILABILITY_TABLE,
        'productId',
        UNIVERSITY_TABLE,
        'universityId',
        $product_id
    );

    $before_html = "<p>Folgende Hochschulen in Nordrhein-Westfalen bieten dieses Hilfsmittel an: </p>\n";
    $error = "Dieses Hilfsmittel wird in NRW leider von keiner Hochschule angeboten.";

    return generate_item_list(
        $universities,
        "hochschulen",
        $before_html,
        $error
    );
}

/* list all categories of assistive technologies delt with in the database */
function list_categories(): string {
    /* get product categories from database */
    $product_categories = select_all(PRODUCT_CATEGORY_TABLE);

    /* create output */
    $output = "<div>\n";
    if ($product_categories) {
        foreach ($product_categories as $category) {
            /* display only product categories with products */
            $number_of_products = count_items(CATEGORY_OF_PRODUCT_TABLE, $category->id);
            if ($number_of_products > 0) {

                /* display detailed information about each category */
                $output .= "<h2 id='category-" . $category->id . "'>" . esc_html($category->name) . "</h2>\n";
                if ($category->description && $category->description != "") {
                    $output .= "<p>" . esc_html($category->description) . "</p>\n";
                }

                /* list all products of the category */
                $output .= list_products($category->id);
            }
        }
    } else {
        /* error if no product is found */
        $output .= "<p>Keine Hilfsmittel vorhanden</p>\n";
    }
    /* list all products without category */
    $output .= list_products_without_category();

    /* finish and return output */
    $output .= "</div>\n";
    return $output;
}

/* list the products of the specified category */
function list_products($category_id): string {
    $products = select_connected(
        CATEGORY_OF_PRODUCT_TABLE,
        'categoryId',
        PRODUCT_TABLE,
        'productId',
        $category_id
    );

    return generate_item_list(
        $products,
        "hilfsmittel",
        error: "Keine Hilfsmittel zu dieser Kategorie gefunden. "
    );
}

/* list all products without category */
function list_products_without_category(): string {
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
        $before_html
    );
}
?>