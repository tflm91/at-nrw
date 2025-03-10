<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

function disability_category_form(): bool|string {
    $category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $is_editing = ($category_id > 0);
    $links = select_all(ADDITIONAL_LINK_TABLE, false);
    $current_category = null;

    if ($is_editing) {
        $current_category = select_one(DISABILITY_CATEGORY_TABLE, $category_id);
        $selected_link_ids = select_associated_ids(
                LINK_FOR_DISABILITY_TABLE,
            'disabilityId',
            'linkId',
            $category_id
        );

        $selected_links = array_filter($links, function ($link) use ($selected_link_ids): bool {
            return in_array($link->id, $selected_link_ids);
        });

        $unselected_links =  array_filter($links, function ($link) use ($selected_link_ids): bool {
            return !in_array($link->id, $selected_link_ids);
        });
    }

ob_start();
?>
<form method="post">
    <label for="category_name">Name der Behinderungskategorie (max. 50 Zeichen)</label>
    <input type="text" id="category_name" name="category_name" maxlength="50" required
    value="<?php echo $is_editing ? esc_attr($current_category->name) : ''; ?>"><br><br>

    <label for="category_description">Beschreibung (max. 1000 Zeichen) :</label>
    <textarea id="category_description" name="category_description" maxlength="1000" required><?php echo $is_editing ? esc_attr($current_category->description) : ''; ?></textarea><br><br>

    <fieldset>
        <legend>Weiterführende Links auswählen:</legend>
        <?php if ($is_editing && !empty($selected_links)): ?>
            <p>Bislang verknüpfte Links: </p>
            <?php foreach ($selected_links as $link): ?>
                <label>
                    <input type="checkbox" name="selected_links[]" value="<?php echo esc_attr($link->id); ?>"
                           checked >
                    <?php echo esc_html($link->altText) ?>
                </label><br>
            <?php endforeach; ?>
            <br>
            <p>Weitere Links: </p>
            <?php if(!empty($unselected_links)): ?>
                <?php foreach ($unselected_links as $link): ?>
                    <label>
                        <input type="checkbox" name="selected_links[]" value="<?php echo esc_attr($link->id); ?>">
                        <?php echo esc_html($link->altText) ?>
                    </label><br>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Alle verfügbaren Links waren bereits ausgewählt: </p>
            <?php endif; ?>
        <?php else: ?>
            <?php foreach ($links as $link): ?>
                <label>
                    <input type="checkbox" name="selected_links[]" value="<?php echo esc_attr($link->id); ?>">
                    <?php echo esc_html($link->altText) ?>
                </label><br>
            <?php endforeach; ?>
        <?php endif; ?>
    </fieldset><br>

    <?php if ($is_editing): ?>
    <input type="hidden" name="category_id" value="<?php echo esc_attr($category_id) ?>">
    <?php endif; ?>

    <button type="submit" name="save_disability_category">Speichern</button>
    <a href="<?php echo site_url('/behinderungskategorien-editieren')?>">
        <button type="button">Abbrechen</button>
    </a>
</form>
<?php
    return ob_get_clean();
}

add_shortcode('disability_category_form', 'disability_category_form');

function save_disability_category(): void {
    if (isset($_POST['save_disability_category'])) {
        global $wpdb;

        $name = sanitize_text_field($_POST['category_name']);
        $description = sanitize_textarea_field($_POST['category_description']);
        $selected_links = $_POST['selected_links'] ?? [];

        if (!empty($_POST['category_id'])) {
            $category_id = intval($_POST['category_id']);
            $wpdb->update(
                    DISABILITY_CATEGORY_TABLE,
                ['name' => $name, 'description' => $description],
                ['id' => $category_id]
            );

            $wpdb->delete(LINK_FOR_DISABILITY_TABLE, ['disabilityId' => $category_id]);
            foreach ($selected_links as $link_id) {
                $wpdb->insert(LINK_FOR_DISABILITY_TABLE, [
                        'disabilityId' => $category_id,
                    'linkId' => $link_id
                ]);
            }
        } else {
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
        }

        wp_redirect(site_url('/behinderungskategorien-editieren'));
        exit;
    }
}

add_action('init', 'save_disability_category');