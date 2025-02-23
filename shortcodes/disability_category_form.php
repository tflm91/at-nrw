<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

function disability_category_form(): bool|string {
$links = select_all(ADDITIONAL_LINK_TABLE, false);

ob_start();
?>
<form method="post">
    <label for="category_name">Name der Behinderungskategorie</label>
    <input type="text" id="category_name" name="category_name" required><br><br>

    <label for="category_description">Beschreibung:</label>
    <textarea id="category_description" name="category_description" required></textarea><br><br>

    <fieldset>
        <legend>Weiterführende Links auswählen:</legend>
        <?php foreach ($links as $link): ?>
            <label>
                <input type="checkbox" name="selected_links[]" value="<?php echo esc_attr($link->id); ?>">
                <?php echo esc_html($link->altText); ?> (<?php echo esc_url($link->URL); ?>)
            </label><br>
        <?php endforeach; ?>
    </fieldset><br>


    <button type="submit" name="save_category">Speichern</button>
    <a href="<?php echo site_url('/behinderungskategorien-editieren')?>">
        <button type="button">Abbrechen</button>
    </a>
</form>
<?php
    return ob_get_clean();
}

add_shortcode('disability_category_form', 'disability_category_form');

function save_disability_category(): void {
    if (isset($_POST['save_category'])) {
        global $wpdb;

        $name = sanitize_text_field($_POST['category_name']);
        $description = sanitize_textarea_field($_POST['category_description']);
        $selected_links = $_POST['selected_links'] ?? [];

        $wpdb->insert(DISABILITY_CATEGORY_TABLE, [
                'name'=> $name,
            'description' => $description,
        ]);

        $category_id = $wpdb->insert_id;

        foreach ($selected_links as $link_id) {
            $wpdb->insert(LINK_FOR_DISABILITY_TABLE, [
                    'disabilityId' => $category_id,
                'linkId' => intval($link_id)
            ]);
        }

        wp_redirect(site_url('/behinderungskategorien-editieren'));
        exit;
    }
}

add_action('init', 'save_disability_category');