<?php

require_once get_stylesheet_directory() . "/table-names.php";

/**
 * Shortcode to display disabilities
 */

require_once get_stylesheet_directory() . "/inc/product_category.php";

/* show detailed information about a specific disability */
function show_detailed_disability_information ($disability_id) {
    global $wpdb;
    $disability_table = DISABILITY_TABLE;

    $disability = $wpdb->get_row($wpdb->prepare("SELECT * FROM $disability_table WHERE id = %d", $disability_id));

    if ($disability) {
        $output = "<div>\n";
        $output .= "<h2>" . esc_html($disability->name) . "</h2>\n";
        $output .= "<h3>Beschreibung</h3>\n";
        $output .= "<p>" . esc_html($disability->description) . "</p>\n";
        $output .= "<h3>Passende Hilfsmittel</h3>\n";
        $output .= list_product_categories(AIDS_WITH_DISABILITY_TABLE, $disability->id);
        $back_url = site_url('/behinderungen');
        $output .= "<a href='". $back_url ."'>Zurück zur Übersicht</a>\n";
        $output .= "</div>\n";
        return $output;
    } else {
        return "<p>Keine Behinderung mit dieser ID gefunden.</p>";
    }
}

/* list all disabilities of the corresponding category */
function list_disabilities($category_id) {
    global $wpdb;
    $disability_table = DISABILITY_TABLE;

    $disabilities = $wpdb->get_results($wpdb->prepare(
        "SELECT id, name FROM $disability_table WHERE categoryId = %d",
        $category_id)
    );

    $output = "";
    if ($disabilities) {
        $output .= "<ul>\n";
        foreach ($disabilities as $disability) {
            $detail_url = site_url('/behinderungen/' . esc_attr($disability->id));
            $output .= "<li><a href='" . esc_url($detail_url) . "'>" . esc_html($disability->name) . "</a></li>\n";
        }
        $output .= "</ul>\n";
    }

    return $output;
}

/* list all disability categories */
function list_disability_categories() {
    global $wpdb;
    $disability_category_table = DISABILITY_CATEGORY_TABLE;
    $results = $wpdb->get_results("SELECT * FROM $disability_category_table");

    $output = "<div>\n";
    if ($results) {
        foreach ($results as $row) {
            $output .= "<h2>" . esc_html($row->name) . "</h2>\n";
            if ($row->description && $row->description != "") {
                $output .= "<p>" . esc_html($row->description) . "</p>\n";
            }
            $output .= list_disabilities($row->id);
        }
    } else {
        $output .= "<p>Keine Behinderungskategorien vorhanden.</p>\n";
    }

    $output .= "</div>\n";
    return $output;
}

/* the shortcode for the disability page */
function show_disabilities() {
    // Get the disability ID from the URL
    $disability_id = get_query_var('disability_id');

    // If a specific disability ID is present, show detailed information about that disability
    if ($disability_id) {
        return show_detailed_disability_information($disability_id);
    }

    return list_disability_categories();
}

add_shortcode("disabilities", "show_disabilities");

?>