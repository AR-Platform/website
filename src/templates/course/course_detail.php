<?php
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
require_once(ROOT_DIR . "/src/templates/content/content_delete_modal.php");
require_once(ROOT_DIR . "/src/templates/content/content_enforce_modal.php");
require_once(ROOT_DIR . "/src/templates/content/content_edit_modal.php");

//Requires $courseID and $courseAdmin to be set

$courseInfo = getCourse($db, $courseID);
$courseFolder = getCourseFolders($db, $courseID);
$courseLevelContent = getCourseLevelContent($db, $courseID);
$enforceMode = $courseInfo["enforce_id"] != null;

createCourseDetailCard($db, $courseID, $courseInfo, $courseFolder, $courseLevelContent, $enforceMode);

if ($enforceMode && WATCHDOG_ENABLE) {
    createWatchdogJS($courseID);
}


/**
 * Creates the detailed course card.
 *
 * @param PDO $db The PDO database instance.
 * @param int $courseID The course ID.
 * @param array $courseInfo All course information as an array.
 * @param array $courseFolder An array containing all course folder ID's and names.
 * @param array $courseLevelContent An array with all contents from the base folder.
 * @param bool $enforceMode If true enforce mode features will be enabled.
 * @return void No return value.
 */
function createCourseDetailCard(PDO $db, int $courseID, array $courseInfo, array $courseFolder, array $courseLevelContent, bool $enforceMode): void
{
    echo $enforceMode ? "<div class='critical-outline'>" : "<div>";
    echo "<h1>" . htmlspecialchars($courseInfo["name"]) . "</h1>";
    echo "<p>" . htmlspecialchars($courseInfo["description"]) . "</p>";
    echo $enforceMode && WATCHDOG_ENABLE ? "<p id='course-enforce-user-count'></p>" : "";
    editRibbon("course", $courseID, $courseID, $enforceMode);

    foreach ($courseFolder as $folder) {
        $folderContent = getFolderContent($db, $folder["id"]);
        createFolder($courseID, $folder["id"], $folderContent, $folder["name"]);
    }
    createFolder($courseID, "null", $courseLevelContent, LANG_COURSE_LEVEL);
    echo "</div>";
}

/**
 * Creates and prints the HTML code to visualize the specified course folder.
 *
 * @param int $courseID The course ID.
 * @param mixed $folderID The folder ID or NULL for base folder.
 * @param array $folderContent An array containing all contents from within the folder.
 * @param string $name The name of the folder.
 * @return void
 */
function createFolder(int $courseID, mixed $folderID, array $folderContent, string $name): void
{
    $name = htmlspecialchars($name);
    echo "<div class='simple-box'>";
    $empty = count($folderContent) == 0;
    echo $empty ? "<h2>$name - " . LANG_EMPTY . "</h2>" : "<h2 class='collapsible'>$name</h2>";
    if (!$empty) {
        echo "<div class='folder-content'>";
        editRibbon("folder", $folderID, $courseID);
        foreach ($folderContent as $content) {
            outputContent($content);
        }
        echo "</div>";
    }
    echo "</div>";
}

/**
 * Creates and prints the HTML code for a single content object.
 *
 * @param array $content The array containing all content information.
 * @return void No return value.
 * @noinspection BadExpressionStatementJS
 * @noinspection JSVoidFunctionReturnValueUsed
 */
function outputContent(array $content): void
{
    $displayName = $content["name"];
    $available = $content["available"] == "1";
    $converted = $content["converted"] == "1";
    $safeName = htmlspecialchars($displayName, ENT_QUOTES);
    $displayName = $safeName . ($converted ? "" : " (" . LANG_NOT_CONVERTED . ")");
    $safeName = str_replace(array("\r\n", "\r", "\n"), "", htmlentities($safeName, ENT_QUOTES));
    $funcPara = "{$content["id"]} , \"$safeName\"";
    $convertedAsStringBool = $converted ? "true" : "false";
    $availableAsStringBool = $available ? "true" : "false";
    $funcParaEdit = "{$content["id"]} , \"$safeName\" , $availableAsStringBool , $convertedAsStringBool";
    $listClass = $content["available"] ? "" : " hidden";
    $contentDelete = "<div class='content-action content-delete'><a onclick='openDeleteModal($funcPara)'>✖</a></div>";
    echo "<div class='list-item$listClass'><span onclick='openEditModal($funcParaEdit)'>$displayName</span>$contentDelete";
    if ($converted && $available) {
        echo "<div class='content-action content-lock' onclick='openEnforceModal($funcPara)'></div>";
    }
    echo "</div>";

}

/**
 * Creates and prints the HTML code for the edit ribbon.
 *
 * @param string $scope The scope identifier (e.g. "course" or "folder")
 * @param mixed $scopeID The ID of the specified scope or NULL for the base folder.
 * @param int $courseID The ID of the course.
 * @param bool $enforceMode Optional. If true options for the enforce mode will be added.
 * @return void No return value.
 */
function editRibbon(string $scope, mixed $scopeID, int $courseID, bool $enforceMode = false): void
{
    echo "<form method='post' action='api/course.php' class='ribbon'>";
    echo "<input type='hidden' name='scope' value='$scope'>";
    echo "<input type='hidden' name='scope-id' value='$scopeID'>";
    echo "<input type='hidden' name='course-id' value='$courseID'>";
    echo "<input type='hidden' name='csrf' value='{$_SESSION["csrf"]}'>";
    echo "<button type='submit' name='available' value='1'>" . LANG_MAKE_ALL_AVAILABLE . "</button>";
    echo "<button type='submit' name='available' value='0'>" . LANG_MAKE_ALL_NOT_AVAILABLE . "</button>";
    if ($enforceMode) {
        echo "<button type='submit' name='stop-enforce' class='critical'>" . LANG_STOP_ENFORCING . "</button>";
    }
    echo "</form>";
}

/**
 * Creates the required JavaScript code for the enforce mode with activated watchdog.
 *
 * @param int $courseID The course ID.
 * @return void No return value.
 */
function createWatchdogJS(int $courseID): void
{
    $langUserAmount = LANG_USER_AMOUNT;
    $watchdogIntervalTime = WATCHDOG_INTERVAL_TIME * 1000;
    /** @noinspection JSUnnecessarySemicolon */
    /** @noinspection BadExpressionStatementJS */
    echo "<script>
        let userCount = document.getElementById('course-enforce-user-count');
        let amount = 0;
        (function worker() {
    
            let ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let localAmount = parseInt(this.response);
                    if(localAmount > amount) {
                        userCount.style.color = 'green';
                    } else if (localAmount < amount) {
                        userCount.style.color = 'red';
                    }
                    userCount.innerHTML = '$langUserAmount: ' + localAmount;
                    amount = localAmount;
                    setTimeout(resetColor, 1000);
                    setTimeout(worker, $watchdogIntervalTime);
                }
            };
            ajax.open('GET', 'api/course.php?count&course=$courseID' , true);
            ajax.send();
        })();
        
        function resetColor() {
            userCount.style.color = '';
        }
    </script>";
}
