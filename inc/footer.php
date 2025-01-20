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
    $copyright = '<span class="copyright">&copy; ' . date('Y') . ' <a href="https://zhb.tu-dortmund.de/">zhb</a>'
        .'//<a href="https://dobus.zhb.tu-dortmund.de/">DoBuS - Bereich Behinderung und Studium</a>'
        . ' - <a href="https://www.tu-dortmund.de/">Technische Universität Dortmund</a></span>';
    $all_rights_reserved = 'Alle Rechte vorbehalten';
    $generate_press = 'Erstellt mit <a href="https://generatepress.com">GeneratePress</a>';
    $imprint = '<a href="https://dobus.zhb.tu-dortmund.de/impressum/">Impressum</a>';
    $privacy = '<a href="datenschutz">Datenschutzerklärung</a>';
    $accessibility = '<a href="erklaerung-zur-barrierefreiheit">Barrierefreiheit</a>';

    $credits = $copyright . " &bull; " . $all_rights_reserved . " &bull; "
        . $generate_press . " &bull; " . $imprint . " &bull; " . $privacy . " &bull; "
         . $accessibility;

    echo apply_filters('generate_copyright', $credits); // phpcs:ignore
}
?>