<?php
require_once(ROOT_DIR . "/src/modules/course/course_functions.php");
?>


<form action="api/upload.php" method="POST" enctype="multipart/form-data">
    <h3><?= LANG_UPLOAD_TITLE ?></h3>
    <label for="upload-name"><?= LANG_CONTENT_NAME ?></label>
    <input type="text" id="upload-name" name="upload-name" placeholder="<?= LANG_CONTENT_NAME ?>" required>
    <input type="file" name="upload-file" accept="<?= join(",", preg_filter("/^/", ".", ALLOWED_FORMATS)) ?>">
    <label for="course"><?= LANG_COURSE_SELECTOR ?></label>
    <select id="course" name="upload-course">
        <?php
        $courseArray = getCourseNameIDs($db, $_SESSION["uid"]);
        foreach ($courseArray as $course)
        {
            echo "<option value='{$course["id"]}'>" . htmlspecialchars($course["name"]) . "</option>";
        }
        ?>
    </select>
    <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
    <button type="submit" name="upload"><?= LANG_UPLOAD ?></button>
</form>
