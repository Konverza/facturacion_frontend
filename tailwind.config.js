import defaultTheme from "tailwindcss/defaultTheme";
const colors = require("tailwindcss/colors");
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    defaultTheme: "light",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: "#e5e7f6",
                    100: "#bcc1ec",
                    200: "#9199e1",
                    300: "#6771d7",
                    400: "#4554cc",
                    500: "#0a1d7d",
                    600: "#081769",
                    700: "#061256",
                    800: "#040c42",
                    900: "#02062e",
                    950: "#010318",
                },
            },
            width: {
                "calc-full-minus-56": "calc(100% - 14rem)",
                "calc-full-minus-64": "calc(100% - 16rem)",
                "calc-full-minus-16" : "calc(100% - 4rem)",
                "calc-full-minus-8" : "calc(100% - 2rem)",
            },
        },
    },
    plugins: [
        require("flowbite/plugin"),
        require("tailwindcss-motion"),
        require("tailwindcss-animated"),
    ],
};
