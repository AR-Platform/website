<?php
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
?>

<form action="api/course.php" method="post">
    <h3><?= LANG_TRANSFER_SENTENCE ?></h3>
    <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
    <input type="hidden" name="course-id" value="<?= $courseID ?>">
    <label for="course-transfer-uid"><?= LANG_SELECT ?></label>
    <select id="course-transfer-uid" name="member-id" required>
    <option selected hidden value=""> --- <?= LANG_SELECT ?> --- </option>
        <?php
        $courseMember = getCourseMembers($db, $courseID);
        foreach ($courseMember as $member) {
            if ($member["id"] == $_SESSION["uid"]) continue;
            echo "<option value='{$member["id"]}'>" . htmlspecialchars($member["username"]) . "</option>";
        }
        ?>
    </select>
    <button type="submit" name="member-transfer" class="critical"><?= LANG_TRANSFER ?></button>
</form>