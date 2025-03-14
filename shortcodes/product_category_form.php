<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/constants.php';

function product_category_form(): bool|string {
    $category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $is_editing = ($category_id > 0);

    $disabilities = select_all(DISABILITY_TABLE);
    $limitations = select_all(FUNCTIONAL_LIMITATION_TABLE);
    $products = select_all(PRODUCT_TABLE);
    $links = select_all(ADDITIONAL_LINK_TABLE, false);

    $current_category = null;
    $selected_disability_ids = [];
    $selected_limitation_ids = [];
    $selected_product_ids = [];
    $selected_links = [];
    $unselected_links = [];

    if ($is_editing) {
        $current_category = select_one(PRODUCT_CATEGORY_TABLE, ['id' => $category_id]);

        $selected_disability_ids = select_associated_ids(
            AIDS_WITH_DISABILITY_TABLE,
            'categoryId',
            'impairmentId',
            $category_id
        );

        $selected_limitation_ids = select_associated_ids(
            AIDS_WITH_LIMITATION_TABLE,
            'categoryId',
            'impairmentId',
            $category_id
        );

        $selected_product_ids = select_associated_ids(
            CATEGORY_OF_PRODUCT_TABLE,
            'categoryId',
            'productId',
            $category_id
        );

        $selected_link_ids = select_associated_ids(
            LINK_FOR_AID_TABLE,
            'aidId',
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
        <label for="category_name">Name der assistiven Technologie (max. 150 Zeichen)</label>
        <input type="text" id="category_name" name="category_name" maxlength="150" required
               value="<?php echo $is_editing ? esc_attr($current_category->name) : ''; ?>"><br><br>

        <label for="category_description">Beschreibung (max. 2000 Zeichen):</label>
        <textarea id="category_description" name="category_description" maxlength="2000" rows="<?php echo esc_attr(TEXTAREA_ROW_COUNT)?>" required><?php echo $is_editing ? esc_attr($current_category->description) : ''; ?></textarea><br><br>

        <fieldset>
            <legend>Unterstützte Beeinträchtigungsformen auswählen:</legend>
            <?php foreach ($disabilities as $disability): ?>
                <label>
                    <input type="checkbox" name="selected_disabilities[]" value="<?php echo esc_attr($disability->id); ?>"
                        <?php checked(in_array($disability->id, $selected_disability_ids));  ?>>
                    <?php echo esc_html($disability->name); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>


        <fieldset>
            <legend>Unterstützte Funktionseinschränkungen auswählen:</legend>
            <?php foreach ($limitations as $limitation): ?>
                <label>
                    <input type="checkbox" name="selected_limitations[]" value="<?php echo esc_attr($limitation->id); ?>"
                        <?php checked(in_array($limitation->id, $selected_limitation_ids));  ?>>
                    <?php echo esc_html($limitation->name); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>


        <fieldset>
            <legend>Passende Produkte auswählen:</legend>
            <?php foreach ($products as $product): ?>
                <label>
                    <input type="checkbox" name="selected_products[]" value="<?php echo esc_attr($product->id); ?>"
                        <?php checked(in_array($product->id, $selected_product_ids));  ?>>
                    <?php echo esc_html($product->name); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>

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

        <button type="submit" name="save_product_category">Speichern</button>
        <a href="<?php echo site_url('/assistive-technologien-editieren')?>">
            <button type="button">Abbrechen</button>
        </a>
    </form>
    <?php
    return ob_get_clean();
}

add_shortcode('product_category_form', 'product_category_form');

function save_product_category(): void {
    if (isset($_POST['save_product_category'])) {
        global $wpdb;

        $name = sanitize_text_field($_POST['category_name']);
        $description = sanitize_textarea_field($_POST['category_description']);
        $selected_disabilities =  $_POST['selected_disabilities'];
        $selected_limitations = $_POST['selected_limitations'];
        $selected_products = $_POST['selected_products'];
        $selected_links = $_POST['selected_links'];

        if (!empty($_POST['category_id'])) {
            $category_id = intval($_POST['category_id']);
            $wpdb->update(
                PRODUCT_CATEGORY_TABLE,
                ['name' => $name, 'description' => $description],
                ['id' => $category_id]
            );

            $wpdb->delete(AIDS_WITH_DISABILITY_TABLE, ['categoryId' => $category_id]);
            foreach ($selected_disabilities as $disability) {
                $wpdb->insert(AIDS_WITH_DISABILITY_TABLE, [
                    'categoryId' => $category_id,
                    'impairmentId' => $disability
                ]);
            }

            $wpdb->delete(AIDS_WITH_LIMITATION_TABLE, ['categoryId' => $category_id]);
            foreach ($selected_limitations as $limitation) {
                $wpdb->insert(AIDS_WITH_LIMITATION_TABLE, [
                   'categoryId' => $category_id,
                   'impairmentId' => $limitation
                ]);
            }

            $wpdb->delete(CATEGORY_OF_PRODUCT_TABLE, ['categoryId' => $category_id]);
            foreach ($selected_products as $product) {
                $wpdb->insert(CATEGORY_OF_PRODUCT_TABLE, [
                   'categoryId' => $category_id,
                   'productId' => $product
                ]);
            }

            $wpdb->delete(LINK_FOR_AID_TABLE, ['aidId' => $category_id]);
            foreach ($selected_links as $link) {
                $wpdb->insert(LINK_FOR_AID_TABLE, [
                    'aidId' => $category_id,
                    'linkId' => $link
                ]);
            }
        } else {
            $wpdb->insert(PRODUCT_CATEGORY_TABLE, [
                'name' => $name,
                'description' => $description,
                ]);

            $category_id = $wpdb->insert_id;

            foreach ($selected_disabilities as $disability) {
                $wpdb->insert(AIDS_WITH_DISABILITY_TABLE, [
                    'categoryId' => $category_id,
                    'impairmentId' => $disability
                ]);
            }

            foreach ($selected_limitations as $limitation) {
                $wpdb->insert(AIDS_WITH_LIMITATION_TABLE, [
                    'categoryId' => $category_id,
                    'impairmentId' => $limitation
                ]);
            }

            foreach ($selected_products as $product) {
                $wpdb->insert(CATEGORY_OF_PRODUCT_TABLE, [
                    'categoryId' => $category_id,
                    'productId' => $product
                ]);
            }

            foreach ($selected_links as $link) {
                $wpdb->insert(LINK_FOR_AID_TABLE, [
                    'aidId' => $category_id,
                    'linkId' => $link
                ]);
            }
        }

        wp_redirect(site_url('/assistive-technologien-editieren'));
        exit;
    }
}

add_action('init', 'save_product_category');