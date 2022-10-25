<?php
require_once(ROOT_DIR . "/src/modules/content/upload_functions.php");

$incompleteUploads = getIncompleteUploads($db, $_SESSION["uid"]);

foreach ($incompleteUploads as $upload) {
    echo "<div>";
    echo "<h3>" . htmlspecialchars($upload["name"]) . "</h3>";
    echo parseConvertForm($upload["id"], $upload["course_id"], explode(";", $upload["options"]));
    echo "</div>";
}

/**
 * Creates the convert form based on the specified information.
 *
 * @param int $contentID The ID of the content.
 * @param int $courseID The ID of the course the content is in.
 * @param array $array The conversion options.
 * @return string The form as a string.
 * @noinspection BadExpressionStatementJS
 * @noinspection JSVoidFunctionReturnValueUsed
 */
function parseConvertForm(int $contentID, int $courseID, array $array): string
{
    $html = array();
    $html[] = "<form action='api/upload.php' method='post'>";
    $counter = 0;
    $inCollapsible = false;

    for ($i = 0; $i < count($array); $i++) {
        $parameter = htmlspecialchars($array[$i]);
        if ($parameter[0] == "#") {
            $parameter = substr($parameter, 1);
            $html[] = "<br>";
            $html[] = "<span>$parameter</span>";
        } else {
            $childEntry = $parameter[0] == "_";
            $explosionResult = explode(":", $parameter);
            $type = array_pop($explosionResult) == "bool" ? "checkbox" : "text";
            $option = implode(":", $explosionResult);
            if ($childEntry) {
                if (!$inCollapsible) {
                    $html[] = "<div id='convert-collapsible-" . ($counter - 1) . "' style='padding-left: 25px; background-color: var(--main-bg-color-shade); border-radius: 10px;'>";
                    $inCollapsible = true;
                }
                $option = substr($option, 1);
            } elseif ($inCollapsible) {
                $html[] = "</div>";
                $inCollapsible = false;
            }
            if ($type == "checkbox") {
                $isCollapsibleHeader = !$inCollapsible && $i != count($array) - 1 && $array[$i + 1][0] == "_";
                $html[] = "<div class='full-width' style='padding: 2px 0;'>";
                $html[] = "<input type='hidden' value='0' name='option$counter'>";
                if ($isCollapsibleHeader) {
                    $html[] = "<input type='$type' value='1' id='form-option-$counter' name='option$counter' onclick='toggleSubCategories(this, $counter)'>";
                } else {
                    $html[] = "<input type='$type' value='1' id='form-option-$counter' name='option$counter'>";
                }
                $html[] = "<label class='checkbox' for='form-option-$counter'>$option</label>";
                $html[] = "</div>";
                if ($isCollapsibleHeader) {
                    $html[] = "<div class='collapsible float-right' style='margin-top: -11px;'></div>";
                }
            } else {
                $html[] = "<br>";
                $html[] = "<input type='$type' id='form$option' name='option$counter'>";
            }
            $counter++;
        }
    }
    if ($inCollapsible) {
        $html[] = "</div>";
    }

    $html[] = "<input type='hidden' name='csrf' value='{$_SESSION["csrf"]}'>";
    $html[] = "<input type='hidden' name='id' value='$contentID'>";
    $html[] = "<input type='hidden' name='course' value='$courseID'>";
    $html[] = "<button type='submit' name='convert'>" . LANG_CONVERT . "</button></form>";
    return implode($html);
}

?>

<script>
    function toggleSubCategories(checkbox, index) {
        const checkboxes = document.querySelectorAll("#convert-collapsible-" + index + " input[type=\"checkbox\"]");
        const enabled = checkbox.checked;
        for (const checkbox of checkboxes) {
            checkbox.checked = enabled;
        }
    }
</script>
