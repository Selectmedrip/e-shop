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
// Определяем кнопку
const scrollToTopButton = document.getElementById("scrollToTop");

// Показываем кнопку при прокрутке
window.addEventListener("scroll", () => {
    if (window.scrollY > 15) { // Показываем кнопку после 300px скролла
        scrollToTopButton.style.display = "block";
    } else {
        scrollToTopButton.style.display = "none";
    }
});

// Добавляем событие клика для возвращения наверх
scrollToTopButton.addEventListener("click", () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth" // Плавная прокрутка
    });
});