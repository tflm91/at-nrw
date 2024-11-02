
<?php
/**
 * Registers custom query variables for specific page routing.
 *
 * @param array $vars Existing query variables.
 * @return array Modified array of query variables.
 */
function atnrw_custom_query_vars($vars) {
    $vars[] = 'disability_id';
    $vars[] = 'product_id';
    $vars[] = 'university_id';
    return $vars;
}
add_filter('query_vars', 'atnrw_custom_query_vars');

/**
 * Adds custom rewrite rules to handle specific URL structures.
 *
 * This function maps custom URLs to corresponding templates and query variables.
 *
 * @return void
 */
function atnrw_custom_rewrite_rules() {
    add_rewrite_rule('^behinderungen/([0-9]+)/?', 'index.php?pagename=behinderungen&disability_id=$matches[1]', 'top');
    add_rewrite_rule('^hilfsmittel/([0-9]+)/?', 'index.php?pagename=hilfsmittel&product_id=$matches[1]', 'top');
    add_rewrite_rule('^hochschulen/([0-9]+)/?', 'index.php?pagename=hochschulen&university_id=$matches[1]', 'top');
}
add_action('init', 'atnrw_custom_rewrite_rules');
?>
