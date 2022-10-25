<?php
$title = "Convert";
require_once(__DIR__ . "/../config/config.php");
require_once(ROOT_DIR . "/src/templates/header.php");

echo "<div class='half-width default-top-margin box-children'>";

include(ROOT_DIR . "/src/templates/content/content_convert.php");

echo "</div>";

require_once(ROOT_DIR . "/src/templates/footer.php");