<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

add_shortcode('list_editable_limitations', 'list_editable_limitations');

function list_editable_limitations(): string {
    $limitations = select_all(FUNCTIONAL_LIMITATION_TABLE, false);
    $output = '';

    if (!empty($limitations)) {
        $output .= '<table>';
        $output .= '<tr><th>Funktionseinschränkung</th><th>Aktionen</th></tr>';

        foreach ($limitations as $limitation) {
            $output .= '<tr>';
            $output .= '<td>' . $limitation->name . '</td>';
            $output .= '<td>';
            $output .= '<a href="' . esc_url(site_url('/funktionseinschraenkung-bearbeiten?id=' . $limitation->id)) . '">';
            $output .= '<button>Bearbeiten</button>';
            $output .= '</a>';
            $output .= '<button class="delete-limitation" data-id="' . esc_attr($limitation->id) . '">Löschen</button>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';

        $output .= '<div id="delete-dialogue" style="display: none">' .
            '<div class="modal-content" id="modal-content"></div>' .
            '</div>';

    } else {
        $output .= '<p>Keine Funktionseinschränkungen gefunden.</p>';
    }

    return $output;
}

function delete_limitation_script(): void {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".delete-limitation").forEach(button => {
                button.addEventListener("click" , function (event) {
                    event.preventDefault();
                    let limitationId = this.getAttribute("data-id");
                    let dialogue = document.getElementById('delete-dialogue');
                    let modalContent = document.getElementById('modal-content');

                    modalContent.innerHTML = "<span class='close' onclick='closeDialogue()'>&times;</span>" +
                        "<p>Bist du sicher, dass du diese Funktionseinschränkung löschen möchtest?</p>" +
                        "<button onclick='deleteLimitation(" + limitationId + ")'>Ja</button> " +
                        "<button onclick='closeDialogue()'>Abbrechen</button>";

                    dialogue.style.display = "block";
                });
            });
        });

        function deleteLimitation(limitationId) {
            fetch('<?php echo admin_url("admin-ajax.php")?>', {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=delete_limitation&limitation_id=" + limitationId
            })
                .then(() => {
                    this.location.reload();
                })
        }
    </script>
    <?php
}

add_action('wp_footer', 'delete_limitation_script');

function delete_limitation(): void {
    $limitation_id = intval($_POST['limitation_id']);
    delete_element(FUNCTIONAL_LIMITATION_TABLE, $limitation_id);
    wp_send_json(['success' => true]);
}

add_action('wp_ajax_delete_limitation', 'delete_limitation');