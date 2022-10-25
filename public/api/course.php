<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(ROOT_DIR . "/src/modules/security/security.php");
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
require_once(ROOT_DIR . "/src/modules/content/content_functions.php");
require_once(ROOT_DIR . "/src/modules/mqtt/mqtt_functions.php");


if (isset($_POST["leave"], $_POST["course-id"])) {
    $userID = $_SESSION["uid"];
    if (checkCoursePermission($db, $userID, $_POST["course-id"]) && !checkCourseAdminPermission($db, $userID, $_POST["course-id"])) {
        removeMemberFromCourse($db, $_POST["course-id"], $userID);
        header("Location:{$_SESSION["redirect_url"]}");
        exit();
    }
} elseif (isset($_POST["member-remove"], $_POST["member-id"], $_POST["course-id"])) {
    if (checkCourseAdminPermission($db, $_SESSION["uid"], $_POST["course-id"])) {
        $userID = $_POST["member-id"];
        if (checkCoursePermission($db, $userID, $_POST["course-id"])) {
            removeMemberFromCourse($db, $_POST["course-id"], $userID);
            header("Location:{$_SESSION["redirect_url"]}");
            exit();
        }
    }
} elseif (isset($_POST["member-transfer"], $_POST["member-id"], $_POST["course-id"])) {
    if (checkCourseAdminPermission($db, $_SESSION["uid"], $_POST["course-id"])) {
        $userID = $_POST["member-id"];
        if (checkCoursePermission($db, $userID, $_POST["course-id"])) {
            transferCourse($db, $_POST["course-id"], $userID);
            header("Location:" . BASE_URL . "/index.php");
            exit();
        }
    }
} elseif (isset($_POST["member-add"], $_POST["username"], $_POST["course-id"])) {
    if (checkCourseAdminPermission($db, $_SESSION["uid"], $_POST["course-id"])) {
        if (usernameExists($db, $_POST["username"])) {
            $userID = getUserID($db, $_POST["username"]);
            if (!checkCoursePermission($db, $userID, $_POST["course-id"])) {
                addUserToCourse($db, $_POST["course-id"], $userID);
                header("Location:{$_SESSION["redirect_url"]}");
                exit();
            }
        }
    }
} elseif (isset($_GET["count"], $_GET["course"])) {
    echo pingCourseMember($_GET["course"], WATCHDOG_RESPONSE_TIME);
    exit();
} elseif (isset($_POST["update"], $_POST["course-id"], $_POST["name"], $_POST["description"], $_POST["abbreviation"], $_POST["color"])) {
    if (checkCourseAdminPermission($db, $_SESSION["uid"], $_POST["course-id"])) {
        updateCourse($db, $_POST["course-id"], $_POST["name"], $_POST["description"], substr($_POST["abbreviation"], 0, 5), $_POST["color"]);
        header("Location:{$_SESSION["redirect_url"]}");
        exit();
    }
} elseif (isset($_POST["enforce"], $_POST["content-id"])) {
    $contentID = $_POST["content-id"];
    if (checkContentPermission($db, $_SESSION["uid"], $contentID)) {
        $courseID = enforceContent($db, $contentID);
        header("Location:{$_SESSION["redirect_url"]}");
        exit();
    }
} elseif (isset($_POST["stop-enforce"], $_POST["course-id"])) {
    if (checkCoursePermission($db, $_SESSION["uid"], $_POST["course-id"])) {
        stopEnforcingContent($db, $_POST["course-id"]);
        header("Location:{$_SESSION["redirect_url"]}");
        exit();
    }
} elseif (isset($_POST["available"], $_POST["scope"], $_POST["scope-id"], $_POST["course-id"])) {
    switch ($_POST["scope"]) {
        case "course":
            if (checkCoursePermission($db, $_SESSION["uid"], $_POST["scope-id"])) {
                makeCourseAvailable($db, $_POST["scope-id"], $_POST["available"] == "1");
                header("Location:{$_SESSION["redirect_url"]}");
                exit();
            }
            break;
        case "folder":
            if ($_POST["scope-id"] == "null") {
                if (checkCoursePermission($db, $_SESSION["uid"], $_POST["course-id"])) {
                    makeCourseBaseFolderAvailable($db, $_POST["course-id"], $_POST["available"] == "1");
                    header("Location:{$_SESSION["redirect_url"]}");
                    exit();
                }
            } elseif (checkCourseFolderPermission($db, $_SESSION["uid"], $_POST["scope-id"])) {
                makeCourseFolderAvailable($db, $_POST["scope-id"], $_POST["available"] == "1");
                header("Location:{$_SESSION["redirect_url"]}");
                exit();
            }
            break;
        case "content":
            if (checkContentPermission($db, $_SESSION["uid"], $_POST["scope-id"])) {
                makeContentAvailable($db, $_POST["scope-id"], $_POST["available"] == "1");
                header("Location:{$_SESSION["redirect_url"]}");
                exit();
            }
            break;
    }
} elseif (isset($_POST["create"], $_SESSION["institution"], $_POST["course-name"], $_POST["course-description"])) {
    $courseName = $_POST["course-name"];
    $courseDescription = $_POST["course-description"];
    $courseID = createCourse($db, $_SESSION["institution"], $courseName, $courseDescription, $_SESSION["uid"]);
    header("Location:" . BASE_URL . "/courses.php?view=$courseID");
    exit();
} elseif (isset($_POST["folder"], $_POST["course-id"], $_POST["course-folder-name"])) {
    $courseID = $_POST["course-id"];
    $folderName = $_POST["course-folder-name"];
    if (checkCourseAdminPermission($db, $_SESSION["uid"], $courseID)) {
        createCourseFolder($db, $courseID, $folderName);
        header("Location:{$_SESSION["redirect_url"]}");
        exit();
    }
}
header("Location:" . BASE_URL . "/index.php?error=badrequest");
exit();