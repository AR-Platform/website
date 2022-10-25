<?php
$title = "Institution";
require_once(__DIR__ . "/../config/config.php");
require_once(ROOT_DIR . "/src/templates/header.php");
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");

echo "<div class='half-width default-top-margin box-children'>";

if ($_SESSION["institution"] ?? false) {
    $institutionID = $_SESSION["institution"];
    if (isInstitutionVerified($db, $institutionID)) {
        if ($_SESSION["institution-admin"] ?? false) {
            include(ROOT_DIR . "/src/templates/institution/institution_detail.php");
            echo '</div>';
            echo '<div id="dashboard" class="two-rows half-width">';
            include(ROOT_DIR . "/src/templates/institution/institution_member_add.php");
            include(ROOT_DIR . "/src/templates/institution/institution_member_remove.php");
        } else {
            include(ROOT_DIR . "/src/templates/institution/institution_detail_member.php");
        }
    } else {
        echo "<h1>Not verified yet!</h1>";
    }
} else {
    include(ROOT_DIR . "/src/templates/institution/institution_apply.php");
}

echo "</div>";

require_once(ROOT_DIR . "/src/templates/footer.php");