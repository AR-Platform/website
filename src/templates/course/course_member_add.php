<form action="api/course.php" method="post">
    <h3><?= LANG_MEMBER_ADD ?></h3>
    <label for="member-add-user-id"><?= LANG_USERNAME ?></label>
    <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
    <input type="hidden" name="course-id" value="<?= $courseID ?>">
    <button type="submit" disabled style="display: none" aria-hidden="true"></button>
    <input type="text" id="member-add-user-id" name="username" placeholder="<?= LANG_USERNAME ?>" required>
    <input type="hidden" name="member-add">
    <button type="button" name="member-add" onclick="submitForm(this.form);"><?= LANG_ADD ?></button>
</form>

<script>
    let usr = document.getElementById('member-add-user-id');

    function validateName(form) {
        if(usr.value === "") {
            removeValid(usr);
            return;
        }
        let ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                if (this.response) {
                    setValid(usr, true);
                    form.submit();
                } else {
                    usr.setCustomValidity("Username does not exist!");
                    //usr.reportValidity();
                    setValid(usr, false);
                }
            }
        };
        ajax.open("GET", "api/user.php?username=" + usr.value, true);
        ajax.send();
    }

    function setValid(inputElement, valid) {
        if (valid) {
            inputElement.classList.remove("invalid");
            inputElement.classList.add("valid");
            inputElement.setCustomValidity("");
        } else {
            inputElement.classList.add("invalid");
            inputElement.classList.remove("valid");
        }
    }

    function submitForm(form) {
        validateName(form);
    }
</script>