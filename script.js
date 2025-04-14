document.addEventListener("DOMContentLoaded", () => {
    const themeSwitch = document.getElementById("theme-switch");
    const themeLink = document.getElementById("theme-link");

    // Проверяем сохранённую тему в localStorage
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
        themeLink.href = savedTheme;
        themeSwitch.checked = savedTheme === "dark.css";
    } else {
        // Устанавливаем style.css по умолчанию
        themeLink.href = "style.css";
        themeSwitch.checked = false;
    }

    themeSwitch.addEventListener("change", () => {
        if (themeSwitch.checked) {
            themeLink.href = "dark.css";
            localStorage.setItem("theme", "dark.css");
        } else {
            themeLink.href = "style.css";
            localStorage.setItem("theme", "style.css");
        }
    });
});