<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

function university_form(): bool|string {
    $university_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $is_editing = ($university_id > 0);

    $current_university = null;
    $selected_product_ids = [];

    if ($is_editing) {
        $current_university = select_one(UNIVERSITY_TABLE, $university_id);
        $selected_product_ids = select_associated_ids(
            AVAILABILITY_TABLE,
            'universityId',
            'productId',
            $university_id
        );
    }

    $general_products = select_conditional_product(PRODUCT_TABLE, 'availableEverywhere');
    $non_general_products = select_non_conditional_product(PRODUCT_TABLE, 'availableEverywhere');
    ob_start();
    ?>
    <form method="post">
        <label for="university_name">Name der Hochschule</label>
        <input type="text" id="university_name" name="university_name" required
               value="<?php echo $is_editing ? esc_attr($current_university->name) : ''; ?>"><br><br>

        <label for="university_division">Arbeitsbereich der Ansprechperson</label>
        <input type="text" id="university_division" name="university_division" required
                value="<?php echo $is_editing ? esc_attr($current_university->division) : '';?>"><br><br>

        <label for="university_contact_name">Name der Ansprechperson</label>
        <input type="text" id="university_contact_name" name="university_contact_name" required
               value="<?php echo $is_editing ? esc_attr($current_university->contactName) : '';?>"><br><br>

        <b>Telefonnummer: </b><br>
        <label>Telefonnummer im internationalen Format: <input type="tel" name="university_phone_number"
                                                               pattern="\+{1-9}{0-9}+"
                                                               value="<?php echo $is_editing ? esc_html($current_university->phoneNumber) : ''; ?>"></label><br><br>
        <label>Alternativtext: <input type="text" name="university_phone_alt" value="<?php echo $is_editing ? esc_html($current_university->phoneAlt) : ''; ?>"></label><br><br>

        <label for="university_email">E-Mail-Adresse</label>
        <input type="email" id="university_email" name="university_email" required
               value="<?php echo $is_editing ? esc_attr($current_university->email) : '';?>"><br><br>

        <b>Link zur Beratungsstelle: </b><br>
        <label>URL: <input type="url" name="university_contact_url" value="<?php echo $is_editing ? esc_url($current_university->contactURL) : ''; ?>"></label><br><br>
        <label>Alternativtext: <input type="text" name="university_contact_alt" value="<?php echo $is_editing ? esc_html($current_university->contactAlt) : ''; ?>"></label><br><br>

        <label for="university_workspaces">Arbeitsplätze:</label><br>
        <textarea id="university_workspaces" name="university_workspaces" required><?php echo $is_editing ? esc_attr($current_university->workspaces) : ''; ?></textarea><br><br>

        <fieldset>
            <legend>Angebotene Hilfsmittel auswählen:</legend>
            <div>
                <p>Folgende Hilfsmittel sind grundsätzlich verfügbar und können daher nicht für diese Hochschule ausgewählt werden: </p>
                <ul>
                    <?php foreach ($general_products as $product): ?>
                        <li><?php echo esc_html($product->name)?></li>
                    <?php endforeach;?>
                </ul>
            </div>
            <p>Folgende Hilfsmittel können ausgewählt werden: </p>
            <?php foreach ($non_general_products as $product): ?>
                <label>
                    <input type="checkbox" name="selected_products[]" value="<?php echo esc_attr($product->id); ?>"
                        <?php checked(in_array($product->id, $selected_product_ids));  ?>>
                    <?php echo esc_html($product->name); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset><br>

        <?php if ($is_editing): ?>
            <input type="hidden" name="university_id" value="<?php echo esc_attr($university_id) ?>">
        <?php endif; ?>

        <button type="submit" name="save_university">Speichern</button>
        <a href="<?php echo site_url('/hochschulen-editieren')?>">
            <button type="button">Abbrechen</button>
        </a>
    </form>
    <?php
    return ob_get_clean();
}

add_shortcode('university_form', 'university_form');

function sanitize_phone_number($phone): array|string|null {
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    if (!filter_var($phone, FILTER_SANITIZE_NUMBER_INT)) {
        return'';
    }

    return $phone;
}

function save_university(): void {
    if (isset($_POST['save_university'])) {
        global $wpdb;

        $name = sanitize_text_field($_POST['university_name']);
        $division = sanitize_text_field($_POST['university_division']);
        $contact_name = sanitize_text_field($_POST['university_contact_name']);

        $phone_number = htmlspecialchars(
            sanitize_phone_number($_POST['university_phone_number']),
            ENT_QUOTES,
            'UTF-8'
        );

        $phone_alt = sanitize_text_field($_POST['university_phone_alt']);
        $email = sanitize_email($_POST['university_email']);

        $contact_url = esc_url_raw($_POST['university_contact_url']);
        $contact_alt = sanitize_text_field($_POST['university_contact_alt']);
        $workspaces = sanitize_text_field($_POST['university_workspaces']);

        $selected_products = $_POST['selected_products'] ?? [];

        if (!empty($_POST['university_id'])) {
            $university_id = intval($_POST['university_id']);
            $wpdb->update(
                UNIVERSITY_TABLE,
                [
                    'name' => $name,
                    'division' => $division,
                    'contactName' => $contact_name,
                    'phoneNumber' => $phone_number,
                    'phoneAlt' => $phone_alt,
                    'email' => $email,
                    'contactUrl' => $contact_url,
                    'contactAlt' => $contact_alt,
                    'workspaces' => $workspaces
                ],
                ['id' => $university_id]
            );

            $wpdb->delete(AVAILABILITY_TABLE, ['universityId' => $university_id]);

            foreach ($selected_products as $product_id) {
                $wpdb->insert(AVAILABILITY_TABLE, [
                    'universityId' => $university_id,
                    'productId' => $product_id
                ]);
            }
        } else {
            $wpdb->insert(UNIVERSITY_TABLE, [
                'name'=> $name,
                'division' => $division,
                'contactName' => $contact_name,
                'phoneNumber' => $phone_number,
                'phoneAlt' => $phone_alt,
                'email' => $email,
                'contactUrl' => $contact_url,
                'contactAlt' => $contact_alt,
                'workspaces' => $workspaces
            ]);

            $university_id = $wpdb->insert_id;

            foreach ($selected_products as $product_id) {
                $wpdb->insert(AVAILABILITY_TABLE, [
                    'universityId' => $university_id,
                    'productId' => intval($product_id)
                ]);
            }
        }

        wp_redirect(site_url('/hochschulen-editieren'));
        exit;
    }
}

add_action('init', 'save_university');