<div id="enforce-content-modal" class="modal">
    <form id="enforce-content-form" action="api/course.php" method="post">
        <h3><?= LANG_ENFORCE_CONTENT ?>?</h3>
        <p id="enforce-content-form-p"></p>
        <input type="hidden" name="content-id">
        <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
        <button type="button"  class="two-inputs-row" onclick="closeEnforceModal()"><?= LANG_CANCEL ?></button>
        <button type="submit" name="enforce" class="critical two-inputs-row float-right"><?= LANG_ENFORCE ?></button>
    </form>
</div>


<script>

    const ecm = document.getElementById("enforce-content-modal");
    const ecf = document.getElementById("enforce-content-form");
    const ecfp = document.getElementById("enforce-content-form-p");

    function openEnforceModal(contentID, contentName) {
        ecm.style.display = "block";
        ecf["content-id"].value = contentID;
        ecfp.innerText = '<?= LANG_CONTENT_ENFORCE_SENTENCE ?> "' + contentName + '"';
    }

    function closeEnforceModal() {
        ecm.style.display = "none";
    }

</script>