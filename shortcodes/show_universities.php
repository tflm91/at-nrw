<?php
require_once get_stylesheet_directory() . "/table-names.php";

/* list all universities in NRW */
function list_universities($wpdb) {
    $university_table = UNIVERSITY_TABLE;
    $universities = $wpdb->get_results("SELECT * FROM $university_table");

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

/* show detailed information about the given university */
function show_university_information($university) {
    $output = "<h2>" . esc_html($university->name) . "</h2>\n";
    $output .= "<p><b>Kontaktinformationen zur Beratungsstelle für behinderte Studierende:</b><br />\n";
    $output .= nl2br($university->contactInformation) . "</p>\n";
    if ($university->contactURL) {
        $output .= "<p><b>Link zur Beratungsstelle: </b><a href='" . esc_url($university->contactURL) . "'>". esc_html($university->contactAlt) . "</a></p>\n";
    } else {
        $output .= "<p>Kein Link zur Beratungsstelle vorhanden. </p>\n";
    }
    $output .= "<p><b>Arbeitsräume: </b>" . esc_html($university->workspaces) . "</p>\n";
    return $output;
}

function list_available_products($wpdb, $university_id) {
    $connection_table = AVAILABILITY_TABLE;
    $product_table = PRODUCT_TABLE;
    $stmt = "SELECT $product_table.id AS id, $product_table.name AS name FROM $connection_table"
        . " INNER JOIN $product_table ON $connection_table.productId=$product_table.id"
        . " WHERE $connection_table.universityId=%d";
    $products = $wpdb->get_results($wpdb->prepare($stmt, $university_id));

    $output = "<div>\n";
    if ($products) {
        $output .= "<p><b>Verfügbare Hilfsmittel:</b></p>\n";
        $output .= "<ul>\n";
        foreach ($products as $product) {
            $output .= "<li><a href='". site_url("/hilfsmittel/" . esc_attr($product->id)) . "'>" . esc_html($product->name) . "</a></li>";
        }
        $output .= "</ul>\n";
    } else {
        $output .= "<p>Diese Hochschule bietet leider keine Hilfsmittel an. </p>\n";
    }
    $output .= "</div>\n";
    return $output;
}

function show_university_details_page($wpdb, $university_id) {
    $university_table = UNIVERSITY_TABLE;

    $stmt = "SELECT * FROM $university_table WHERE id = %d";
    $university = $wpdb->get_row($wpdb->prepare($stmt, $university_id));

    $output = "<div>\n";
    if ($university) {
        $output .= show_university_information($university);
        $output .= list_available_products($wpdb, $university_id);
    } else {
        $output .= "<p>Die Hochschule konnte nicht gefunden werden. </p>";
    }

    $output .= "<a href='" . site_url("/hochschulen") . "'>Zur Übersicht aller Hochschulen</a>\n";
    $output .= "</div>\n";
    return $output;
}

/* the shortcodes for displaying the universities */
function show_universities() {
    global $wpdb;
    $university_id = get_query_var('university_id');
    if ($university_id) {
        return show_university_details_page($wpdb, $university_id);
    }
    return list_universities($wpdb);
}


add_shortcode("universities", "show_universities");
?>
