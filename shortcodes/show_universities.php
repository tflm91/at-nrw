<?php
require_once get_stylesheet_directory() . "/table-names.php";

/* list all universities in NRW */
function list_universities($wpdb) {
    $university_table_name = UNIVERSITY_TABLE;
    $universities = $wpdb->get_results("SELECT * FROM $university_table_name");

    $output = "<div>\n";
    if ($universities) {
        foreach ($universities as $university) {
            $output .= "<h2>" . $university->name . "</h2>\n";
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

add_shortcode("universities", "show_universities");
?>
