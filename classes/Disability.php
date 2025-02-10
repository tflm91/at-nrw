<?php

require_once get_stylesheet_directory() . '/classes/Impairment.php';
require_once get_stylesheet_directory() . '/table-names.php';

class Disability extends Impairment {
    public int $id;
    public int $categoryId;
    public string $name;
    public string $description;

    public function __construct(int $id, int $categoryId, string $name, string $description) {
        parent::__construct($id, $name, AIDS_WITH_DISABILITY_TABLE);
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->description = $description;
    }

    public function display(): string {
        $output = '<h2>' . $this->name . '</h2>';
        $output .= '<h3>Beschreibung</h3>';
        $output .= '<p>' . $this->description . '</p>';
        $output .= $this->list_suitable_aids();
        return $output;
    }
}