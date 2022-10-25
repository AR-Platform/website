<?php
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");

$institution = getInstitution($db, $_SESSION["institution"]);
$memberAmount = count(getInstitutionMembers($db, $institution["id"]));
$courseAmount = count(getCourses($db, $institution["id"]));
echo "<div>";
echo "<h1>" . htmlspecialchars($institution["name"]) . "</h1>";
echo "<p>" . LANG_MEMBER_AMOUNT . ": $memberAmount</p>";
echo "<p>" . LANG_COURSE_AMOUNT . ": $courseAmount</p>";
echo "</div>";