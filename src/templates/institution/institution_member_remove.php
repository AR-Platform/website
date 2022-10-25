<?php
require_once(ROOT_DIR . "/src/modules/institution/institution_functions.php");
?>

<form action="api/institution.php" method="post">
    <h3><?= LANG_MEMBER_REMOVE ?></h3>
    <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
    <label for="institution-member-remove-id"><?= LANG_SELECT ?></label>
    <select id="institution-member-remove-id" name="member-id" required>
        <option disabled selected hidden> --- <?= LANG_SELECT ?> --- </option>
        <?php
        $institutionMember = getInstitutionMembers($db, $_SESSION["institution"]);
        foreach ($institutionMember as $member) {
            if ($member["id"] == $_SESSION["uid"]) continue;
            echo "<option value='{$member["id"]}'>" . htmlspecialchars($member["username"]) . "</option>";
        }
        ?>
    </select>
    <button type="submit" name="member-remove" class="critical"><?= LANG_REMOVE ?></button>
</form>