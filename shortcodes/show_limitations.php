<?php

require_once get_stylesheet_directory() . "/table-names.php";
require_once get_stylesheet_directory() . "/inc/product_category.php";

/* list all available functional limitations */
function show_limitations() {
    global $wpdb;
    $limitations_table = FUNCTIONAL_LIMITATION_TABLE;
    $functional_limitations = $wpdb->get_results("SELECT * FROM $limitations_table");
    $output = "<div>\n";
    if ($functional_limitations) {
        foreach ($functional_limitations as $limitation) {
            $output .= "<h2>" . esc_html($limitation->name) . "</h2>\n";
            $output .= list_product_categories(AIDS_WITH_LIMITATION_TABLE, $limitation->id);
        }
    } else {
        $output .= "<p>Keine Funktionseinschränkungen gefunden. </p>\n";
    }
    $output .= "</div>\n";
    return $output;
}

add_shortcode("limitations", "show_limitations");
?>