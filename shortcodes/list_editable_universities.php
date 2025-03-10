<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/constants.php';

add_shortcode('list_editable_universities', 'list_editable_universities');

function list_editable_universities(): string {
    $universities = select_all(UNIVERSITY_TABLE);
    $output = '';

    if (!empty($universities)) {
        $output .= '<table>';
        $output .= '<tr><th>Hochschule</th><th>Aktionen</th></tr>';

        foreach ($universities as $university) {
            $output .= '<tr>';
            $output .= '<td>' . $university->name . '</td>';
            $output .= '<td>';
            $output .= '<a href="' . esc_url(site_url('/hochschule-bearbeiten?id=' . $university->id)) . '">';
            $output .= '<button>Bearbeiten</button>';
            $output .= '</a>';
            $output .= '<button class="delete-university" data-id="' . esc_attr($university->id) . '">Löschen</button>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';

        $output .= '<div id="delete-dialogue" style="display: none">' .
            '<div class="modal-content" id="modal-content"></div>' .
            '</div>';

    } else {
        $output .= '<p>Keine Hochschulen gefunden.</p>';
    }

    return $output;
}

function delete_university_script(): void {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".delete-university").forEach(button => {
                button.addEventListener("click" , function (event) {
                    event.preventDefault();
                    let universityId = this.getAttribute("data-id");
                    let dialogue = document.getElementById('delete-dialogue');
                    let modalContent = document.getElementById('modal-content');

                    modalContent.innerHTML = "<span class='close' onclick='closeDialogue()'>&times;</span>" +
                        "<p>Bist du sicher, dass du diese Hochschule löschen möchtest?</p>" +
                        "<button onclick='deleteUniversity(" + universityId + ")'>Ja</button> " +
                        "<button onclick='closeDialogue()'>Abbrechen</button>";

                    dialogue.style.display = "block";
                });
            });
        });

        function deleteUniversity(universityId) {
            fetch('<?php echo admin_url("admin-ajax.php")?>', {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=delete_university&university_id=" + universityId
            })
                .then(() => {
                    this.location.reload();
                })
        }
    </script>
    <?php
}

add_action('wp_footer', 'delete_university_script');

function delete_university(): void {
    $university_id = intval($_POST['university_id']);
    delete_element(UNIVERSITY_TABLE, $university_id);
    wp_send_json(['success' => true]);
}

add_action('wp_ajax_delete_university', 'delete_university');