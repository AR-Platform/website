<div id="course-leave-modal" class="modal">
    <form id="course-leave-form" action="api/course.php" method="post">
        <h3><?= LANG_COURSE_LEAVE ?>?</h3>
        <p id="course-leave-form-p"></p>
        <input type="hidden" name="course-id">
        <input type='hidden' name='csrf' value='<?= $_SESSION["csrf"] ?>'>
        <button type="button"  class="two-inputs-row" onclick="closeCourseLeaveModal();"><?= LANG_CANCEL ?></button>
        <button type="submit" name="leave" class="critical two-inputs-row float-right"><?= LANG_LEAVE ?></button>
    </form>
</div>


<script>

    const clm = document.getElementById("course-leave-modal");
    const clf = document.getElementById("course-leave-form");
    const clfp = document.getElementById("course-leave-form-p");

    function openCourseLeaveModal(courseID, courseName) {
        clm.style.display = "block";
        clf["course-id"].value = courseID;
        clfp.innerText = '<?= LANG_COURSE_LEAVE_SENTENCE ?> "' + courseName + '"';
    }

    function closeCourseLeaveModal() {
        clm.style.display = "none";
    }

</script>