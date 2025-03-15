<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/constants.php';

class Product {
    public int $id;
    public string $name;
    public string $info_url;
    public string $info_alt;
    public string $description;
    public bool $available_general;

    public function __construct($id, $name, $info_url, $info_alt, $description, $available_general) {
        $this->id = $id;
        $this->name = $name;
        $this->info_url = $info_url;
        $this->info_alt = $info_alt;
        $this->description = $description;
        $this->available_general = $available_general;
    }

    function get_universities() {
        return select_connected(
            AVAILABILITY_TABLE,
            'productId',
            UNIVERSITY_TABLE,
            'universityId',
            $this->id
        );
    }

    function list_universities(): string {
        if ($this->available_general) {
            return '<p>Dieses Produkt ist allgemein verfügbar. </p>';
        }

        $universities = $this->get_universities();
        $before_html = "<p>Folgende Hochschulen in Nordrhein-Westfalen bieten dieses Produkt an: </p>\n";
        $error = "Dieses Produkt wird in NRW leider von keiner Hochschule angeboten.";

        return generate_item_list(
            $universities,
            "hochschulen",
            $before_html,
            $error
        );
    }

    public function display(): string {
        $output = "<h2>" . esc_html($this->name) . "</h2>\n";
        $output .= "<p>" . esc_html($this->description) . "</p>\n";

        if ($this->info_url != '') {
            $output .= '<p><a href="'
                . esc_url($this->info_url) . '">'
                . esc_html($this->info_alt) . '</a></p>';
        } else {
            $output .= '<p>Kein Link mit weiterführenden Informationen vorhanden. </p>';
        }

        $output .= $this->list_universities();
        return $output;
    }
}