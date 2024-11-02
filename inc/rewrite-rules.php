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
    add_rewrite_rule('^behinderungen/([0-9]+)/?', 'index.php?pagename=behinderungen&disability_id=$matches[1]', 'top');
    add_rewrite_rule('^hilfsmittel/([0-9]+)/?', 'index.php?pagename=hilfsmittel&product_id=$matches[1]', 'top');
    add_rewrite_rule('^hochschulen/([0-9]+)/?', 'index.php?pagename=hochschulen&product_id=$matches[1]', 'top');
}
add_action( 'init', 'custom_rewrite_rules' );
?>