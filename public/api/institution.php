<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(ROOT_DIR . "/src/modules/security/security.php");
require_once(ROOT_DIR . "/src/modules/user/user_functions.php");
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");

if (isset($_POST["member-remove"], $_POST["member-id"])) {
    if (($_SESSION["institution-admin"] ?? false)) {
        $userID = $_POST["member-id"];
        if (isMemberOfInstitution($db, $userID, $_SESSION["institution"])) {
            removeMemberFromInstitution($db, $_SESSION["institution"], $userID);
            header("Location:{$_SESSION["redirect_url"]}");
            exit();
        }
    }
} elseif (isset($_POST["member-add"], $_POST["username"])) {
    if (($_SESSION["institution-admin"] ?? false)) {
        if (usernameExists($db, $_POST["username"])) {
            $userID = getUserID($db, $_POST["username"]);
            if (!isInstitutionMember($db, $userID)) {
                addMemberToInstitution($db, $_SESSION["institution"], $userID);
                header("Location:{$_SESSION["redirect_url"]}");
                exit();
            }
        }
    }
} elseif (isset($_POST["apply"], $_POST["institution-name"], $_POST["institution-email"])) {
    if ($_SESSION["institution"]) {
        header("Location:" . BASE_URL . "/index.php?error=noperm");
        exit();
    }
    createInstitution($db, $_POST["institution-name"], $_POST["institution-email"], $_SESSION["uid"]);
    header("Location:{$_SESSION["redirect_url"]}");
    exit();
} elseif ($isAdmin && isset($_POST["verify"], $_POST["institution-id"])) {
    verifyInstitution($db, $_POST["institution-id"], true);
    header("Location:{$_SESSION["redirect_url"]}");
    exit();
} elseif ($isAdmin && isset($_POST["reject"], $_POST["institution-id"])) {
    verifyInstitution($db, $_POST["institution-id"], false);
    header("Location:{$_SESSION["redirect_url"]}");
    exit();
}
header("Location:" . BASE_URL . "/index.php?error=badrequest");
exit();