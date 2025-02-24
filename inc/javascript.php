<?php
function general_javascript() {
    ?>
    <script>
        function closeDialogue() {
            document.getElementById("delete-dialogue").style.display = "none";
        }
    </script>
    <?php
}

add_action('wp_footer', 'general_javascript');