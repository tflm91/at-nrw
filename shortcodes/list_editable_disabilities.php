<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/constants.php';

add_shortcode('list_editable_disabilities', 'list_editable_disabilities');

function list_editable_disabilities(): string {
    $disabilities = select_all(DISABILITY_TABLE);
    $output = '';

    if (!empty($disabilities)) {
        $output .= '<table>';
        $output .= '<tr><th>Beeinträchtigungsform</th><th>Aktionen</th></tr>';

        foreach ($disabilities as $disability) {
            $output .= '<tr>';
            $output .= '<td>' . $disability->name . '</td>';
            $output .= '<td>';
            $output .= '<a href="' . esc_url(site_url('/beeintraechtigungsform-bearbeiten?id=' . $disability->id)) . '">';
            $output .= '<button>Bearbeiten</button>';
            $output .= '</a>';
            $output .= '<button class="delete-disability" data-id="' . esc_attr($disability->id) . '">Löschen</button>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';

        $output .= '<div id="delete-dialogue" style="display: none">' .
        '<div class="modal-content" id="modal-content"></div>' .
        '</div>';

    } else {
        $output .= '<p>Keine Beeinträchtigungsformen gefunden.</p>';
    }

    return $output;
}

function delete_disability_script(): void {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
           document.querySelectorAll(".delete-disability").forEach(button => {
              button.addEventListener("click" , function (event) {
                  event.preventDefault();
                  let disabilityId = this.getAttribute("data-id");
                  let dialogue = document.getElementById('delete-dialogue');
                  let modalContent = document.getElementById('modal-content');

                  modalContent.innerHTML = "<span class='close' onclick='closeDialogue()'>&times;</span>" +
                      "<p>Bist du sicher, dass du diese Beeinträchtigungsform löschen möchtest?</p>" +
                      "<button onclick='deleteDisability(" + disabilityId + ")'>Ja</button> " +
                      "<button onclick='closeDialogue()'>Abbrechen</button>";

                  dialogue.style.display = "block";
              });
           });
        });

        function deleteDisability(disabilityId) {
            fetch('<?php echo admin_url("admin-ajax.php")?>', {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=delete_disability&disability_id=" + disabilityId
            })
                .then(() => {
                    this.location.reload();
                })
        }
    </script>
<?php
}

add_action('wp_footer', 'delete_disability_script');

function delete_disability(): void {
    $disability_id = intval($_POST['disability_id']);
    delete_element(DISABILITY_TABLE, $disability_id);
    wp_send_json(['success' => true]);
}

add_action('wp_ajax_delete_disability', 'delete_disability');