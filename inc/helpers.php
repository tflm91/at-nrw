<?php
/* generate a link list of different items */
function generate_item_list($items, $subpage, $before_html = null, $error = null, $id_prefix = null): string {
    $output = "";
    if($items) {
        if($before_html) {
            $output .= $before_html;
        }

        $output .= "<ul>";

        foreach($items as $item) {
            $output .= '<li>' . generate_item_link($item, $subpage, $id_prefix) . '</li>';
        }

        $output .= "</ul>\n";
    } else {
        if ($error) {
            $output .= "<p>" . $error . "</p>\n";
        }
    }

    return $output;
}

/* generate link to a specific item for link list */
function generate_item_link($item, $subpage, $id_prefix = null): string {
    $url = site_url(
        '/'
        . $subpage
        . (($id_prefix) ? '#' . $id_prefix . '-' . esc_attr($item->id) : '/' . esc_attr($item->id)));
    return '<a href="' . $url . '">' . esc_html($item->name) . '</a>';
}

/* generate list of additional links */
function generate_link_list($links) {
    $output = "<p>Weiterführende Links: </p>";
    $output .= '<ul>';
    foreach($links as $link) {
        $output .= '<li><a href="' . esc_url($link->URL) . '">' . esc_html($link->altText) . '</a></li>';
    }
    $output .= "</ul>\n";
    return $output;
}
?>

