<?php

require_once get_stylesheet_directory() . "/table-names.php";
require_once get_stylesheet_directory() . "/inc/helpers.php";
require_once get_stylesheet_directory() . "/inc/database.php";

add_shortcode("limitations", "show_limitations");

/* list all available functional limitations */
function show_limitations() {
    $functional_limitations = select_all(FUNCTIONAL_LIMITATION_TABLE);
    $output = "<div>\n";
    if ($functional_limitations) {
        foreach ($functional_limitations as $limitation) {
            $output .= "<h2>" . esc_html($limitation->name) . "</h2>\n";

            $product_categories = select_connected(
                AIDS_WITH_LIMITATION_TABLE,
                'impairmentId',
                PRODUCT_CATEGORY_TABLE,
                'categoryId',
                $limitation->id
            );

            $output .= generate_item_list(
                $product_categories,
                'hilfsmittel',
                error: 'Keine passenden Hilfsmittel gefunden. ',
                id_prefix: 'category'
            );
        }
    } else {
        $output .= "<p>Keine Funktionseinschränkungen gefunden. </p>\n";
    }
    $output .= "</div>\n";
    return $output;
}
?>