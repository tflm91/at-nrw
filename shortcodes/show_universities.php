<?php
require_once get_stylesheet_directory() . "/table-names.php";
require_once get_stylesheet_directory() . "/inc/helpers.php";
require_once get_stylesheet_directory() . "/inc/database.php";

add_shortcode("universities", "show_universities");

/* the shortcodes for displaying the universities */
function show_universities(): string {
    $university_id = get_query_var('university_id');
    if ($university_id) {
        return show_university_details_page($university_id);
    }
    return list_universities();
}

/* show details page for a specific university */
function show_university_details_page($university_id): string {
    $university = select_one(UNIVERSITY_TABLE, $university_id);

    $output = "<div>\n";
    if ($university) {
        $output .= show_university_information($university);
        $output .= list_available_products($university_id);
    } else {
        $output .= "<p>Die Hochschule konnte nicht gefunden werden. </p>";
    }

    $output .= "<a href='" . site_url("/hochschulen") . "'>Zur Übersicht aller Hochschulen</a>\n";
    $output .= "</div>\n";
    return $output;
}

/* show detailed information about the given university */
function show_university_information($university): string {
    $output = "<h2>" . esc_html($university->name) . "</h2>\n";
    $output .= "<p><b>Kontaktinformationen zur Beratungsstelle für behinderte Studierende:</b><br />\n";
    $output .= nl2br($university->contactInformation) . "</p>\n";
    if ($university->contactURL) {
        $output .= "<p><b>Link zur Beratungsstelle: </b><a href='" . esc_url($university->contactURL) . "'>". esc_html($university->contactAlt) . "</a></p>";
    } else {
        $output .= "<p>Kein Link zur Beratungsstelle vorhanden. </p>";
    }
    $output .= "<p><b>Arbeitsräume: </b>" . esc_html($university->workspaces) . "</p>\n";
    return $output;
}

/* list all available products at the specified university */
function list_available_products($university_id): string {
    $products = select_connected(
        AVAILABILITY_TABLE,
        'universityId',
        PRODUCT_TABLE,
        'productId',
        $university_id
    );

    $before_html = "<p><b>Verfügbare Hilfsmittel:</b></p>\n";
    $error = "Diese Hochschule bietet leider keine Hilfsmittel an. ";

    return generate_item_list(
        $products,
        "hilfsmittel",
        $before_html,
        $error
    );
}

/* list all universities in NRW */
function list_universities(): string {
    $universities = select_all(UNIVERSITY_TABLE);

    $output = "<div>\n";
    if ($universities) {
        foreach ($universities as $university) {
            $output .= show_university_information($university);
            $output .= "<p><a href='" . site_url("/hochschulen/" . esc_attr($university->id)) . "'>Verfügbare Hilfsmittel anzeigen</a></p>\n";
        }
    } else {
        $output .= "<h2>Keine Universitäten gefunden</h2>\n";
    }
    $output .= "</div>\n";
    return $output;
}
?>
