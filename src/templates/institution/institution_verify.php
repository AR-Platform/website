<?php
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");

$institutions = getNotVerifiedInstitutions($db);

foreach ($institutions as $institution) {
    echo "
    <form action='api/institution.php' method='post'>
    <h3>" . htmlspecialchars($institution["name"]) . "</h3>
    <p>" . htmlspecialchars($institution["email"]) . "</p>
    <input type='hidden' name='institution-id' value='{$institution["id"]}'>
    <input type='hidden' name='csrf' value='{$_SESSION["csrf"]}'>
    <button type='submit' name='reject' class='two-inputs-row critical'>" . LANG_REJECT . "</button>
    <button type='submit' name='verify' class='two-inputs-row'>" . LANG_VERIFY . "</button>
    ";
}