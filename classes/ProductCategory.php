<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/table-names.php';

class ProductCategory {
    public int $id;
    public string $name;
    public string $description;

    public function __construct($id, $name, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    public function display(): string {
        $output = "<h2 id='category-" . $this->id . "'>" . esc_html($this->name) . "</h2>\n";
        if ($this->description && $this->description != "") {
            $output .= "<p>" . esc_html($this->description) . "</p>\n";
        }
        $output .= $this->list_products();
        return $output;
    }

    function get_products() {
        return select_connected(
            CATEGORY_OF_PRODUCT_TABLE,
            'categoryId',
            PRODUCT_TABLE,
            'productId',
            $this->id
        );
    }

    function list_products(): string {
        return generate_item_list(
            $this->get_products(),
            "hilfsmittel",
            error: "Keine Hilfsmittel zu dieser Kategorie gefunden. "
        );
    }
}