<?php

/**
 * Modify and customize footer credits
 */
function remove_overwritten_functions()
{
    remove_action('generate_credits', 'generate_add_footer_info');
    add_action('generate_credits', 'add_footer_info');
}

add_action('after_setup_theme', 'remove_overwritten_functions');

/**
 * Add custom footer info
 */
function add_footer_info()
{
    $copyright = '<span class="copyright">&copy; ' . date('Y') . ' zhb//DoBuS - Bereich Behinderung und Studium - Technische Universität Dortmund</span>';
    $all_rights_reserved = 'Alle Rechte vorbehalten';
    $generate_press = 'Erstellt mit <a href="https://generatepress.com" target="_blank">GeneratePress</a>';
    $imprint = '<a href="impressum">Impressum</a>';
    $privacy = '<a href="datenschutz">Datenschutzerklärung</a>';
    $terms_of_use = '<a href="nutzungsbedingungen">Nutzungsbedingungen</a>';
    $accessibility = '<a href="erklaerung-zur-barrierefreiheit">Barrierefreiheit</a>';

    $credits = $copyright . " &bull; " . $all_rights_reserved . " &bull; "
        . $generate_press . " &bull; " . $imprint . " &bull; " . $privacy . " &bull; "
        . $terms_of_use . " &bull; " . $accessibility;

    echo apply_filters('generate_copyright', $credits); // phpcs:ignore
}
?>