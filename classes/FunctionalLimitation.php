<?php

require_once get_stylesheet_directory() . '/constants.php';
require_once get_stylesheet_directory() . '/classes/Impairment.php';

class FunctionalLimitation extends Impairment {
    public function __construct($id, $name) {
        parent::__construct($id, $name, AIDS_WITH_LIMITATION_TABLE);
    }

    public function display(): string {
        $output =  '<h2>' . $this->name . '</h2>';
        $output .= $this->list_suitable_aids();
        return $output;
    }
}