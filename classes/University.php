<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

class University {
    public int $id;
    public string $name;
    public string $contact_information;
    public string $contact_url;
    public string $contact_alt;
    public string $workspaces;

    public function __construct($id, $name, $contact_information, $contact_url, $contact_alt, $workspaces) {
        $this->id = $id;
        $this->name = $name;
        $this->contact_information = $contact_information;
        $this->contact_url = $contact_url;
        $this->contact_alt = $contact_alt;
        $this->workspaces = $workspaces;
    }

    function get_aids() {
        return select_connected(
            AVAILABILITY_TABLE,
            'universityId',
            PRODUCT_TABLE,
            'productId',
            $this->id
        );
    }

    public function list_aids(): string {
        $before_html = "<p><b>Verfügbare Hilfsmittel:</b></p>\n";
        $error = "Diese Hochschule bietet leider keine Hilfsmittel an. ";
        return generate_item_list(
            $this->get_aids(),
            "hilfsmittel",
            $before_html,
            $error
        );
    }

    public function display_information() {
        $output = "<h2>" . esc_html($this->name) . "</h2>\n";
        $output .= "<p><b>Kontaktinformationen zur Beratungsstelle für behinderte Studierende:</b><br />\n";
        $output .= nl2br($this->contact_information) . "</p>\n";
        if ($this->contact_url) {
            $output .= "<p><b>Link zur Beratungsstelle: </b><a href='"
                . esc_url($this->contact_url) . "'>". esc_html($this->contact_alt) . "</a></p>";
        } else {
            $output .= "<p>Kein Link zur Beratungsstelle vorhanden. </p>";
        }
        $output .= "<p><b>Arbeitsräume: </b>" . esc_html($this->workspaces) . "</p>\n";
        return $output;

    }
}