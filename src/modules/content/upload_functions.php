<?php
require_once(__DIR__ . "/../../../config/config.php");
require_once(ROOT_DIR . "/src/modules/content/shell_functions.php");

/**
 * Validates the user upload before proceeding with the conversion.
 * 
 * @param array $file The user uploaded file.
 * @return bool|string True if the upload is valid, the error code otherwise.
 */
function validateUpload(array $file): bool|string
{
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileActualExt = getFileExtension($fileName);

    if (!in_array($fileActualExt, ALLOWED_FORMATS)) {
        return "file type not allowed";
    } elseif ($fileError !== 0) {
        switch ($fileError) {
            case 1:
                return "php.ini upload_max_filesize exceeded";
            case 2:
                return "HTML MAX_FILE_SIZE exceeded";
            case 3:
                return "partial upload";
            case 4:
                return "no file was uploaded";
            case 6:
                return "no temporary directory";
            case 7:
                return "error saving file";
            case 8:
                return "a php extensions stopped the upload";
        }
    } elseif ($fileSize > MAX_FILESIZE) {
        return "file too big";
    }
    return true;
}

/**
 * Creates the database upload and content entry.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @param int $courseID The ID of the course.
 * @param int|null $folderID The ID of the folder inside the course.
 * @param string $contentName The name of the content.
 * @param string $filePath The file path of the uploaded file.
 * @param string $fileExt The file extension of the uploaded file.
 * @return array An array containing the ID of the upload and content entry.
 */
function createUploadContentEntry(PDO $db, int $userID, int $courseID, int|null $folderID, string $contentName, string $filePath, string $fileExt): array
{
    error_log("Folder ID: $folderID");

    $sql = 'INSERT INTO upload (user_id, file_name, file_ext) VALUES(:userID, :fileName, :fileExt)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->bindValue(':fileName', $filePath);
    $stmt->bindValue(':fileExt', $fileExt);
    $stmt->execute();
    $uploadID = $db->lastInsertId();
    error_log("Upload ID: $uploadID");

    $locationID = getLocationID($db, $courseID, $folderID);
    error_log("Location ID: $locationID");

    $sql = 'INSERT INTO content (upload_id, location_id, name) VALUES(:uploadID, :locationID, :contentName)';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':uploadID', $uploadID);
    $stmt->bindValue(':locationID', $locationID);
    $stmt->bindValue(':contentName', $contentName);
    $stmt->execute();
    $contentID = $db->lastInsertId();
    return array($uploadID, $contentID);
}

/**
 * Fetches the ID of the location table that represents the course and folder combination.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The ID of the course.
 * @param int|null $folderID The ID of the folder inside the course.
 * @return string The ID of the location table that represents the course and folder combination.
 */
function getLocationID(PDO $db, int $courseID, int|null $folderID): string
{
    $sql = 'SELECT DISTINCT id FROM location WHERE course_id=:courseID AND folder_id=:folderID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':courseID', $courseID);
    $stmt->bindValue(':folderID', $folderID);
    $stmt->execute();
    $output = $stmt->fetch(PDO::FETCH_NUM);
    if (!$output) {
        $sql = 'INSERT INTO location (course_id, folder_id) VALUES(:courseID, :folderID)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':courseID', $courseID);
        $stmt->bindValue(':folderID', $folderID);
        $stmt->execute();
        return $db->lastInsertId();
    } else {
        return $output[0];
    }
}

/**
 * Updates the database upload entry with the given options.
 *
 * @param PDO $db The PDO database instance.
 * @param int $uploadID The ID of the upload.
 * @param string $options The options string from the file converter.
 */
function addUploadEntryOptions(PDO $db, int $uploadID, string $options)
{
    $sql = 'UPDATE upload SET options=:options WHERE id=:uploadID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':uploadID', $uploadID);
    $stmt->bindValue(':options', $options);
    $stmt->execute();
}

/**
 * Extracts the file extension from the given file name / path.
 *
 * @param string $fileName The file path of the uploaded file.
 * @return string The file extension of the file.
 */
function getFileExtension(string $fileName): string
{
    $fileExt = explode('.', $fileName);
    return strtolower(end($fileExt));
}

/**
 * Fetches a list of all uploads from the given user which are not complete yet.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @return array A list of all incomplete uploads for the given user.
 */
function getIncompleteUploads(PDO $db, int $userID): array
{
    $sql = 'SELECT c.id, c.name, u.options, l.course_id FROM content AS c JOIN upload AS u ON c.upload_id = u.id JOIN location AS l ON c.location_id=l.id WHERE u.user_id=:userID AND (c.converted IS NULL OR c.converted=false) AND u.options IS NOT NULL';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Checks if the user has access to the upload.
 *
 * @param PDO $db The PDO database instance.
 * @param int $userID The ID of the user.
 * @param int $contentID The ID of the content.
 * @return bool True if the user started the upload and therefore owns it.
 */
function ownsUploadFileID(PDO $db, int $userID, int $contentID): bool
{
    $sql = 'SELECT 1 FROM upload AS u JOIN content AS c ON u.id = c.upload_id WHERE c.id=:contentID AND u.user_id=:userID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->bindValue(':userID', $userID);
    $stmt->execute();
    return sizeof($stmt->fetchAll()) > 0;
}

/**
 * Fetches all information regarding an upload by its ID.
 *
 * @param PDO $db The PDO database instance.
 * @param int $uploadID The ID of the upload.
 * @return array|bool mixed
 */
function getUploadFileByContentID(PDO $db, int $uploadID): array|bool
{
    $sql = 'SELECT DISTINCT * FROM upload AS u JOIN content AS c ON u.id = c.upload_id WHERE c.id=:uploadID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':uploadID', $uploadID);
    $stmt->execute();
    return $stmt->fetch();
}

/**
 * Marks the given content as converted.
 * 
 * @param PDO $db The PDO database instance.
 * @param int $contentID The ID of the content.
 * @return void No return value.
*/
function markContentAsConverted(PDO $db, int $contentID)
{
    $sql = 'UPDATE content SET converted=true WHERE id=:contentID';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':contentID', $contentID);
    $stmt->execute();
}