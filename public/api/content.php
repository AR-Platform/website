<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(ROOT_DIR . "/src/modules/security/security.php");
require_once(ROOT_DIR . "/src/modules/content/content_functions.php");

if (isset($_POST["available"], $_POST["content-id"])) {
    $contentID = $_POST["content-id"];
    if (checkContentPermission($db, $_SESSION["uid"], $contentID)) {
        makeContentAvailable($db, $contentID, $_POST["available"] == "1");
        header("Location:{$_SESSION["redirect_url"]}");
        exit();
    }
} elseif (isset($_POST["delete"], $_POST["content-id"])) {
    $contentID = $_POST["content-id"];
    if (checkContentAdminPermission($db, $_SESSION["uid"], $contentID)) {
        deleteContent($db, $contentID);
        header("Location:{$_SESSION["redirect_url"]}");
        exit();
    }
}
header("Location:" . BASE_URL . "/index.php?error=badrequest");
exit();