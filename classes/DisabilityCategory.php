<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/table-names.php';

class DisabilityCategory {
    public int $id;
    public string $name;
    public string $description;

    public function __construct($id, $name, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    public function display(): string {
        $output = "<h2>" . esc_html($this->name) . "</h2>\n";
        if ($this->description && $this->description != "") {
            $output .= "<p>" . esc_html($this->description) . "</p>\n";
        }
        $output .= $this->list_disabilities();
        $additional_links = select_connected_links(
            LINK_FOR_DISABILIATION_TABLE,
            ADDITIONAL_LINK_TABLE,
            "disabilityId",
            "linkId",
            $this->id
        );

        if (!empty($additional_links)) {
            $output .= generate_link_list($additional_links);
        }

        return $output;
    }

    function list_disabilities(): string {
        $disabilities = select_of_category(DISABILITY_TABLE, $this->id);
        return generate_item_list(
            $disabilities,
            "behinderungen",
            error: "Keine spezifische Behinderung gefunden. "
        );
    }
}