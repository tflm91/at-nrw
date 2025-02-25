<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

function limitation_form(): bool|string {
    $limitation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $is_editing = ($limitation_id > 0);

    $current_limitation = null;
    $selected_product_category_ids = [];

    if ($is_editing) {
        $current_limitation = select_one(FUNCTIONAL_LIMITATION_TABLE, $limitation_id);
        $selected_product_category_ids = select_associated_ids(
            AIDS_WITH_LIMITATION_TABLE,
            'impairmentId',
            'categoryId',
            $limitation_id
        );
    }

    $product_categories = select_all(PRODUCT_CATEGORY_TABLE);

    ob_start();
    ?>
    <form method="post">
        <label for="limitation_name">Name der Funktionseinschränkung</label>
        <input type="text" id="limitation_name" name="limitation_name" required
               value="<?php echo $is_editing ? esc_attr($current_limitation->name) : ''; ?>"><br><br>

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
            <input type="hidden" name="limitation_id" value="<?php echo esc_attr($limitation_id) ?>">
        <?php endif; ?>

        <button type="submit" name="save_limitation">Speichern</button>
        <a href="<?php echo site_url('/funktionseinschraenkungen-editieren')?>">
            <button type="button">Abbrechen</button>
        </a>
    </form>
    <?php
    return ob_get_clean();
}

add_shortcode('limitation_form', 'limitation_form');

function save_limitation(): void {
    if (isset($_POST['save_limitation'])) {
        global $wpdb;

        $name = sanitize_text_field($_POST['limitation_name']);
        $selected_categories = $_POST['selected_categories'] ?? [];

        if (!empty($_POST['limitation_id'])) {
            $limitation_id = intval($_POST['limitation_id']);
            $wpdb->update(
                FUNCTIONAL_LIMITATION_TABLE,
                ['name' => $name],
                ['id' => $limitation_id]
            );

            $wpdb->delete(AIDS_WITH_LIMITATION_TABLE, ['impairmentId' => $limitation_id]);
            foreach ($selected_categories as $product_category_id) {
                $wpdb->insert(AIDS_WITH_LIMITATION_TABLE, [
                    'impairmentId' => $limitation_id,
                    'categoryId' => $product_category_id
                ]);
            }
        } else {
            $wpdb->insert(FUNCTIONAL_LIMITATION_TABLE, ['name'=> $name]);

            $limitation_id = $wpdb->insert_id;

            foreach ($selected_categories as $product_category_id) {
                $wpdb->insert(AIDS_WITH_LIMITATION_TABLE, [
                    'impairmentId' => $limitation_id,
                    'categoryId' => intval($product_category_id)
                ]);
            }
        }

        wp_redirect(site_url('/funktionseinschraenkungen-editieren'));
        exit;
    }
}

add_action('init', 'save_limitation');