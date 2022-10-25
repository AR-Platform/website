<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(ROOT_DIR . "/src/modules/content/shell_functions.php");
require_once(ROOT_DIR . "/src/modules/content/upload_functions.php");
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
require_once(ROOT_DIR . "/src/modules/security/security.php");

if (isset($_POST["upload"], $_POST["upload-name"], $_POST["upload-course"], $_FILES["upload-file"])) {
    $file = $_FILES['upload-file'];
    $contentName = $_POST['upload-name'];
    $courseID = $_POST["upload-course"];
    $userID = $_SESSION["uid"];
    $folderID = $_POST["upload-folder"] ?? null;
    $folderID = $folderID == 0 ? null : $folderID;
    if (!checkCourseAdminPermission($db, $userID, $courseID)) {
        header("Location:" . BASE_URL . "/index.php?error=noperm");
        exit();
    }
    if (validateUpload($file) !== true) {
        header("Location:" . BASE_URL . "/index.php?error=fileerror");
        exit();
    }

    // Keep alive from here on
    keepAlive("convert.php");

    $fileExt = getFileExtension($file['name']);
    $destinationFile = tempnam(UPLOAD_DIR, "aer");
    $ids = createUploadContentEntry($db, $userID, $courseID, $folderID, $contentName, $destinationFile, $fileExt);
    $uploadID = $ids[0];
    $contentID = $ids[1];
    move_uploaded_file($file['tmp_name'], $destinationFile);
    if ($fileExt == "ares") {
        markContentAsConverted($db, $contentID);
        copy($destinationFile, DOWNLOAD_DIR . "/$contentID.ares");
    } else {
        $optionsArray = analyzeFile($destinationFile, $fileExt);
        $options = implode(";", $optionsArray);
        addUploadEntryOptions($db, $uploadID, $options);
    }
} elseif (isset($_POST["convert"], $_POST["id"], $_POST["course"])) {
    $contentID = $_POST["id"];
    if (ownsUploadFileID($db, $_SESSION["uid"], $contentID)) {

        // Keep alive from here on
        keepAlive("courses.php?view={$_POST["course"]}");

        $optionsString = array();
        $counter = 0;
        while (isset($_POST["option$counter"])) {
            $optionsString[] = $_POST["option$counter"];
            $counter++;
        }
        $optionsString = implode(";", $optionsString);
        $fileArray = getUploadFileByContentID($db, $contentID);
        markContentAsConverted($db, $contentID);
        $outputFile = DOWNLOAD_DIR . "/$contentID";
        convertFile($fileArray["file_name"], $fileArray["file_ext"], $outputFile, $optionsString);
    } else {
        header("Location:" . BASE_URL . "/index.php?error=noperm");
    }

} else {
    header("Location:" . BASE_URL . "/index.php?error=badrequest");
}

/**
 * Redirects the user back to the specified location while keeping the script running in the background.
 *
 * @param string $location The optional location. Default is the landing page.
 * @return void No return value.
 */
function keepAlive(string $location = "index.php")
{
    $location = $location ?? $_SESSION["redirect_url"];
    ignore_user_abort(true);
    header("Location:" . BASE_URL . "/$location");
    set_time_limit(300);
}