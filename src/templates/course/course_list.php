<?php
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
require_once(ROOT_DIR . "/src/templates/course/course_leave_modal.php");

$courseArray = getCourseIDs($db, $_SESSION["uid"]);

foreach ($courseArray as $course) {
    echo createCourseCard($db, $course["id"], $course["admin"] ?? false);
}

/**
 * Creates the required HTML code to visualize a course card.
 *
 * Each course card contains the name, the description and the select and leave buttons.
 * Course admins have an edit button instead of the leave button.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param bool $courseAdmin If set to true course admin features will be visible.
 * @return string The created HTML code to represent the course card.
 * @noinspection BadExpressionStatementJS
 * @noinspection JSVoidFunctionReturnValueUsed
 */
function createCourseCard(PDO $db, int $courseID, bool $courseAdmin): string
{
    $course = getCourse($db, $courseID);
    $html = array();
    $courseName = htmlspecialchars($course['name']);
    $courseDesc = htmlspecialchars($course["description"]);
    $html[] = "<form action='courses.php' method='get'><h3 class='two-inputs-row'>$courseName</h3><div class='two-inputs-row float-right'>";
    if($courseAdmin)
    {
        $html[] = "<button type='submit' name='edit' value='{$course['id']}' class='two-inputs-row'>" . LANG_EDIT . "</button>";
    }
    else
    {
        $safeName = htmlentities(str_replace(array("\r\n", "\r", "\n"), "", $courseName), ENT_QUOTES);
        $funcParameter = $courseID . ", \"$safeName\"";
        $html[] = "<button type='button' onclick='openCourseLeaveModal($funcParameter)' class='two-inputs-row critical'>" . LANG_LEAVE . "</button>";
    }
    $html[] = "<button type='submit' name='view' value='{$course['id']}' class='two-inputs-row float-right'>" . LANG_SELECT . "</button>";
    $html[] = "</div><p>$courseDesc</p></form>";
    return join("", $html);
}