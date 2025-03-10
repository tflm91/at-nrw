<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/constants.php';

function additional_link_form(): bool|string {
    $link_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $is_editing = ($link_id > 0);

    $current_link = null;
    $selected_product_category_ids = [];
    $selected_disability_category_ids = [];

    if ($is_editing) {
        $current_link = select_one(ADDITIONAL_LINK_TABLE, $link_id);
        $selected_product_category_ids = select_associated_ids(
            LINK_FOR_AID_TABLE,
            'linkId',
            'aidId',
            $link_id
        );

        $selected_disability_category_ids = select_associated_ids(
            LINK_FOR_DISABILITY_TABLE,
            'linkId',
            'disabilityId',
            $link_id
        );
    }

    $product_categories = select_all(PRODUCT_CATEGORY_TABLE);
    $disability_categories = select_all(DISABILITY_CATEGORY_TABLE);


    ob_start();
    ?>
    <form method="post">
        <label>URL: <input type="url" name="link_url" value="<?php echo $is_editing ? esc_url($current_link->URL) : ''; ?>"></label><br><br>
        <label>Alternativtext (max. 255 Zeichen): <input type="text" name="link_alt" maxlength="255" value="<?php echo $is_editing ? esc_html($current_link->altText) : ''; ?>"></label><br><br>

        <label>
            <input type="checkbox" name="comprehensive_link" id="comprehensive_link"
                <?php checked($is_editing ? $current_link->comprehensive : false) ?>>
            Dies ist ein übergreifender Link.
        </label><br><br>

        <fieldset>
            <legend>Passende Behinderungskategorien auswählen:</legend>
            <?php foreach ($disability_categories as $disability_category): ?>
                <label>
                    <input type="checkbox" name="selected_disability_categories[]" value="<?php echo esc_attr($disability_category->id); ?>"
                        <?php checked(in_array($disability_category->id, $selected_disability_category_ids));  ?>>
                    <?php echo esc_html($disability_category->name); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>

        <fieldset>
            <legend>Passende Produktkategorien auswählen:</legend>
            <?php foreach ($product_categories as $product_category): ?>
                <label>
                    <input type="checkbox" name="selected_product_categories[]" value="<?php echo esc_attr($product_category->id); ?>"
                        <?php checked(in_array($product_category->id, $selected_product_category_ids));  ?>>
                    <?php echo esc_html($product_category->name); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>

        <?php if ($is_editing): ?>
            <input type="hidden" name="link_id" value="<?php echo esc_attr($link_id) ?>">
        <?php endif; ?>

        <button type="submit" name="save_link">Speichern</button>
        <a href="<?php echo site_url('/weiterfuehrende-links-editieren')?>">
            <button type="button">Abbrechen</button>
        </a>
    </form>
    <?php
    return ob_get_clean();
}

add_shortcode('additional_link_form', 'additional_link_form');

function save_link(): void {
    if (isset($_POST['save_link'])) {
        global $wpdb;

        $link_url = $_POST['link_url'];
        $link_alt = $_POST['link_alt'];
        $comprehensive = isset($_POST['comprehensive_link']);
        $selected_disability_categories = $_POST['selected_disability_categories'];
        $selected_product_categories = $_POST['selected_product_categories'];

        if (!empty($_POST['link_id'])) {
            $link_id = intval($_POST['link_id']);
            $wpdb->update(
                ADDITIONAL_LINK_TABLE,
                ['URL' => $link_url, 'altText' => $link_alt, 'comprehensive' => $comprehensive],
                ['id' => $link_id]
            );

            $wpdb->delete(LINK_FOR_DISABILITY_TABLE, ['linkId' => $link_id]);
            foreach ($selected_disability_categories as $disability_category_id) {
                $wpdb->insert(LINK_FOR_DISABILITY_TABLE, [
                    'linkId' => $link_id,
                    'disabilityId' => $disability_category_id
                ]);
            }

            $wpdb->delete(LINK_FOR_AID_TABLE, ['linkId' => $link_id]);
            foreach ($selected_product_categories as $product_category_id) {
                $wpdb->insert(LINK_FOR_AID_TABLE, [
                    'linkId' => $link_id,
                    'aidId' => $product_category_id
                ]);
            }
        } else {
            $wpdb->insert(ADDITIONAL_LINK_TABLE, [
                'URL' => $link_url,
                'altText' => $link_alt,
                'comprehensive' => $comprehensive
            ]);

            $link_id = $wpdb->insert_id;

            foreach ($selected_disability_categories as $disability_category_id) {
                $wpdb->insert(LINK_FOR_DISABILITY_TABLE, [
                    'linkId' => $link_id,
                    'disabilityId' => $disability_category_id
                ]);
            }

            foreach ($selected_product_categories as $product_category_id) {
                $wpdb->insert(LINK_FOR_AID_TABLE, [
                    'linkId' => $link_id,
                    'aidId' => $product_category_id
                ]);
            }
        }

        wp_redirect(site_url('/weiterfuehrende-links-editieren'));
        exit;
    }
}

add_action('init', 'save_link');