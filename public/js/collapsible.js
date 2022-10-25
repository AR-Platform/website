let collapsibleDivs = document.getElementsByClassName("collapsible");

for (let i = 0; i < collapsibleDivs.length; i++) {
    collapsibleDivs[i].addEventListener("click", function() {
        this.classList.toggle("collapsible-open");
        let content = this.nextElementSibling;
        if (content.style.maxHeight) {
            content.style.maxHeight = null;
        } else {
            content.style.maxHeight = content.scrollHeight + "px";
        }
    });
}