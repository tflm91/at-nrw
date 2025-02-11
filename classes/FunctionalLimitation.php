<?php

require_once get_stylesheet_directory() . '/table-names.php';
require_once get_stylesheet_directory() . '/classes/Impairment.php';

class FunctionalLimitation extends Impairment {
    public function __construct($id, $name) {
        parent::__construct($id, $name, AIDS_WITH_LIMITATION_TABLE);
    }

    public function display(): string {
        $output =  '<h3>' . $this->name . '</h3>';
        $output .= $this->list_suitable_aids();
        return $output;
    }
}