<?php
require_once get_stylesheet_directory() . '/constants.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/inc/helpers.php';

add_shortcode('comprehensive_links', 'list_comprehensive_links');

function list_comprehensive_links(): string {
    $links = select_conditional_links(ADDITIONAL_LINK_TABLE, "comprehensive");
    $output = '';
    if (!empty($links)) {
        $output .= generate_link_list($links);
    }
    return $output;
}