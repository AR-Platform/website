<?php
$allowGuests = true;
$title = "Dashboard";
require_once(__DIR__ . "/../config/config.php");
require_once(ROOT_DIR . "/src/templates/header.php");
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");

$uploadPermission = false;
if (isset($_SESSION["uid"]))
{
    $courses = getCourseIDs($db, $_SESSION["uid"]);
    foreach ($courses as $course) {
        if ($course["admin"]) {
            $uploadPermission = true;
        }
    }
}
?>

    <div id="dashboard" class="two-rows half-width">
        <?php $auth ?: include(ROOT_DIR . "/src/templates/login.php"); ?>
        <?php $auth ?: include(ROOT_DIR . "/src/templates/register.php"); ?>
        <?php !$auth ?: include(ROOT_DIR . "/src/templates/content/content_convert.php"); ?>
        <?php !$auth || !$_SESSION["institution-verified"] || !$uploadPermission ?: include(ROOT_DIR . "/src/templates/content/content_upload.php"); ?>
        <?php !$auth || !$_SESSION["institution-verified"] ?: include(ROOT_DIR . "/src/templates/course/course_create.php"); ?>
        <?php !$auth || !$_SESSION["institution-verified"] ?: include(ROOT_DIR . "/src/templates/course/courses.php"); ?>
    </div>

<?php

require_once(ROOT_DIR . "/src/templates/footer.php");