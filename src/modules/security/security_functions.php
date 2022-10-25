<?php

use JetBrains\PhpStorm\NoReturn;

require_once(__DIR__ . "/../../../config/config.php");
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");
require_once(ROOT_DIR . "/src/modules/user/user_functions.php");

/**
 * Starts and configures a new secure session.
 *
 * @return void No return value.
 */
function startSecureSession()
{
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $cookieParams["lifetime"],
        'path' => $cookieParams["path"],
        'domain' => $cookieParams["domain"],
        'secure' => HTTPS_ONLY,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    session_name("ARES");
    session_start();
    session_regenerate_id();
}

/**
 * Specifies all session variables for the secure session and creates a new CSRF-Token.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @return void No return value.
 */
function configureSecureSession(PDO $db, int $userID)
{
    $institutionID = getInstitutionID($db, $userID);
    $_SESSION["uid"] = $userID;
    $_SESSION["auth"] = true;
    $_SESSION["admin"] = isAdmin($db, $userID);
    $_SESSION["institution"] = $institutionID;
    $_SESSION["institution-admin"] = isInstitutionAdmin($db, $userID);
    $_SESSION["institution-verified"] = $institutionID && isInstitutionVerified($db, $institutionID);
    $_SESSION["fingerprint"] = calculateSessionFingerprint();
    createNewCsrfToken();
}

/**
 * Validates the current secure session to prevent session hijacking.
 *
 * If the session is considered invalid, it is destroyed and the user is therefore logged out.
 *
 * @return void No return value.
 */
function validateSecureSession()
{
    if ($_SESSION["fingerprint"] != calculateSessionFingerprint()) {
        destroySession();
    }
}

/**
 * Creates and stores a new CSRF-Token to prevent CSRF attacks.
 *
 * For security reasons the token should be renewed after each use.
 *
 * @return void No return value.
 */
function createNewCsrfToken()
{
    $_SESSION["csrf"] = uniqid("", true);
}

/**
 * Calculates the session fingerprint.
 *
 * The session fingerprint is used to prevent session hijacking.
 * It consists out of the user-agent, a secret only known to the webserver and user IP address.
 *
 * @return string The calculated session fingerprint.
 */
function calculateSessionFingerprint(): string
{
    return sha1($_SERVER["HTTP_USER_AGENT"] . SECURITY_SECRET . $_SERVER["REMOTE_ADDR"]);
}

/**
 * Destroys the current secure session including all session variables.
 *
 * @return void No return value.
 */
#[NoReturn] function destroySession()
{
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location:" . BASE_URL . "/index.php");
    exit();
}
