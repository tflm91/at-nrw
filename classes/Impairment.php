<?php

require_once get_stylesheet_directory() . '/inc/helpers.php';
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/constants.php';

class Impairment {
    public int $id;
    public string $name;
    public string $connection_table;

    public function __construct(int $id, string $name, string $connection_table) {
        $this->id = $id;
        $this->name = $name;
        $this->connection_table = $connection_table;
    }

    function find_suitable_aids() {
        return select_connected(
            $this->connection_table,
            'impairmentId',
            PRODUCT_CATEGORY_TABLE,
            'categoryId',
            $this->id
        );
    }

    function list_suitable_aids(): string {
        return generate_item_list(
            $this->find_suitable_aids(),
            'hilfsmittel',
            error: 'Keine passenden Hilfsmittel gefunden. ',
            id_prefix: 'category'
        );
    }
}