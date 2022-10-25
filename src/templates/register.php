<!--suppress HtmlUnknownTarget -->
<form action="api/login.php" method="post">
    <h3><?= LANG_REGISTER_TITLE ?></h3>
    <label for="register-username"><?= LANG_USERNAME ?></label>
    <input type="text" id="register-username" name="username" placeholder="<?= LANG_USERNAME ?>" onfocusout="validateName();"
           required>
    <label for="register-email">E-Mail</label>
    <input type="email" id="register-email" name="email" placeholder="E-Mail" onfocusout="validateEmail();" required>
    <label for="register-pw1"><?= LANG_PASSWORD ?></label>
    <input type="password" id="register-pw1" name="password" placeholder="<?= LANG_PASSWORD ?>" onkeyup="validatePassword();" required>
    <label for="register-pw2"><?= LANG_PASSWORD_CONFIRMATION ?></label>
    <input type="password" id="register-pw2" name="password-confirmation" placeholder="<?= LANG_PASSWORD_CONFIRMATION ?>"
           onkeyup="validatePassword();" required>
    <input type="hidden" name="register" value="<?= LANG_REGISTER ?>">
    <button type="button" name="register" onclick="submitForm(this.form);"><?= LANG_REGISTER ?></button>
</form>

<script>
    let usr = document.getElementById('register-username');
    let email = document.getElementById('register-email');
    let pw1 = document.getElementById('register-pw1');
    let pw2 = document.getElementById('register-pw2');

    function validatePassword() {
        if(pw1.value === "" || pw2.value === "") {
            removeValid(pw1);
            removeValid(pw2);
        }
        else if (pw1.value === pw2.value) {
            setValid(pw1, true)
            setValid(pw2, true)
        } else {
            setValid(pw1, false)
            setValid(pw2, false)
        }
    }

    function validateName() {
        if(usr.value === "") {
            removeValid(usr);
            return;
        }
        let ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                if (this.response) {
                    usr.setCustomValidity("Username already exists!");
                    //usr.reportValidity();
                    setValid(usr, false)
                } else {
                    setValid(usr, true)
                }
            }
        };
        ajax.open("GET", "api/user.php?username=" + usr.value, true);
        ajax.send();
    }

    function validateEmail() {
        if(email.value === "") {
            removeValid(email);
            return;
        }
        let ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                if (this.response) {
                    email.setCustomValidity("<?= LANG_EMAIL_TAKEN ?>");
                    //email.reportValidity();
                    setValid(email, false);
                } else if (email.validity.typeMismatch) {
                    email.setCustomValidity("<?= LANG_EMAIL_INVALID ?>");
                    setValid(email, false);
                } else {
                    setValid(email, true);
                }
            }
        };
        ajax.open("GET", "api/user.php?email=" + email.value, true);
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

    function removeValid(inputElement) {
        inputElement.classList.remove("invalid");
        inputElement.classList.remove("valid");
    }

    function isValid() {
        let outputBool = true;
        if (!usr.validity.valid) {
            setValid(usr, false);
            outputBool = false;
        }
        if (!email.validity.valid) {
            setValid(email, false);
            outputBool = false;
        }
        if (!pw1.validity.valid || pw1.value !== pw2.value) {
            setValid(pw1, false);
            outputBool = false;
        }
        if (!pw2.validity.valid || pw1.value !== pw2.value) {
            setValid(pw2, false);
            outputBool = false;
        }
        return outputBool;
    }

    function submitForm(form) {
        if (isValid() && usr.checkValidity()) {
            form.submit();
        }
    }
</script>