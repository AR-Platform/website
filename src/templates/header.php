<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(ROOT_DIR . "/src/modules/security/security.php");
$_SESSION["redirect_url"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]}";

if (isset($_SESSION["lang"])) {
    require_once(LANGUAGES[$_SESSION["lang"]]);
} else {
    $userPrefLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if (in_array($userPrefLang, array_keys(LANGUAGES))) {
        require_once(LANGUAGES[$userPrefLang]);
    } else {
        require_once(LANGUAGES[LANGUAGE_DEFAULT]);
    }
}

?>
<!DOCTYPE html>
<html lang="de" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>AER - <?= $title ?? "" ?></title>
    <base href="<?= BASE_URL ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dark_design.css">
    <link rel="stylesheet" href="css/light_design.css">
    <link rel="stylesheet" href="css/base_design.css">
    <link rel="stylesheet" href="css/form_design.css">
    <link rel="stylesheet" href="css/dashboard_design.css">
    <link rel="stylesheet" href="css/modal_design.css">
    <link rel="stylesheet" href="css/collapsible.css">
    <link rel="stylesheet" href="css/responsive_design.css">
    <script src="js/collapsible.js" defer></script>
</head>

<body>
<header>
    <a href="index.php"><?= LANG_HOME ?></a>
    <nav>
        <ul>
            <?= $auth && $_SESSION["institution"] ? "<li><a href='courses.php'>" . LANG_COURSES . "</a></li>" : ""; ?>
            <?= $auth ? "<li><a href='institution.php'>" . LANG_INSTITUTION . "</a></li>" : ""; ?>
            <?= $isAdmin ? "<li><a href='admin.php'>" . LANG_ADMIN . "</a></li>" : ""; ?>
            <?= $auth ? "<li><a href='api/login.php?logout'>" . LANG_LOGOUT . "</a></li>" : ""; ?>
        </ul>
    </nav>
</header>