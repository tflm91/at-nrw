<?php

require_once get_stylesheet_directory() . '/classes/FunctionalLimitation.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/constants.php';

add_shortcode("limitations", "show_limitations");

function show_limitations(): string {
    $rows = select_all(FUNCTIONAL_LIMITATION_TABLE);
    $output = "<div>\n";
    if ($rows) {
        foreach ($rows as $row) {
            $limitation = new FunctionalLimitation(
                $row->id ?? 0,
                $row->name ?? 'Unbekannt'
            );
            $output .= $limitation->display();
        }
    } else {
        $output .= "<p>Keine Funktionseinschränkungen gefunden. </p>\n";
    }
    $output .= "</div>\n";
    return $output;
}
?>