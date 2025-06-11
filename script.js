document.addEventListener("DOMContentLoaded", () => {
    const themeSwitch = document.getElementById("theme-switch");
    const themeLink = document.getElementById("theme-link");

    // Проверяем сохранённую тему в localStorage
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
        themeLink.href = savedTheme;
        themeSwitch.checked = savedTheme === "dark.css";
    } else {
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

    // Копирование ключа по клику на input с попап-уведомлением
    const copyPopup = document.getElementById("copyPopup");
    document.querySelectorAll('.key-input').forEach(function(input) {
        input.addEventListener('click', function() {
            input.select();
            document.execCommand('copy');
            // Показываем попап
            if (copyPopup) {
                copyPopup.style.display = "block";
                setTimeout(() => {
                    copyPopup.style.display = "none";
                }, 5400);
            }
        });
    });

    // Определяем кнопку
    const scrollToTopButton = document.getElementById("scrollToTop");

    // Показываем кнопку при прокрутке
    window.addEventListener("scroll", () => {
        if (window.scrollY > 15) {
            scrollToTopButton.style.display = "block";
        } else {
            scrollToTopButton.style.display = "none";
        }
    });

    // Добавляем событие клика для возвращения наверх
    scrollToTopButton.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
});