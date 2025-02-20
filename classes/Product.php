<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/table-names.php';

class Product {
    public int $id;
    public string $name;
    public string $manufacturer_url;
    public string $manufacturer_alt;
    public string $description;
    public bool $available_everywhere;

    public function __construct($id, $name, $manufacturer_url, $manufacturer_alt, $description, $available_everywhere) {
        $this->id = $id;
        $this->name = $name;
        $this->manufacturer_url = $manufacturer_url;
        $this->manufacturer_alt = $manufacturer_alt;
        $this->description = $description;
        $this->available_everywhere = $available_everywhere;
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
        if ($this->available_everywhere) {
            return '<p>Dieses Hilfsmittel ist an allen Hochschulen vorhanden. </p>';
        }

        $universities = $this->get_universities();
        $before_html = "<p>Folgende Hochschulen in Nordrhein-Westfalen bieten dieses Hilfsmittel an: </p>\n";
        $error = "Dieses Hilfsmittel wird in NRW leider von keiner Hochschule angeboten.";

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

        if ($this->manufacturer_url != '') {
            $output .= '<p><a href="'
                . esc_url($this->manufacturer_url) . '">'
                . esc_html($this->manufacturer_alt) . '</a></p>';
        } else {
            $output .= '<p>Kein Link zur Herstellerwebsite vorhanden. </p>';
        }

        $output .= $this->list_universities();
        return $output;
    }
}