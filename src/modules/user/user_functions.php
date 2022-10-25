<?php
require_once(__DIR__ . "/../security/security_functions.php");


/**
 * Checks if the specified user is a super-admin on the website.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @return bool True if the user is a super-admin, false otherwise.
 */
function isAdmin(PDO $db, int $userID): bool
{
    $sql = 'SELECT 1 FROM "admin" WHERE user_id=:userID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
    return sizeof($stmt->fetchAll()) > 0;
}

/**
 * Checks if the user credentials are already in the database and therefore cannot be reused.
 *
 * @param PDO $db The PDO database instance.
 * @param string $username The user specified username.
 * @param string $email The user specified email.
 * @return bool True if the credentials are not yet used by any user.
 */
function validateNewCredentials(PDO $db, string $username, string $email): bool
{
    return !(usernameExists($db, $username) || emailExists($db, $email));
}

/**
 * Adds a user entry to the database with the specified information.
 *
 * The password will be encrypted before stored in the database.
 * No further checks regarding the validity of the provided information take place.
 *
 * @param PDO $db The PDO database instance.
 * @param string $username The user specified unique username.
 * @param string $email The user specified unique email address.
 * @param string $password The user provided password.
 * @return void No return value.
 */
function addUser(PDO $db, string $username, string $email, string $password)
{
    $sql = 'INSERT INTO "user" (email, username, password) VALUES(:email, :username, :password)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', encryptPassword($password));
    $stmt->execute();
}

/**
 * Adds the given userID to the admin table.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @return void No return value.
 */
function addAdmin(PDO $db, int $userID)
{
    $sql = 'INSERT INTO "admin" (user_id) VALUES(:user_id)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $userID);
    $stmt->execute();
}

/**
 * Validates the user provided login data.
 *
 * @param PDO $db The PDO database instance.
 * @param string $credential The username or email of the user.
 * @param string $password The user provide password.
 * @return bool True if the login data is valid, false otherwise.
 */
function validateLoginData(PDO $db, string $credential, string $password): bool
{
    $sql = 'SELECT DISTINCT password FROM "user" WHERE LOWER(email)=:credential OR LOWER(username)=:credential';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':credential', strtolower($credential));
    $stmt->execute();
    $dbPassword = $stmt->fetchColumn();
    return validatePassword($password, $dbPassword);
}

/**
 * Invokes the login process for the specified user and starts a new secure session.
 *
 * Ensure the user provided details correctly authenticate the user before calling.
 *
 * @param PDO $db The PDO database instance.
 * @param string $credential The username or email of the user.
 * @return void No return value.
 */
function loginUser(PDO $db, string $credential)
{
    $sql = 'SELECT DISTINCT id FROM "user" WHERE LOWER(email)=:credential OR LOWER(username)=:credential';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':credential', strtolower($credential));
    $stmt->execute();
    $id = $stmt->fetchColumn();
    configureSecureSession($db, $id);
}

/**
 * Checks if the specified username exists within the user-database.
 *
 * The check is not case sensitiv.
 *
 * @param PDO $db The PDO database instance.
 * @param string $username The username to check.
 * @return bool True if the username already exists, false otherwise.
 */
function usernameExists(PDO $db, string $username): bool
{
    $sql = 'SELECT 1 FROM "user" WHERE LOWER(username)=:username';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':username', strtolower($username));
    $stmt->execute();
    return sizeof($stmt->fetchAll()) > 0;
}

/**
 * Retrieves the ID of the user with the given username.
 *
 * Additional checks to ensure the username exists in the database should be done beforehand.
 *
 * @param PDO $db The PDO database instance.
 * @param string $username The username of the user.
 * @return int The ID of the user with the specified username.
 */
function getUserID(PDO $db, string $username): int
{
    $sql = 'SELECT id FROM "user" WHERE LOWER(username)=:username';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':username', strtolower($username));
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_NUM)[0];
}

/**
 * Verifies if the specified email exists within the database.
 *
 * @param PDO $db The PDO database instance.
 * @param string $email The email to check.
 * @return bool True if the email already exists in the user database.
 */
function emailExists(PDO $db, string $email): bool
{
    $sql = 'SELECT 1 FROM "user" WHERE LOWER(email)=:email';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', strtolower($email));
    $stmt->execute();
    return sizeof($stmt->fetchAll()) > 0;
}

/**
 * Validates the password against the stored encrypted hash.
 *
 * @param string $password The user-provided password.
 * @param string $passwordHashed The hashed password string from the database.
 * @return bool True if the passwords match, false otherwise.
 */
function validatePassword(string $password, string $passwordHashed): bool
{
    return password_verify($password, $passwordHashed);
}

/**
 * Encrypts the given password string with Argon2ID.
 *
 * @param string $password The unencrypted password string.
 * @return string The encrypted password string.
 */
function encryptPassword(string $password): string
{
    return password_hash($password, PASSWORD_ARGON2ID);
}