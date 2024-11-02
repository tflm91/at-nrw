<?php
require_once get_stylesheet_directory() . "/table-names.php";

/* list all universities in NRW */
function list_universities($wpdb) {
    $university_table_name = UNIVERSITY_TABLE;
    $universities = $wpdb->get_results("SELECT * FROM $university_table_name");

    $output = "<div>\n";
    if ($universities) {
        foreach ($universities as $university) {
            $output .= show_university_information($university);
        }
    } else {
        $output .= "<h2>Keine Universitäten gefunden</h2>\n";
    }
    $output .= "</div>\n";
    return $output;
}

/* the shortcodes for displaying the universities */
function show_universities() {
    global $wpdb;
    return list_universities($wpdb);
}

/* show detailed information about the given university */
function show_university_information($university) {
    $output = "<h2>" . esc_html($university->name) . "</h2>\n";
    $output .= "<p><b>Kontaktinformationen zur Beratungsstelle für behinderte Studierende:</b><br />\n";
    $output .= nl2br($university->contactInformation) . "</p>\n";
    $output .= "<p><b>Link zur Beratungsstelle: </b><a href='" . esc_url($university->contactURL) . "'>". esc_html($university->contactAlt) . "</a></p>\n";
    $output .= "<p><b>Arbeitsräume: </b>" . esc_html($university->workspaces) . "</p>\n";
    return $output;
}

add_shortcode("universities", "show_universities");
?>
