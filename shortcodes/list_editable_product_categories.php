<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

add_shortcode('list_editable_product_categories', 'list_editable_product_categories');

function list_editable_product_categories(): string {
    $product_categories = select_all(PRODUCT_CATEGORY_TABLE);
    $output = '';

    if (!empty($product_categories)) {
        $output .= '<table>';
        $output .= '<tr><th>Produktkategorie</th><th>Aktionen</th></tr>';

        foreach ($product_categories as $product_category) {
            $output .= '<tr>';
            $output .= '<td>' . $product_category->name . '</td>';
            $output .= '<td>';
            $output .= '<a href="' . esc_url(site_url('/produktkategorie-bearbeiten?id=' . $product_category->id)) . '">';
            $output .= '<button>Bearbeiten</button>';
            $output .= '</a>';
            $output .= '<button class="delete-category" data-id="' . esc_attr($product_category->id) . '">Löschen</button>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';

        $output .= '<div id="delete-dialogue" style="display: none">' .
            '<div class="modal-content" id="modal-content"></div>' .
            '</div>';

    } else {
        $output .= '<p>Keine Produktkategorien gefunden.</p>';
    }

    return $output;
}

function delete_product_category_script(): void {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".delete-category").forEach(button => {
                button.addEventListener("click" , function (event) {
                    event.preventDefault();
                    let categoryId = this.getAttribute("data-id");
                    let dialogue = document.getElementById('delete-dialogue');
                    let modalContent = document.getElementById('modal-content');

                    modalContent.innerHTML = "<span class='close' onclick='closeDialogue()'>&times;</span>" +
                        "<p>Bist du sicher, dass du diese Produktkategorie löschen möchtest?</p>" +
                        "<button onclick='deleteProductCategory(" + categoryId + ")'>Ja</button> " +
                        "<button onclick='closeDialogue()'>Abbrechen</button>";

                    dialogue.style.display = "block";
                });
            });
        });

        function deleteProductCategory(categoryId) {
            fetch('<?php echo admin_url("admin-ajax.php")?>', {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=delete_product_category&category_id=" + categoryId
            })
                .then(() => {
                    this.location.reload();
                })
        }
    </script>
    <?php
}

add_action('wp_footer', 'delete_product_category_script');

function delete_product_category(): void {
    $category_id = intval($_POST['disability_id']);
    delete_element(PRODUCT_CATEGORY_TABLE, $category_id);
    wp_send_json(['success' => true]);
}

add_action('wp_ajax_delete_product_category', 'delete_product_category');