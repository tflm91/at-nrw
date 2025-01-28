<?php
function delete_account_button_shortcode() {
    if (is_user_logged_in()) {
        return '<button id="delete-account" style="background-color: red; color: white; padding: 10px 20px; border: none; cursor: pointer;">Konto löschen</button>';
    }
    return 'Du musst eingeloggt sein, um dein Konto löschen zu können. ';
}

add_shortcode("delete-account-button", "delete_account_button_shortcode");

function delete_account_js() {
    ?>
    <script type="text/javascript">
        document.getElementById('delete-account').addEventListener('click', function (e) {
            e.stopPropagation();
            e.preventDefault();

            if (confirm("Möchtest du dein Benutzerkonto wirklich löschen? Um diese Aktion rückgängig zu machen, ist eine Kontaktaufnahme mit dem Administrator notwendig. ")) {
                window.location.href = "<?php echo esc_url(admin_url('admin-post.php?action=delete_user_account')); ?>";
            }
        });
    </script>
<?php
}

add_action('wp_footer', 'delete_account_js');

function delete_user_account() {
    if ( is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user = get_user_by('id', $user_id);

        if (in_array('administrator', (array) $user->roles)) {
            wp_redirect(home_url('konto-loeschen-fehlgeschlagen'));
            exit;
        }

        wp_logout();

        require_once ( ABSPATH . 'wp-admin/includes/user.php' );
        wp_delete_user($user_id);

        wp_redirect(home_url('/konto-geloescht'));
        exit;
    } else {
        wp_redirect(home_url());
        exit;
    }
}

add_action('admin_post_delete_user_account', 'delete_user_account');
?>