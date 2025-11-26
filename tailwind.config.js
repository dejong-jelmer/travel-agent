import defaultTheme from "tailwindcss/defaultTheme";
import screens from "./resources/js/screens.js";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                tahu: ["Tahu", "system-ui", ...defaultTheme.fontFamily.sans],
                poppins: [
                    "Poppins",
                    "system-ui",
                    ...defaultTheme.fontFamily.sans,
                ],
                elite: [
                    "SpecialElite",
                    "system-ui",
                    ...defaultTheme.fontFamily.sans,
                ],
            },
            fontSize: {
                "2xs": "10px",
            },
            colors: {
                // Brand identity colors
                brand: {
                    primary: "#2F3E46",
                    secondary: "#F0F4F7",
                    tertiary: "#ccf6ff",
                    light: "#A3BCCB"
                },
                accent: {
                    primary: "#f0972d",
                    sage: "#AFCB98",
                    earth: "#DCC7AA",
                    terracotta: "#B17C65",
                    link: "#82b2ca"
                },
                // Status feedback colors
                status: {
                    error: "#dc3545",
                    success: "#198754",
                    warning: "#ffc107",
                    info: "#0d6efd"
                },
            },
            screens: screens, // {phone: '0px', tablet: '700px', laptop: '900px', desktop: '1200px', wide: '1800px'}
            keyframes: {
                "slide-left-right": {
                    "0%, 100%": { transform: "translateX(0px)" },
                    "33%": { transform: "translateX(-25px)" },
                    "66%": { transform: "translateX(25px)" },
                },
            },
            animation: {
                "wiggle-x": "slide-left-right 6s ease-in-out infinite",
            },
        },
    },
    plugins: [],
};
