<?php
require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

add_shortcode('editable_disability_categories', 'list_editable_disability_categories');

function list_editable_disability_categories(): string {
    $categories = select_all(DISABILITY_CATEGORY_TABLE);
    $output = "";
    if (!empty ($categories)) {
        $output .= "<table>";
        $output .= "<tr><th>Behinderungskategorie</th><th>Aktionen</th></tr>";
        foreach ($categories as $category) {
            $output .= "<tr>";
            $output .= "<td>" . $category->name . "</td>";
            $output .= "<td>";
            $output .= '<a href="' . esc_url(site_url('/behinderungskategorie-bearbeiten?id=' . $category->id)) . '">';
            $output .= '<button>Bearbeiten</button>';
            $output .= '</a>';
            $output .= "<button class='delete-category' data-id='". esc_attr($category->id) . "'>Löschen</button></td>";
            $output .= "</td>";
            $output .= "</tr>";
        }
        $output .= "</table>";

        $output .= '<div id="delete-dialogue" style="display: none;">' .
            '<div class="modal-content" id="modal-content"></div>' .
            '</div>';
    } else {
        $output .= '<p>Keine Behinderungskategorien gefunden.</p> ';
    }
    return $output;
}

function delete_disability_category_script(): void {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".delete-category").forEach(button => {
               button.addEventListener("click", function (event) {
                   event.preventDefault();
                  let categoryId = this.getAttribute("data-id");

                  fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=check_disability_category&category_id=' + categoryId)
                      .then(response => response.json())
                      .then(data => {
                          let dialogue = document.getElementById("delete-dialogue");
                          let modalContent = document.getElementById('modal-content');

                          if (data.hasEntries) {
                              modalContent.innerHTML = '<span class="close" onclick="closeDialogue()">&times;</span> ' +
                                  "<p>Diese Kategorie enthält noch folgende Behinderungen und kann daher nicht gelöscht werden. </p><ul>" +
                                  data.entries.map(entry => "<li>" + entry.name + "</li>").join("") +
                                  "</ul><p>Bitte lösche erst diese Behinderungen, bevor du die Kategorie löschst</p>" +
                                  "<button onClick='closeDialogue()'>Schließen</button>";
                          } else {
                              modalContent.innerHTML = '<span class="close" onclick="closeDialogue()">&times;</span> ' +
                                  "<p>Bist du sicher, dass du die Kategorie löschen möchtest?</p>" +
                                  "<button onclick='deleteDisabilityCategory(" + categoryId + ")'>Ja</button> " +
                                  "<button onclick='closeDialogue()'>Abbrechen</button>";
                          }
                          dialogue.style.display = "block";
                      })
               });
            });
        });

        function deleteDisabilityCategory(categoryId) {
            fetch('<?php echo admin_url("admin-ajax.php") ?>', {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=delete_disability_category&category_id=" + categoryId
            })
                .then(() => {
                    location.reload();
                })
        }
    </script>
<?php
}

add_action('wp_footer', 'delete_disability_category_script');

function check_disability_category(): void {
    $category_id = intval($_GET['category_id']);
    $disabilities = select_of_category(DISABILITY_TABLE, $category_id) ?? [];

    wp_send_json([
       'hasEntries' => count($disabilities) > 0,
        'entries' => $disabilities
    ]);
}

add_action('wp_ajax_check_disability_category', 'check_disability_category');
add_action('wp_ajax_nopriv_check_disability_category', 'check_disability_category');

function delete_disability_category(): void {
    $category_id = intval($_POST['category_id']);

    $disabilities = select_of_category(DISABILITY_TABLE, $category_id) ?? [];

    if (empty($disabilities)) {
        delete_element(DISABILITY_CATEGORY_TABLE, $category_id);
        wp_send_json(['success' => true]);
    } else {
        wp_send_json(['success' => false, 'error' => 'Category still contains disabilities. ']);
    }
}
;
add_action('wp_ajax_delete_disability_category', 'delete_disability_category');