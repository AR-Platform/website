<?php
require_once(__DIR__ . "/../../config/config.php");
require_once (ROOT_DIR . "/src/modules/user/user_functions.php");



if (isset($_GET["username"])) {
    echo usernameExists($db, $_GET["username"]);
} elseif (isset($_GET["email"])) {
    echo emailExists($db, $_GET["email"]);
}