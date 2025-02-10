<?php

require_once get_stylesheet_directory() . '/classes/DisabilityCategory.php';
require_once get_stylesheet_directory() . '/classes/Disability.php';

/**
 * Shortcode to display disabilities
 */

add_shortcode("disabilities", "show_disabilities");

/* the shortcode for the disability page */
function show_disabilities(): string {
    $disability_id = get_query_var('disability_id');
    if ($disability_id) {
        return show_detailed_disability_information($disability_id);
    }
    return list_disability_categories();
}

/* show detailed information about a specific disability */
function show_detailed_disability_information ($disability_id): string {
    $row = select_one(DISABILITY_TABLE, $disability_id);
    if ($row) {
        $disability = new Disability($row->id, $row->categoryId, $row->name, $row->description);
        $output = "<div>\n";
        $output .= $disability->display();
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
    $results = select_all(DISABILITY_CATEGORY_TABLE);
    $output = "<div>\n";
    if ($results) {
        foreach ($results as $row) {
            $output .= display_disability_category_information($row);
        }
    } else {
        $output .= "<p>Keine Behinderungskategorien vorhanden.</p>\n";
    }
    $output .= "</div>\n";
    return $output;
}

/* display information about the specified disability_category */
function display_disability_category_information($row): string {
    $number_of_disabilities = count_items(DISABILITY_TABLE, $row->id);
    $output = "";
    if ($number_of_disabilities > 0) {
        $disability_category = new DisabilityCategory($row->id, $row->name, $row->description);
        $output .= $disability_category->display();
    }
    return $output;
}