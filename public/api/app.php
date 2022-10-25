<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
require_once(ROOT_DIR . "/src/modules/content/content_functions.php");

switch (key($_GET)) {
    case "mqtt":
        outputJson(array(array("server" => MQTT_SERVER_EXTERN, "topic" => MQTT_TOPIC)));
        exit();
    case "institutions":
        outputJson(getInstitutions($db));
        exit();
    case "institution":
        if (is_numeric($_GET["institution"])) {
            outputJson(getCourses($db, $_GET["institution"]));
        }
        exit();
    case "course":
        if (is_numeric($_GET["course"])) {
            outputJson(getAvailableCourseContent($db, $_GET["course"]));
        }
        exit();
    case "content":
        if (is_numeric($_GET["content"])) {
            if (contentIsAvailable($db, $_GET["content"])) {
                $path = getContentFilePath($_GET["content"]);
                addDownloadEntry($db, $_GET["content"]);
                downloadContent($path);
            }
        }
        exit();
    default:
        header("Location:" . BASE_URL . "/index.php?error=badrequest");
        exit();
}

/**
 * Converts the input data into a json representation with a toplevel keyword and outputs it.
 *
 * @param array $data The data array to convert.
 * @return void The result of the conversion will be printed.
 */
function outputJson(array $data)
{
    $json = json_encode($data);
    echo "{\"data\": $json}";
}