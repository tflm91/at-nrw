
<?php
/**
 * Enqueues stylesheets for parent and child themes
 *
 * @return void
 */
function atnrw_enqueue_theme_styles() {
    // Enqueue parent theme stylesheet
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    // Enqueue child theme stylesheet, making it dependent on the parent style
    wp_enqueue_style('atnrw-child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
}
add_action('wp_enqueue_scripts', 'atnrw_enqueue_theme_styles');
?>
