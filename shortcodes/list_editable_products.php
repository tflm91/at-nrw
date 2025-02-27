<?php

require_once get_stylesheet_directory() . '/inc/database.php';
require_once get_stylesheet_directory() . '/table-names.php';

add_shortcode('list_editable_products', 'list_editable_products');

function list_editable_products(): string {
    $products = select_all(PRODUCT_TABLE);
    $output = '';

    if (!empty($products)) {
        $output .= '<table>';
        $output .= '<tr><th>Produkt</th><th>Aktionen</th></tr>';

        foreach ($products as $product) {
            $output .= '<tr>';
            $output .= '<td>' . $product->name . '</td>';
            $output .= '<td>';
            $output .= '<a href="' . esc_url(site_url('/produkt-bearbeiten?id=' . $product->id)) . '">';
            $output .= '<button>Bearbeiten</button>';
            $output .= '</a>';
            $output .= '<button class="delete-product" data-id="' . esc_attr($product->id) . '">Löschen</button>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        $output .= '</table>';

        $output .= '<div id="delete-dialogue" style="display: none">' .
            '<div class="modal-content" id="modal-content"></div>' .
            '</div>';

    } else {
        $output .= '<p>Keine Produkte gefunden.</p>';
    }

    return $output;
}

function delete_product_script(): void {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".delete-product").forEach(button => {
                button.addEventListener("click" , function (event) {
                    event.preventDefault();
                    let productId = this.getAttribute("data-id");
                    let dialogue = document.getElementById('delete-dialogue');
                    let modalContent = document.getElementById('modal-content');

                    modalContent.innerHTML = "<span class='close' onclick='closeDialogue()'>&times;</span>" +
                        "<p>Bist du sicher, dass du dieses Produkt löschen möchtest?</p>" +
                        "<button onclick='deleteProduct(" + productId + ")'>Ja</button> " +
                        "<button onclick='closeDialogue()'>Abbrechen</button>";

                    dialogue.style.display = "block";
                });
            });
        });

        function deleteProduct(productId) {
            fetch('<?php echo admin_url("admin-ajax.php")?>', {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=delete_product&product_id=" + productId
            })
                .then(() => {
                    this.location.reload();
                })
        }
    </script>
    <?php
}

add_action('wp_footer', 'delete_product_script');

function delete_product(): void {
    $product_id = intval($_POST['product_id']);
    delete_element(PRODUCT_TABLE, $product_id);
    wp_send_json(['success' => true]);
}

add_action('wp_ajax_delete_product', 'delete_product');