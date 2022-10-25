<?php
require_once(__DIR__ . "/../config/config.php");
require_once(ROOT_DIR . "/src/modules/user/user_functions.php");

initAdmin($db);

/**
 * Creates the initial admin based on environment variables.
 *
 * @param PDO $db The PDO database instance.
 * @return void No return value
 */
function initAdmin(PDO $db)
{
    $username = $_ENV["AER_ADMIN_NAME"];
    if(!usernameExists($db, $username))
    {
        addUser($db, $username, "", $_ENV["AER_ADMIN_PASSWORD"]);
        addAdmin($db, getUserID($db, $username));
    }
}
