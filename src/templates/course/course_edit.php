<?php
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");

$courseInfo = getCourse($db, $courseID);
?>

<form action="api/course.php" method="post">
    <input type="hidden" name="course-id" value="<?= $courseInfo["id"] ?>">
    <label for="course-edit-name"><?= LANG_COURSE_NAME ?></label>
    <input type="text" name="name" id="course-edit-name" title="<?= LANG_COURSE_NAME ?>" placeholder="<?= LANG_COURSE_NAME ?>" value="<?= htmlspecialchars($courseInfo["name"]) ?>" required>
    <label for="course-edit-description"><?= LANG_DESCRIPTION ?></label>
    <input type="text" name="description" id="course-edit-description" title="<?= LANG_DESCRIPTION ?>" placeholder="<?= LANG_DESCRIPTION ?>" value="<?= htmlspecialchars($courseInfo["description"]) ?>" required>
    <label for="course-edit-abbreviation"><?= LANG_ABBREVIATION ?></label>
    <input type="text" name="abbreviation" maxlength="5" id="course-edit-abbreviation" title="<?= LANG_ABBREVIATION ?>" placeholder="<?= LANG_ABBREVIATION ?>" value="<?= htmlspecialchars($courseInfo["abbreviation"]) ?>" required>
    <label for="course-edit-color"><?= LANG_COLOR ?></label>
    <input type="color" name="color" id="course-edit-color" title="<?= LANG_COLOR ?>" placeholder="<?= LANG_COLOR ?>" value="<?= htmlspecialchars($courseInfo["color"]) ?>" required>
    <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
    <button type="submit" name="update"><?= LANG_UPDATE ?></button>
</form>
