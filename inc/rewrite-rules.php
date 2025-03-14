<?php
/**
 * Add custom query variables
 */
function custom_query_vars($vars) {
    $vars[] = 'disability_id';
    $vars[] = 'product_id';
    $vars[] = 'university_id';
    return $vars;
}
add_filter('query_vars', 'custom_query_vars');


function custom_rewrite_rules() {
    add_rewrite_rule('^beeintraechtigungsformen/([0-9]+)/?', 'index.php?pagename=beeintraechtigungsformen&disability_id=$matches[1]', 'top');
    add_rewrite_rule('^assistive-technologien/([0-9]+)/?', 'index.php?pagename=assistive-technologien&product_id=$matches[1]', 'top');
    add_rewrite_rule('^hochschulen/([0-9]+)/?', 'index.php?pagename=hochschulen&university_id=$matches[1]', 'top');
}
add_action( 'init', 'custom_rewrite_rules' );
?>