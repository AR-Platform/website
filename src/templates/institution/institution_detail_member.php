<?php
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");

$institution = getInstitution($db, $_SESSION["institution"]);
echo "<div>";
echo "<h1>" . htmlspecialchars($institution["name"]) . "</h1>";
echo "</div>";