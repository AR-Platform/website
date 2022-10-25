<?php
require_once(__DIR__ . "/../../../config/config.php");
require_once(ROOT_DIR . "/src/modules/mqtt/mqtt_functions.php");

/**
 * Creates a course based on the given information.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The institution ID.
 * @param string $courseName The name of the course.
 * @param string $courseDescription A text describing the course content.
 * @param int $userID The user ID.
 * @return int The course ID.
 */
function createCourse(PDO $db, int $institutionID, string $courseName, string $courseDescription, int $userID): int
{
    $sql = 'INSERT INTO course (institution_id, name, description) VALUES(:institutionID, :courseName, :courseDescription)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->bindValue(':courseName', $courseName);
    $stmt->bindValue(':courseDescription', $courseDescription);
    $stmt->execute();

    $courseID = $db->lastInsertId();

    $sql = 'INSERT INTO course_member (course_id, user_id, admin) VALUES(:courseID, :userID, true)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();

    return $courseID;
}

/**
 * Creates a folder within a course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param string $folderName The name of the folder.
 * @return void No return value.
 */
function createCourseFolder(PDO $db, int $courseID, string $folderName)
{
    $sql = 'INSERT INTO course_folder (course_id, name) VALUES(:courseID, :folderName)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->bindValue(':folderName', $folderName);
    $stmt->execute();
}

/**
 * Fetches an array of all folders within a given course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @return array An array containing all folder ID's and names.
 */
function getCourseFolders(PDO $db, int $courseID): array
{
    $sql = 'SELECT f.id, f.name FROM course_folder AS f WHERE f.course_id=:courseID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches all courses where the given user is at least a member.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The user ID.
 * @return array An array containing all course ID's and a bool defining if the user is the course admin.
 */
function getCourseIDs(PDO $db, int $userID): array
{
    $sql = 'SELECT c.id, ca.admin FROM course AS c, course_member AS ca WHERE ca.user_id = :userID AND c.id = ca.course_id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches a list of all courses the given user is a member of.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The user ID.
 * @return array An array containing all course ID's and names.
 */
function getCourseNameIDs(PDO $db, int $userID): array
{
    $sql = 'SELECT c.id, c.name FROM course AS c, course_member AS ca WHERE ca.user_id = :userID AND c.id = ca.course_id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches all information from a course based on its ID.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @return array An array containing all courses with all details.
 */
function getCourse(PDO $db, int $courseID): array
{
    $sql = 'SELECT DISTINCT * FROM course WHERE id = :courseID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Fetches all courses from the defined institution.
 *
 * @param PDO $db The PDO database instance.
 * @param int $institutionID The institution ID.
 * @return array An array containing the ID's, names, descriptions, abbreviations and colors of the courses.
 */
function getCourses(PDO $db, int $institutionID): array
{
    $sql = 'SELECT id, name, description, abbreviation, color FROM course WHERE institution_id = :institutionID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':institutionID', $institutionID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches all contents from a given course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @return array An array containing the ID's and names of all contents.
 */
function getCourseContent(PDO $db, int $courseID): array
{
    $sql = 'SELECT c.id, c.name FROM content AS c JOIN location AS l ON c.location_id = l.id WHERE l.course_id = :courseID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches all available contents from a given course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @return array An array containing the ID's and names of all contents.
 */
function getAvailableCourseContent(PDO $db, int $courseID): array
{
    $sql = 'SELECT c.id, c.name FROM content AS c LEFT JOIN location AS l ON c.location_id = l.id WHERE l.course_id = :courseID AND c.available AND c.converted';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches all base level contents from a given course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @return array An array containing all base level contents data.
 */
function getCourseLevelContent(PDO $db, int $courseID): array
{
    $sql = 'SELECT c.* FROM content AS c JOIN location AS l ON c.location_id = l.id WHERE l.course_id = :courseID AND l.folder_id IS NULL';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetches all content from within a course folder.
 *
 * @param PDO $db The PDO database instance.
 * @param int $folderID The folder ID.
 * @return array An array containing all content data.
 */
function getFolderContent(PDO $db, int $folderID): array
{
    $sql = 'SELECT c.* FROM content AS c JOIN location AS l ON c.location_id = l.id WHERE l.folder_id = :folderID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':folderID', $folderID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Checks if the given user has at least member permissions for the specified course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The user ID.
 * @param int $courseID The course ID.
 * @return bool True if the user has the required permissions, false otherwise.
 */
function checkCoursePermission(PDO $db, int $userID, int $courseID): bool
{
    $sql = 'SELECT 1 FROM course_member WHERE user_id = :userID AND course_id = :courseID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    return sizeof($stmt->fetchAll()) > 0;
}

/**
 * Checks if the given user has at admin permissions on the specified course folder.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The user ID.
 * @param int $courseID The course ID.
 * @return bool True if the user has the required permissions, false otherwise.
 */
function checkCourseAdminPermission(PDO $db, int $userID, int $courseID): bool
{
    $sql = 'SELECT 1 FROM course_member WHERE user_id = :userID AND course_id = :courseID AND admin';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    return sizeof($stmt->fetchAll()) > 0;
}

/**
 * Checks if the given user has at least member permissions on the specified course folder.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The user ID.
 * @param int $folderID The folder ID.
 * @return bool True if the user has the required permissions, false otherwise.
 */
function checkCourseFolderPermission(PDO $db, int $userID, int $folderID): bool
{
    $sql = 'SELECT 1 FROM course_member AS ca JOIN course_folder AS cf ON ca.course_id = cf.course_id WHERE ca.user_id = :userID AND cf.id = :folderID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->bindValue(':folderID', $folderID);
    $stmt->execute();
    return sizeof($stmt->fetchAll()) > 0;
}

/**
 * Makes all converted content available from the course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param bool $available
 * @return void No return value.
 */
function makeCourseAvailable(PDO $db, int $courseID, bool $available)
{
    $sql = 'UPDATE content SET available=:available WHERE id IN (SELECT c.id FROM content AS c JOIN location AS l ON c.location_id = l.id WHERE l.course_id = :courseID AND c.converted)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':available', (int)$available);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
}

/**
 * Makes all converted folder content available from the course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $folderID The folder ID.
 * @param bool $available
 * @return void No return value.
 */
function makeCourseFolderAvailable(PDO $db, int $folderID, bool $available)
{
    $sql = 'UPDATE content SET available=:available WHERE id IN (SELECT c.id FROM content AS c JOIN location AS l ON c.location_id = l.id WHERE l.folder_id = :folderID AND c.converted)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':available', (int)$available);
    $stmt->bindValue(':folderID', $folderID);
    $stmt->execute();
}

/**
 * Makes all converted base folder content available from the specified course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param bool $available True will enable all contents of the base folder.
 * @return void No return value.
 */
function makeCourseBaseFolderAvailable(PDO $db, int $courseID, bool $available)
{
    $sql = 'UPDATE content SET available=:available WHERE id IN (SELECT c.id FROM content AS c JOIN location AS l ON c.location_id = l.id WHERE l.course_id = :courseID AND l.folder_id IS NULL AND c.converted)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':available', (int)$available);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
}

/**
 * Begins enforcing the specified content for the parent course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $contentID The content ID.
 * @return int The course ID of the enforced content.
 */
function enforceContent(PDO $db, int $contentID): int
{
    $sql = 'SELECT DISTINCT l.course_id FROM content AS c JOIN location AS l ON c.location_id = l.id WHERE c.id=:contentID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->execute();
    $courseID = $stmt->fetch(PDO::FETCH_NUM)[0];

    $sql = 'UPDATE course SET enforce_id=:contentID WHERE course.id = :courseID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();

    sendEnforceMessage($courseID, $contentID);
    return $courseID;
}

/**
 * Stops enforcing content within a certain course.
 *
 * Updates the database and sends the MQTT message.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @return void No return value.
 */
function stopEnforcingContent(PDO $db, int $courseID)
{
    $sql = 'UPDATE course SET enforce_id=NULL WHERE course.id = :courseID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();

    sendEnforceMessage($courseID, null);
}

/**
 * Updates the course information stored in the database.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param string $courseName The name of the course.
 * @param string $courseDescription A text describing the course content.
 * @param string $courseAbbreviation An abbreviation of the course name.
 * @param string $courseColor The desired course accent color.
 * @return void No return value.
 */
function updateCourse(PDO $db, int $courseID, string $courseName, string $courseDescription, string $courseAbbreviation, string $courseColor)
{
    $sql = 'UPDATE course SET name=?, description=?, abbreviation=?, color=?  WHERE course.id=?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$courseName, $courseDescription, $courseAbbreviation, $courseColor, $courseID]);
}

/**
 * Adds the user to the specified course.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param int $userID The user ID.
 * @return void No return value.
 */
function addUserToCourse(PDO $db, int $courseID, int $userID)
{
    $sql = 'INSERT INTO course_member (course_id, user_id) VALUES(:courseID, :userID)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
}

/**
 * Fetches a list of all course members.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @return array An array containing the ID's and usernames of all course members.
 */
function getCourseMembers(PDO $db, int $courseID): array
{
    $sql = 'SELECT u.id, u.username FROM course_member AS cm JOIN "user" AS u ON cm.user_id = u.id WHERE cm.course_id=:courseID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Transfers the given course to the specified user.
 *
 * The previous course admin will still be a member of the course after the transfer.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param int $userID The user ID.
 * @return void No return value.
 */
function transferCourse(PDO $db, int $courseID, int $userID)
{
    $sql = 'UPDATE course_member AS cm SET admin=NULL WHERE cm.course_id=:courseID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->execute();
    $sql = 'UPDATE course_member AS cm SET admin=true WHERE cm.course_id=:courseID AND cm.user_id=:userID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
}

/**
 * Removes a user from a course.
 *
 * Does not transfer the course if the course admin is removed.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param int $userID The user ID.
 * @return void No return value.
 */
function removeMemberFromCourse(PDO $db, int $courseID, int $userID)
{
    $sql = 'DELETE FROM course_member WHERE user_id=? and course_id=?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$userID, $courseID]);
}