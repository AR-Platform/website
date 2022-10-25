<?php
require_once(__DIR__ . "/../../../config/config.php");
require_once(ROOT_DIR . "/src/modules/security/security_functions.php");

startSecureSession();

$auth = $_SESSION["auth"] ?? false;
$isAdmin = $_SESSION["admin"] ?? false;

if ($auth) {
    validateSecureSession();
    if (!empty($_POST)) {
        //An API request has been made and the CSRF-Token has to be verified.
        if (!isset($_POST["csrf"]) || $_POST["csrf"] != $_SESSION["csrf"]) {
            destroySession();
        } else {
            createNewCsrfToken();
        }
    }
} elseif (!(isset($allowGuests) && $allowGuests)) {
    header("Location:" . BASE_URL . "/index.php?error=noperm");
    exit();
}
