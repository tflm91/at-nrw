<?php

/* show detailed information about the given university */
function show_university_information($university) {
    $output = "<p><b>Kontaktinformationen zur Beratungsstelle für behinderte Studierende:</b><br />\n";
    $output .= esc_html($university->contactInformation) . "</p>\n";
    $output .= "<p><b>Link zur Beratungsstelle: </b><a href='" . esc_url($university->contactURL) . "'>". esc_url($university->contactAlt) . "</a></p>\n";
    $output .= "<p><b>Arbeitsräume: </b>" . esc_html($university->workspaces) . "</p>\n";
    return $output;
}
?>