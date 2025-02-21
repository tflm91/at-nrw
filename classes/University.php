<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

class University {
    public int $id;
    public string $name;
    public string $divsion;
    public string $contact_name;
    public string $phone_number;
    public string $phone_alt;
    public string $email;
    public string $contact_url;
    public string $contact_alt;
    public string $workspaces;

    public function __construct(
        $id,
        $name,
        $divsion,
        $contact_name,
        $phone_number,
        $phone_alt,
        $email,
        $contact_url,
        $contact_alt,
        $workspaces
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->divsion = $divsion;
        $this->contact_name = $contact_name;
        $this->phone_number = $phone_number;
        $this->phone_alt = $phone_alt;
        $this->email = $email;
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

    public function list_special_aids(): string {
        $before_html = "<h4>Spezielle Hilfsmittel der Hochschule</h4>\n";
        $error = "Diese Hochschule bietet nur die Hilfsmittel an, die an allen Hochschulen angeboten werden. ";
        return generate_item_list(
            $this->get_aids(),
            "hilfsmittel",
            $before_html,
            $error
        );
    }

    public function display_information(): string {
        $output = "<h2>" . esc_html($this->name) . "</h2>\n";
        $output .= "<h3>Kontaktinformationen zur Beratungsstelle für behinderte Studierende </h3>\n";
        $output .= '<p><b>Arbeitsbereich: </b>' . esc_html($this->divsion) . '</p>';
        $output .= '<p><b>Name der Ansprechperson: </b>' . esc_html($this->contact_name) . '</p>';

        if ($this->phone_number != '') {
            $output .= '<p><b>Telefonnummer: </b><a href="' . esc_url('tel:' . $this->phone_number) . '">' . esc_html($this->phone_alt) . '</a></p>';
        } else {
            $output .= '<p><b>Telefonnummer: </b>nicht vorhanden</p>';
        }

        if ($this->email != '') {
            $output .= '<p><b>E-Mail: </b><a href="' . esc_url('mailto:' . $this->email) . '">' . $this->email . '</a></p>';
        } else {
            $output .= '<p><b>E-Mail: </b>nicht vorhanden</p>';
        }

        if ($this->contact_url != '') {
            $output .= '<p><b>Link zur Beratungsstelle: </b><a href="' . esc_url($this->contact_url) . '">' . esc_html($this->contact_alt) . '</a></p>';
        } else {
            $output .= "<p>Kein Link zur Beratungsstelle vorhanden. </p>";
        }
        $output .= '<h3>Arbeitsräume</h3>';
        $output .= '<p>' . esc_html($this->workspaces) . '</p>';
        return $output;

    }
}