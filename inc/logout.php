<?php
function logout_without_confirmation() {
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        wp_logout();
        wp_redirect("logout");
        exit;
    }
}

add_action('init', 'logout_without_confirmation');
?>