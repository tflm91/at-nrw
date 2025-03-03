<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

add_shortcode('list_editable_links', 'list_editable_links');

function list_editable_links(): string {
    $links = select_all(ADDITIONAL_LINK_TABLE, false);
    $output = '';

    if (!empty($links)) {
        $output .= '<table>';
        $output .= '<tr><th>Link</th><th>Aktionen</th></tr>';

        foreach ($links as $link) {
            $output .= '<tr>';
            $output .= '<td>' . $link->altText . '<br> ('. $link->URL . ')</td>';
            $output .= '<td>';
            $output .= '<a href="' . esc_url(site_url('/weiterfuehrenden-link-bearbeiten?id=' . $link->id)) . '">';
            $output .= '<button>Bearbeiten</button>';
            $output .= '</a>';
            $output .= '<button class="delete-link" data-id="' . esc_attr($link->id) . '">Löschen</button>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';

        $output .= '<div id="delete-dialogue" style="display: none">' .
            '<div class="modal-content" id="modal-content"></div>' .
            '</div>';

    } else {
        $output .= '<p>Keine weiterführenden Links gefunden.</p>';
    }

    return $output;
}

function delete_link_script(): void {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".delete-link").forEach(button => {
                button.addEventListener("click" , function (event) {
                    event.preventDefault();
                    let linkId = this.getAttribute("data-id");
                    let dialogue = document.getElementById('delete-dialogue');
                    let modalContent = document.getElementById('modal-content');

                    modalContent.innerHTML = "<span class='close' onclick='closeDialogue()'>&times;</span>" +
                        "<p>Bist du sicher, dass du diesen Link löschen möchtest?</p>" +
                        "<button onclick='deleteLink(" + linkId + ")'>Ja</button> " +
                        "<button onclick='closeDialogue()'>Abbrechen</button>";

                    dialogue.style.display = "block";
                });
            });
        });

        function deleteLink(linkId) {
            fetch('<?php echo admin_url("admin-ajax.php")?>', {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=delete_link&link_id=" + linkId
            })
                .then(() => {
                    alert("Anfrage durchgeführt. ")
                    this.location.reload();
                })
        }
    </script>
    <?php
}

add_action('wp_footer', 'delete_link_script');

function delete_link(): void {
    $link_id = intval($_POST['link_id']);
    delete_element(ADDITIONAL_LINK_TABLE, $link_id);
    wp_send_json(['success' => true]);
}

add_action('wp_ajax_delete_link', 'delete_link');