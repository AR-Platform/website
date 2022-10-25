<div id="delete-content-modal" class="modal">
    <form id="delete-content-form" action="api/content.php" method="post">
        <h3><?= LANG_CONTENT_DELETE ?>?</h3>
        <p id="delete-content-form-p"></p>
        <input type="hidden" name="content-id">
        <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
        <button type="button"  class="two-inputs-row" onclick="closeDeleteModal()"><?= LANG_CANCEL ?></button>
        <button type="submit" name="delete" class="critical two-inputs-row float-right"><?= LANG_DELETE ?></button>
    </form>
</div>


<script>

    const dcm = document.getElementById("delete-content-modal");
    const dcf = document.getElementById("delete-content-form");
    const dcfp = document.getElementById("delete-content-form-p");

    function openDeleteModal(contentID, contentName) {
        dcm.style.display = "block";
        dcf["content-id"].value = contentID;
        dcfp.innerText = '<?= LANG_CONTENT_DELETE_SENTENCE ?> "' + contentName + '"';
    }

    function closeDeleteModal() {
        dcm.style.display = "none";
    }

</script>