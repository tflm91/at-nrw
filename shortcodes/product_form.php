<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/constants.php';

function product_form(): bool|string {
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $is_editing = ($product_id > 0);

    $current_product = null;
    $selected_product_category_ids = [];
    $selected_university_ids = [];

    if ($is_editing) {
        $current_product = select_one(PRODUCT_TABLE, $product_id);
        $selected_product_category_ids = select_associated_ids(
            CATEGORY_OF_PRODUCT_TABLE,
            'productId',
            'categoryId',
            $product_id
        );

        $selected_university_ids = select_associated_ids(
                AVAILABILITY_TABLE,
            'productId',
            'universityId',
            $product_id
        );
    }

    $product_categories = select_all(PRODUCT_CATEGORY_TABLE);
    $universities = select_all(UNIVERSITY_TABLE);

    ob_start();
    ?>
    <form method="post">
        <label for="product_name">Name des Produkts (max. 100 Zeichen) </label>
        <input type="text" id="product_name" name="product_name" maxlength="100" required
               value="<?php echo $is_editing ? esc_attr($current_product->name) : ''; ?>"><br><br>

        <label for="product_description">Beschreibung (max. 3000 Zeichen) :</label><br>
        <textarea id="product_description" name="product_description" maxlength="3000" rows="<?php echo esc_attr(TEXTAREA_ROW_COUNT)?>" required><?php echo $is_editing ? esc_attr($current_product->description) : ''; ?></textarea><br><br>

        <b>Link zu weiterführenden Informationen (leer lassen, wenn kein Link vorhanden): </b><br>
        <label>URL: <input type="url" name="product_info_url" maxlength="2048" value="<?php echo $is_editing ? esc_url($current_product->infoURL) : ''; ?>"></label><br><br>
        <label>Alternativtext (max. 200 Zeichen): <input type="text" name="product_info_alt" maxlength="200" value="<?php echo $is_editing ? esc_html($current_product->infoAlt) : ''; ?>"></label><br><br>

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

        <label>
            <input type="checkbox" name="available_general" id="available_general"
                <?php checked($is_editing ? $current_product->availableGeneral : false) ?>>
            Dieses Produkt ist allgemein verfügbar.
        </label><br><br>

        <fieldset id="university_list">
            <legend>Hochschulen auswählen, die dieses Produkt anbieten:</legend>
            <?php foreach ($universities as $university): ?>
                <label>
                    <input type="checkbox" name="selected_universities[]" value="<?php echo esc_attr($university->id); ?>"
                        <?php checked(in_array($university->id, $selected_university_ids));  ?>>
                    <?php echo esc_html($university->name); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>


        <?php if ($is_editing): ?>
            <input type="hidden" name="product_id" value="<?php echo esc_attr($product_id) ?>">
        <?php endif; ?>

        <button type="submit" name="save_product">Speichern</button>
        <a href="<?php echo site_url('/produkte-editieren')?>">
            <button type="button">Abbrechen</button>
        </a>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('university_list').style.display = document.getElementById('available_general').checked ? 'none' : 'block';
        });
        document.getElementById('available_general').addEventListener('change', function () {
            document.getElementById('university_list').style.display = this.checked ? 'none' : 'block';
        });
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('product_form', 'product_form');

function save_product(): void {
    if (isset($_POST['save_product'])) {
        global $wpdb;

        $name = sanitize_text_field($_POST['product_name']);
        $description = sanitize_textarea_field($_POST['product_description']);
        $info_url = esc_url_raw($_POST['product_info_url']);
        $info_alt = sanitize_text_field($_POST['product_info_alt']);
        $selected_categories = $_POST['selected_categories'] ?? [];
        $available_general = isset($_POST['available_general']);
        $selected_universities = $_POST['selected_universities'] ?? [];

        if (!empty($_POST['product_id'])) {
            $product_id = intval($_POST['product_id']);
            $wpdb->update(
                PRODUCT_TABLE,
                ['name' => $name, 'description' => $description, 'infoURL' => $info_url, 'infoAlt' => $info_alt, 'availableGeneral' => $available_general],
                ['id' => $product_id]
            );

            $wpdb->delete(CATEGORY_OF_PRODUCT_TABLE, ['productId' => $product_id]);
            foreach ($selected_categories as $product_category_id) {
                $wpdb->insert(CATEGORY_OF_PRODUCT_TABLE, [
                    'productId' => $product_id,
                    'categoryId' => $product_category_id
                ]);
            }

            if (!$available_general) {
                $wpdb->delete(AVAILABILITY_TABLE, ['productId' => $product_id]);
                foreach ($selected_universities as $university_id) {
                    $wpdb->insert(AVAILABILITY_TABLE, [
                        'productId' => $product_id,
                        'universityId' => $university_id
                    ]);
                }
            }

        } else {
            $wpdb->insert(PRODUCT_TABLE, [
                'name'=> $name,
                'description' => $description,
                'infoURL' => $info_url,
                'infoAlt' => $info_alt,
                'availableGeneral' => $available_general
            ]);

            $product_id = $wpdb->insert_id;

            foreach ($selected_categories as $product_category_id) {
                $wpdb->insert(CATEGORY_OF_PRODUCT_TABLE, [
                    'productId' => $product_id,
                    'categoryId' => intval($product_category_id)
                ]);
            }

            if (!$available_general) {
                foreach ($selected_universities as $university_id) {
                    $wpdb->insert(AVAILABILITY_TABLE, [
                        'productId' => $product_id,
                        'universityId' => intval($university_id)
                    ]);
                }
            }
        }

        wp_redirect(site_url('/produkte-editieren'));
        exit;
    }
}

add_action('init', 'save_product');