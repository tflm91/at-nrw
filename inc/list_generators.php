<?php
function generate_item_link($item, $underside, $id_prefix) {
    $url = site_url(
        '/'
        . $underside
        . (($id_prefix) ? '#' . $id_prefix . '-' . esc_attr($item->id) : '/' . esc_attr($item->id)));
    return '<a href="' . $url . '">' . esc_html($item->name) . '</a>';
}
?>

