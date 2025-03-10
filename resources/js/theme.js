$(document).ready(function () {
    const $btnToggleTheme = $(".theme-toggle");
    const $html = $("html");
    const systemPrefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)"
    ).matches;
    let theme = localStorage.getItem("theme") || "system";

    $(".theme-toggle").removeClass("theme-selected");

    function applyTheme(theme) {
        $(".theme-toggle").removeClass("theme-selected");
        if (theme === "dark") {
            $html.addClass("dark");
            $(".theme-dark").addClass("theme-selected");
        } else if (theme === "light") {
            $html.removeClass("dark");
            $(".theme-light").addClass("theme-selected");
        } else if (theme === "system") {
            systemPrefersDark
                ? $html.addClass("dark")
                : $html.removeClass("dark");
            $(".theme-system").addClass("theme-selected");
        }
    }

    applyTheme(theme);

    $btnToggleTheme.on("click", function () {
        if ($html.hasClass("dark")) {
            theme = "light";
        } else {
            theme = "dark";
        }
        localStorage.setItem("theme", theme);
        applyTheme(theme);
    });
});
