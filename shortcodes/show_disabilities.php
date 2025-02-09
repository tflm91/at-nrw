<?php

require_once get_stylesheet_directory() . "/table-names.php";
require_once get_stylesheet_directory() . "/inc/helpers.php";
require_once get_stylesheet_directory() . "/inc/database.php";

/**
 * Shortcode to display disabilities
 */

add_shortcode("disabilities", "show_disabilities");

/* the shortcode for the disability page */
function show_disabilities(): string {
    // Get the disability ID from the URL
    $disability_id = get_query_var('disability_id');

    // If a specific disability ID is present, show detailed information about that disability
    if ($disability_id) {
        return show_detailed_disability_information($disability_id);
    }

    return list_disability_categories();
}

/* show detailed information about a specific disability */
function show_detailed_disability_information ($disability_id): string {

    /* search disability in database */
    $disability = select_one(DISABILITY_TABLE, $disability_id);

    if ($disability) {

        /* display disability information */
        $output = "<div>\n";
        $output .= "<h2>" . esc_html($disability->name) . "</h2>\n";
        $output .= "<h3>Beschreibung</h3>\n";
        $output .= "<p>" . esc_html($disability->description) . "</p>\n";
        $output .= "<h3>Passende Hilfsmittel</h3>\n";

        /* get suitable aids from database */
        $product_categories =  select_connected(
            AIDS_WITH_DISABILITY_TABLE,
            "impairmentId",
            PRODUCT_CATEGORY_TABLE,
            "categoryId",
            $disability_id
        );

        /* add link list to aids */
        $output .= generate_item_list(
            $product_categories,
            'hilfsmittel',
            error: 'Keine passenden Hilfsmittel gefunden. ',
            id_prefix: 'category'
        );

        /* create back url */
        $back_url = site_url('/behinderungen');
        $output .= "<a href='". $back_url ."'>Zurück zur Übersicht</a>\n";
        $output .= "</div>\n";
        return $output;
    } else {
        return "<p>Keine Behinderung mit dieser ID gefunden.</p>";
    }
}

/* list all disability categories */
function list_disability_categories(): string {
    /* retrieve all disability categories */
    $results = select_all(DISABILITY_CATEGORY_TABLE);

    /* create output string*/
    $output = "<div>\n";
    if ($results) {
        foreach ($results as $row) {

            /* display categories only if they have disabilities */
            $number_of_disabilities = count_items(DISABILITY_TABLE, $row->id);
            if ($number_of_disabilities > 0) {

                /* display name and description if available */
                $output .= "<h2>" . esc_html($row->name) . "</h2>\n";
                if ($row->description && $row->description != "") {
                    $output .= "<p>" . esc_html($row->description) . "</p>\n";
                }

                /* list all disabilities of the category*/
                $output .= list_disabilities($row->id);
            }
        }
    } else {
        $output .= "<p>Keine Behinderungskategorien vorhanden.</p>\n";
    }

    /* finish and return output */
    $output .= "</div>\n";
    return $output;
}

/* list all disabilities of the corresponding category */
function list_disabilities($category_id): string {
    $disabilities = select_of_category(DISABILITY_TABLE, $category_id);
    return generate_item_list(
        $disabilities,
        "behinderungen",
        error: "Keine spezifische Behinderung gefunden. "
    );
}
?>