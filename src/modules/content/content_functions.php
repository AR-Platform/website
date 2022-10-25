<?php

/**
 * Validates if the given user has admin permission regarding the given content.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @param int $contentID The ID of the content.
 * @return bool True if the user has admin permission, false otherwise.
 */
function checkContentAdminPermission(PDO $db, int $userID, int $contentID): bool
{
    $sql = 'SELECT DISTINCT 1 FROM content AS c JOIN location AS l ON c.location_id = l.id JOIN course_member AS ca ON l.course_id = ca.course_id WHERE ca.user_id=:userID AND c.id=:contentID AND ca.admin';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->execute();
    return count($stmt->fetchAll(PDO::FETCH_NUM)) > 0;
}

/**
 * Validates if the given user has member permission regarding the given content.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @param int $contentID The ID of the content.
 * @return bool
 */
function checkContentPermission(PDO $db, int $userID, int $contentID): bool
{
    $sql = 'SELECT DISTINCT 1 FROM content AS c JOIN location AS l ON c.location_id = l.id JOIN course_member AS ca ON l.course_id = ca.course_id WHERE ca.user_id=:userID AND c.id=:contentID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->execute();
    return count($stmt->fetchAll(PDO::FETCH_NUM)) > 0;
}

/**
 * Deletes the content with the given ID from the database.
 *
 * No checks for permission performed in advance, validate if necessary before.
 *
 * @param PDO $db The PDO database instance.
 * @param int $contentID The ID of the content.
 * @return void No return value.
 */
function deleteContent(PDO $db, int $contentID)
{
    unlink(DOWNLOAD_DIR . "/$contentID.ares");

    $sql = 'DELETE FROM download WHERE content_id=:contentID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->execute();

    $sql = 'DELETE FROM content WHERE id=:contentID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->execute();
}

/**
 * Checks if the content with the given ID is currently available.
 *
 * @param PDO $db The PDO database instance.
 * @param int $contentID The ID of the content.
 * @return bool True if the content is available, false otherwise.
 */
function contentIsAvailable(PDO $db, int $contentID): bool
{
    $sql = 'SELECT DISTINCT available FROM content WHERE id=:contentID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->execute();
    $output = $stmt->fetch(PDO::FETCH_NUM);
    return $output && $output[0] == 1;
}

/**
 * Generates the content file path based on the given content ID.
 *
 * @param int $contentID The ID of the content.
 * @return string The generated file path.
 */
function getContentFilePath(int $contentID): string
{
    return DOWNLOAD_DIR . "/$contentID.ares";
}

/**
 * Starts a download for the given file path.
 *
 * @param string $path The path to the file to download.
 * @return void No return value.
 */
function downloadContent(string $path)
{
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($path));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    if(ob_get_contents()) {
        ob_clean();
    }
    flush();
    readfile($path);
}

/**
 * Adds an anonymous log into the download table.
 *
 * @param PDO $db The PDO database instance.
 * @param int $contentID The ID of the content.
 * @return void No return value.
 */
function addDownloadEntry(PDO $db, int $contentID)
{
    $sql = 'INSERT INTO download (content_id, time) VALUES(:contentID, :downloadTime)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->bindValue(':downloadTime', time());
    $stmt->execute();
}

/**
 * Updates the availability of the given content.
 *
 * @param PDO $db The PDO database instance.
 * @param int $contentID The ID of the content.
 * @param bool $available True will make the content available.
 * @return void No return value.
 */
function makeContentAvailable(PDO $db, int $contentID, bool $available)
{
    $sql = 'UPDATE content SET available=:available WHERE id = :contentID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':available', (int)$available);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->execute();
}