<?php
$title = "Courses";
require_once(__DIR__ . "/../config/config.php");
require_once(ROOT_DIR . "/src/templates/header.php");
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");

if (!$_SESSION["institution"]) {
    header("Location:" . BASE_URL . "/index.php?error=noinstitute");
}

/**
 * Checks if the user has access to the requested course.
 *
 * If the user has no access to the course the session is destroyed.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The user ID.
 * @param int $courseID The course ID.
 * @return void No return value.
 */
function validatePermission(PDO $db, int $userID, int $courseID): void
{
    if (!checkCoursePermission($db, $userID, $courseID)) {
        destroySession();
    }
}

if (isset($_GET["view"]) && $_GET["view"]) {
    validatePermission($db, $_SESSION["uid"], $_GET["view"]);
    $courseID = $_GET["view"];
    $courseAdmin = checkCourseAdminPermission($db, $_SESSION["uid"], $courseID);
    echo "<div class='box-children half-width default-top-margin'>";
    include(ROOT_DIR . "/src/templates/course/course_detail.php");
    echo "</div>";
    if($courseAdmin) {
        echo '<div id="dashboard" class="two-rows half-width">';
        include(ROOT_DIR . "/src/templates/course/course_upload.php");
        include(ROOT_DIR . "/src/templates/course/course_folder_create.php");
        echo '</div>';
    }
} elseif (isset($_GET["edit"]) && $_GET["edit"])
{
    validatePermission($db, $_SESSION["uid"], $_GET["edit"]);
    $courseID = $_GET["edit"];
    echo "<div class='box-children half-width default-top-margin'>";
    include(ROOT_DIR . "/src/templates/course/course_edit.php");
    echo "</div>";
    echo '<div id="dashboard" class="two-rows half-width">';
    include(ROOT_DIR . "/src/templates/course/course_member_add.php");
    include(ROOT_DIR . "/src/templates/course/course_member_remove.php");
    include(ROOT_DIR . "/src/templates/course/course_member_transfer.php");
    echo '</div>';
}

echo '<div class="half-width box-children default-top-margin">';
include(ROOT_DIR . "/src/templates/course/course_list.php");
echo '</div>';


require_once(ROOT_DIR . "/src/templates/footer.php");