<?php

/**
 * Creates a database entry for the institution.
 *
 * No data validation takes place.
 *
 * @param PDO $db The PDO database instance.
 * @param string $name
 * @param string $email
 * @param int $userID The ID of the user.
 * @return void
 */
function createInstitution(PDO $db, string $name, string $email, int $userID)
{
    $sql = 'INSERT INTO institution (name, email) VALUES(:institutionName, :institutionEmail)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionName', $name);
    $stmt->bindValue(':institutionEmail', $email);
    $stmt->execute();

    $institutionID = $db->lastInsertId();

    $sql = 'INSERT INTO institution_member (institution_id, user_id, admin) VALUES(:institutionID, :userID, true)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
}

/**
 * Checks if the specified user is part of an institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @return bool True if the user is at least member of an institution.
 */
function isInstitutionMember(PDO $db, int $userID): bool
{
    $sql = 'SELECT 1 FROM institution_member WHERE user_id=:userID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
    return count($stmt->fetchAll()) > 0;
}

/**
 * Checks if the user is part of the specified institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @param int $institutionID The ID of the institution.
 * @return bool True if the user is a member of the specified institution.
 */
function isMemberOfInstitution(PDO $db, int $userID, int $institutionID): bool
{
    $sql = 'SELECT 1 FROM institution_member WHERE user_id=:userID AND institution_id=:institutionID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->execute();
    return count($stmt->fetchAll()) > 0;
}

/**
 * Checks if the user is an admin of an institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @return bool True if the user is an admin of an institution.
 */
function isInstitutionAdmin(PDO $db, int $userID): bool
{
    $sql = 'SELECT admin FROM institution_member WHERE user_id=:userID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_COLUMN) == 1;
}

/**
 * Fetches the ID of the user's institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @return int|bool The ID if the user institution or false if the user is not part of an institution.
 */
function getInstitutionID(PDO $db, int $userID): int|bool
{
    $sql = 'SELECT institution_id FROM institution_member WHERE user_id=:userID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

/**
 * Fetches all institution data based on the institution ID.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The ID of the institution.
 * @return array All data related to the institution.
 */
function getInstitution(PDO $db, int $institutionID): array
{
    $sql = 'SELECT * FROM institution WHERE id=:institutionID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->execute();
    return $stmt->fetch();
}

/**
 * Fetches a list of all members of the specified institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The ID of the institution.
 * @return array An array containing the ID's and usernames of all members.
 */
function getInstitutionMembers(PDO $db, int $institutionID): array
{
    $sql = 'SELECT u.id, u.username FROM institution_member AS im JOIN "user" AS u ON im.user_id=u.id WHERE im.institution_id=:institutionID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Fetches a list of all institutions.
 *
 * @param PDO $db The PDO database instance.
 * @return array An array containing all institution ID's and names.
 */
function getInstitutions(PDO $db): array
{
    $sql = 'SELECT id, name FROM institution WHERE verified IS NOT NULL AND verified';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches a list of all non verified institutions.
 *
 * @param PDO $db The PDO database instance.
 * @return array An array containing all non verified institution ID's and names.
 */
function getNotVerifiedInstitutions(PDO $db): array
{
    $sql = 'SELECT * FROM institution WHERE verified IS NULL';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Changes the verification status of the specified institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The ID of the institution.
 * @param bool $verify True if the institution should be verified, false otherwise.
 * @return void No return value.
 */
function verifyInstitution(PDO $db, int $institutionID, bool $verify)
{
    $sql = 'UPDATE institution SET verified=:verified WHERE id=:institutionID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':verified', (int)$verify);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->execute();
}

/**
 * Adds a new user to the institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The ID of the institution.
 * @param int $userID The ID of the user.
 * @return void No return value.
 */
function addMemberToInstitution(PDO $db, int $institutionID, int $userID)
{
    $sql = 'INSERT INTO institution_member (institution_id, user_id) VALUES(:institutionID, :userID)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
}

/**
 * Removes the user with the given ID from the specified institution.
 *
 * Transfers all courses from the removed member to the institution admin.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The ID of the institution.
 * @param int $userID The ID of the user.
 * @return void No return value.
 */
function removeMemberFromInstitution(PDO $db, int $institutionID, int $userID)
{
    //Remove user from institution member list
    $sql = 'DELETE FROM institution_member AS im WHERE im.institution_id=? AND im.user_id=?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$institutionID, $userID]);

    $institutionAdminID = getInstitutionAdminID($db, $institutionID);

    //Add the institution admin as course admin to all courses that belong to the removed member.
    $sql = 'UPDATE course_member SET user_id=:institutionAdminID WHERE user_id=:userID AND admin=true AND course_id NOT IN (SELECT course_id FROM course_member WHERE user_id=:institutionAdminID)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionAdminID', $institutionAdminID);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();

    $sql = 'UPDATE course_member SET admin=true WHERE user_id=:institutionAdminID AND course_id IN (SELECT course_id FROM course_member WHERE user_id=:userID AND admin=true)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionAdminID', $institutionAdminID);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();

    //Remove user from all courses
    $sql = 'DELETE FROM course_member WHERE user_id=?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$userID]);
}

/**
 * Fetches the user ID of the admin from the specified institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The ID of the institution.
 * @return int The ID of the user account that is the admin of the institution.
 */
function getInstitutionAdminID(PDO $db, int $institutionID): int
{
    $sql = 'SELECT user_id FROM institution_member WHERE institution_id=:institutionID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

/**
 * Checks if the specified institution is verified.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The ID of the institution.
 * @return bool True if the institution is verified, false otherwise.
 */
function isInstitutionVerified(PDO $db, int $institutionID): bool
{
    $sql = 'SELECT verified FROM institution WHERE id=:institutionID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_NUM)[0] == 1;
}