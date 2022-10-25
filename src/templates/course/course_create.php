<form action="api/course.php" method="post">
    <h3><?= LANG_COURSE_CREATE_TITLE ?></h3>
    <label for="course-name"><?= LANG_COURSE_NAME ?></label>
    <input type="text" id="course-name" name="course-name" placeholder="<?= LANG_COURSE_NAME ?>" required>
    <label for="description"><?= LANG_DESCRIPTION ?></label>
    <textarea id="description" name="course-description" placeholder="<?= LANG_DESCRIPTION ?>" required></textarea>
    <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
    <button type="submit" name="create"><?= LANG_CREATE ?></button>
</form>