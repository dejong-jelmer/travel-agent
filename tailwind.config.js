import defaultTheme from "tailwindcss/defaultTheme";
import screens from "./resources/js/screens.js";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "media",
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
                poppins: [
                    "Poppins",
                    "system-ui",
                    ...defaultTheme.fontFamily.sans,
                ],
                caveat: [
                    "Caveat",
                    "system-ui",
                    ...defaultTheme.fontFamily.sans,
                ],
                cormorant: [
                    "CormorantGaramond",
                    "system-ui",
                    ...defaultTheme.fontFamily.sans,
                ],
                nunito: [
                    "Nunito",
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
                    primary: "#2d5f6e",
                    accent: "#f59e0b",
                    secondary: "#f5f0e8",
                    text: "#1e2d3d",
                    light: "#a3bccb",
                    subtle: "#afcb98",
                    earth: "#dcc7aa",
                    link: "#82b2ca",
                },
                // Status feedback colors
                status: {
                    error: "#dc3545",
                    success: "#198754",
                    warning: "#ffc107",
                    info: "#0d6efd",
                },
            },
            screens: screens, // {phone: '0px', tablet: '700px', laptop: '900px', desktop: '1200px', wide: '1350px'}
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
