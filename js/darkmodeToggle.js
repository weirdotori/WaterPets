const toggleBtn = document.getElementById("themeToggle");
const themeIcon = document.getElementById("themeIcon");
const body = document.body;

// Load saved theme
if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark-mode");
    themeIcon.src = "/images/darkmode.png"; // sun icon
}

// Toggle theme on click
toggleBtn.addEventListener("click", () => {
    body.classList.toggle("dark-mode");

    if (body.classList.contains("dark-mode")) {
        localStorage.setItem("theme", "dark");
        themeIcon.src = "/images/lightmode.png";
        themeIcon.style.filter = "invert(1)";
    } else {
        localStorage.setItem("theme", "light");
        themeIcon.src = "/images/darkmode.png";
        themeIcon.style.filter = "invert(0)";
    }
});
