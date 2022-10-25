<?php
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
require_once(ROOT_DIR . "/src/templates/content/content_delete_modal.php");


echo createCourseCard($db);


/**
 * Creates a card containing all courses for the dashboard.
 *
 * @param PDO $db The PDO database instance.
 * @return string The string representation of the HTML code to visualize the course card.
 * @noinspection BadExpressionStatementJS
 * @noinspection JSVoidFunctionReturnValueUsed
 */
function createCourseCard(PDO $db): string
{
    $courseArray = getCourseNameIDs($db, $_SESSION["uid"]);
    $html = [];
    $html[] = "<div>";
    $html[] = "<h3>" . LANG_COURSES . "</h3>";
    foreach ($courseArray as $course) {
        $html[] = "<div class='simple-box'><a href='courses.php?view={$course["id"]}'>";
        $html[] = "<h4 class='centered-grid'>" . htmlspecialchars($course["name"]) . "</h4>";
        $html[] = "</a></div>";
    }
    $html[] = "</div>";
    return implode("", $html);
}