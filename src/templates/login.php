<form action="api/login.php" method="post">
    <h3><?= LANG_LOGIN_TITLE ?></h3>
    <label for="login-credential"><?= LANG_USERNAME_EMAIL ?></label>
    <input type="text" id="login-credential" name="credential" placeholder="<?= LANG_USERNAME_EMAIL ?>" required>
    <label for="login-password"><?= LANG_PASSWORD ?></label>
    <input type="password" id="login-password" name="password" placeholder="<?= LANG_PASSWORD ?>" required>
    <button type="submit" name="login"><?= LANG_LOGIN ?></button>
</form>