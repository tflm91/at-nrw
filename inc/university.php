<?php

/* show detailed information about the given university */
function show_university_information($university) {
    $output = "<h2>" . esc_html($university->name) . "</h2>\n";
    $output .= "<p><b>Kontaktinformationen zur Beratungsstelle für behinderte Studierende:</b><br />\n";
    $output .= nl2br($university->contactInformation) . "</p>\n";
    $output .= "<p><b>Link zur Beratungsstelle: </b><a href='" . esc_url($university->contactURL) . "'>". esc_html($university->contactAlt) . "</a></p>\n";
    $output .= "<p><b>Arbeitsräume: </b>" . esc_html($university->workspaces) . "</p>\n";
    return $output;
}
?>