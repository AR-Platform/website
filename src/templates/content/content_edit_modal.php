<div id="edit-content-modal" class="modal">
    <form id="edit-content-form" action="api/content.php" method="post">
        <h3><?= LANG_CONTENT_OPTIONS ?></h3>
        <p id="edit-content-form-p"></p>
        <input type="hidden" name="content-id">
        <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
        <button type="button" name="convert" id="edit-content-form-convert"><?= LANG_CONVERT ?></button>
        <button type="submit" name="available" id="edit-content-form-available"><?= LANG_MAKE_AVAILABLE ?></button>
        <button type="button" name="enforce" class="critical" id="edit-content-form-enforce"><?= LANG_ENFORCE ?></button>
        <button type="button" name="delete" class="critical" id="edit-content-form-delete"><?= LANG_DELETE ?></button>
        <button type="button" onclick="closeEditModal()"><?= LANG_CANCEL ?></button>
    </form>
</div>


<script>

    const editcm = document.getElementById("edit-content-modal");
    const editcf = document.getElementById("edit-content-form");
    const editcfp = document.getElementById("edit-content-form-p");
    const editcfa = document.getElementById("edit-content-form-available");
    const editcfe = document.getElementById("edit-content-form-enforce");
    const editcfc = document.getElementById("edit-content-form-convert");
    const editcfd = document.getElementById("edit-content-form-delete");

    function openEditModal(contentID, contentName, available, converted) {
        editcm.style.display = "block";
        editcf["content-id"].value = contentID;
        editcfp.innerText = contentName;
        console.log(available);
        console.log(converted);
        if(!converted)
        {
            editcfe.style.display = "none";
            editcfa.style.display = "none";
            editcfd.style.display = "none";
            editcfc.style.display = "block";
            editcfc.onclick = function () {
                window.location.replace("convert.php?id=" + contentID);
            }
        }
        else
        {
            editcfc.style.display = "none";
            editcfa.style.display = "block";
            editcfd.style.display = "block";
            editcfd.onclick = function () {
                openDeleteModal(contentID, contentName);
                closeEditModal();
            }
            if(available)
            {
                editcfa.innerText = "<?= LANG_MAKE_NOT_AVAILABLE ?>";
                editcf["available"].value = 0;
                editcfe.style.display = "block";
                editcfe.onclick = function () {
                    openEnforceModal(contentID, contentName);
                    closeEditModal();
                }
            }
            else
            {
                editcfa.innerText = "<?= LANG_MAKE_AVAILABLE ?>";
                editcf["available"].value = 1;
                editcfe.style.display = "none";
            }
        }
    }

    function closeEditModal() {
        editcm.style.display = "none";
    }

</script>