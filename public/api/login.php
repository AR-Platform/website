<?php

$allowGuests = true;

require_once(__DIR__ . "/../../config/config.php");
require_once(ROOT_DIR . "/src/modules/user/user_functions.php");
require_once(ROOT_DIR . "/src/modules/security/security.php");
require_once(ROOT_DIR . "/src/modules/security/security_functions.php");

if (isset($_POST["login"])) {
    $credential = $_POST["credential"];
    $password = $_POST["password"];
    if (validateLoginData($db, $credential, $password)) {
        loginUser($db, $credential);
        header("Location:" . BASE_URL . "/index.php");
        exit();
    }
} elseif (isset($_POST["register"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordConf = $_POST["password-confirmation"];
    if (validateNewCredentials($db, $username, $email)) {
        addUser($db, $username, $email, $password);
        loginUser($db, $username);
        header("Location:" . BASE_URL . "/index.php");
        exit();
    }
} elseif (isset($_GET["logout"])) {
    destroySession();
}
header("Location:" . BASE_URL . "/index.php?error=badrequest");
exit();