<form action="api/institution.php" method="post">
    <h3><?= LANG_INSTITUTION_APPLY_SENTENCE ?></h3>
    <label for="institution-name"><?= LANG_INSTITUTION_NAME ?></label>
    <input type="text" id="institution-name" name="institution-name" placeholder="<?= LANG_INSTITUTION_NAME ?>" required>
    <label for="institution-email"><?= LANG_INSTITUTION_MAIL ?></label>
    <input type="email" id="institution-email" name="institution-email" placeholder="<?= LANG_INSTITUTION_MAIL ?>" required>
    <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
    <button type="submit" name="apply"><?= LANG_APPLY_NOW ?></button>
</form>