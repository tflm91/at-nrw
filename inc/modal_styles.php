<?php
function add_modal_styles() {
    ?>
    <style>
        /* the modal background overlay */
        #delete-dialogue {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Dunkler Hintergrund */
        }

        /* the actual modal window */
        #delete-dialogue .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* close-button */
        #delete-dialogue .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        #delete-dialogue .close:hover {
            color: black;
        }
    </style>
    <?php
}
add_action('wp_head', 'add_modal_styles');