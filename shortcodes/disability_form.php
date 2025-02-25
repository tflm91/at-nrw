<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

function disability_form(): bool|string {
    $disability_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $is_editing = ($disability_id > 0);

    $current_disability = null;
    $selected_product_category_ids = [];

    if ($is_editing) {
        $current_disability = select_one(DISABILITY_TABLE, $disability_id);
        $selected_product_category_ids = select_associated_ids(
                AIDS_WITH_DISABILITY_TABLE,
            'impairmentId',
            'categoryId',
            $disability_id
        );
    }

    $disability_categories = select_all(DISABILITY_CATEGORY_TABLE);
    $product_categories = select_all(PRODUCT_CATEGORY_TABLE, false);


    ob_start();
    ?>
    <form method="post">
        <label for="disability_name">Name der Behinderung</label>
        <input type="text" id="disability_name" name="disability_name" required
               value="<?php echo $is_editing ? esc_attr($current_disability->name) : ''; ?>"><br><br>

        <label for="disability_category">Behinderungskategorie</label>
        <select id="disability_category" name="disability_category" required>
            <?php foreach ($disability_categories as $category): ?>
            <?php if ($is_editing && $category->id == $current_disability->categoryId): ?>
                <option value="<?php echo esc_attr($category->id)?>" selected><?php echo esc_html($category->name)?></option>
        <?php else: ?>
                <option value="<?php echo esc_attr($category->id)?>"><?php echo esc_html($category->name)?></option>
        <?php endif; ?>
            <?php endforeach; ?>
        </select><br><br>

        <label for="disability_description">Beschreibung:</label>
        <textarea id="disability_description" name="disability_description" required><?php echo $is_editing ? esc_attr($current_disability->description) : ''; ?></textarea><br><br>

        <fieldset>
            <legend>Passende Produktkategorien auswählen:</legend>
            <?php foreach ($product_categories as $product_category): ?>
                <label>
                    <input type="checkbox" name="selected_categories[]" value="<?php echo esc_attr($product_category->id); ?>"
                        <?php checked(in_array($product_category->id, $selected_product_category_ids));  ?>>
                    <?php echo esc_html($product_category->name); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>

        <?php if ($is_editing): ?>
            <input type="hidden" name="disability_id" value="<?php echo esc_attr($disability_id) ?>">
        <?php endif; ?>

        <button type="submit" name="save_disability">Speichern</button>
        <a href="<?php echo site_url('/behinderungen-editieren')?>">
            <button type="button">Abbrechen</button>
        </a>
    </form>
    <?php
    return ob_get_clean();
}

add_shortcode('disability_form', 'disability_form');

function save_disability(): void {
    if (isset($_POST['save_disability'])) {
        global $wpdb;

        $name = sanitize_text_field($_POST['disability_name']);
        $category_id = intval($_POST['disability_category']);
        $description = sanitize_textarea_field($_POST['disability_description']);
        $selected_categories = $_POST['selected_categories'] ?? [];

        if (!empty($_POST['disability_id'])) {
            $disability_id = intval($_POST['disability_id']);
            $wpdb->update(
                DISABILITY_TABLE,
                ['name' => $name, 'categoryId' => $category_id, 'description' => $description],
                ['id' => $disability_id]
            );

            $wpdb->delete(AIDS_WITH_DISABILITY_TABLE, ['impairmentId' => $disability_id]);
            foreach ($selected_categories as $product_category_id) {
                $wpdb->insert(AIDS_WITH_DISABILITY_TABLE, [
                    'impairmentId' => $disability_id,
                    'categoryId' => $product_category_id
                ]);
            }
        } else {
            $wpdb->insert(DISABILITY_TABLE, [
                'name'=> $name,
                'categoryId' => $category_id,
                'description' => $description,
            ]);

            $disability_id = $wpdb->insert_id;

            foreach ($selected_categories as $product_category_id) {
                $wpdb->insert(AIDS_WITH_DISABILITY_TABLE, [
                    'impairmentId' => $disability_id,
                    'categoryId' => intval($product_category_id)
                ]);
            }
        }

        wp_redirect(site_url('/behinderungen-editieren'));
        exit;
    }
}

add_action('init', 'save_disability');