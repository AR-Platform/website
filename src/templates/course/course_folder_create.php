<form action="api/course.php" method="post">
    <h3><?= LANG_FOLDER_CREATE ?></h3>
    <label for="course-folder-name"><?= LANG_FOLDER_NAME ?></label>
    <input type="text" id="course-folder-name" name="course-folder-name" placeholder="<?= LANG_FOLDER_NAME ?>" required>
    <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
    <input type='hidden' name='course-id' value='<?= $courseID ?>'>
    <button type="submit" name="folder"><?= LANG_CREATE ?></button>
</form>