<?php
$title = "Admin";
require_once(__DIR__ . "/../config/config.php");
require_once(ROOT_DIR . "/src/templates/header.php");
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");

if(!$isAdmin)
{
    header("Location:" . BASE_URL . "/index.php?error=noperm");
    exit();
}

echo "<div id='dashboard'  class='two-rows half-width'>";
include(ROOT_DIR . "/src/templates/institution/institution_verify.php");
echo "</div>";

require_once(ROOT_DIR . "/src/templates/footer.php");