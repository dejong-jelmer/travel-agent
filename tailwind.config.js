import defaultTheme from "tailwindcss/defaultTheme";
import typography from "@tailwindcss/typography";
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
                ]
            },
            colors: {
                // Brand identity colors
                brand: {
                    primary: "#2d5f6e",
                    accent: "#f59e0b",
                    secondary: "#f5f0e8",
                    text: "#1e2d3d",
                    light: "#4d6f80",
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
            "hero-offset": {
                laptop: "140px",
                phone: {
                    default: "200px",
                    trip: "250px",
                },
            },
            screens: screens, // {phone: '0px', tablet: '600px', laptop: '900px', desktop: '1350px', wide: '1600px'}
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
            typography: ({ theme }) => ({
                brand: {
                    css: {
                        h2: {
                            color: theme("colors.brand.primary"),
                            fontWeight: "600",
                            fontSize: theme("fontSize.xl")[0],
                            lineHeight: theme("fontSize.xl")[1].lineHeight,
                            marginTop: theme("spacing.8"),
                            marginBottom: theme("spacing.4"),

                            [`@media (min-width: ${screens.laptop})`]: {
                                fontSize: theme("fontSize.2xl")[0],
                                lineHeight: theme("fontSize.2xl")[1].lineHeight,
                                marginTop: theme("spacing.10"),
                            },
                        },
                        h3: {
                            color: theme("colors.brand.primary"),
                            fontWeight: "600",
                            fontSize: theme("fontSize.base")[0],
                            lineHeight: theme("fontSize.base")[1].lineHeight,
                            marginTop: theme("spacing.6"),
                            marginBottom: theme("spacing.2"),
                            [`@media (min-width: ${screens.tablet})`]: {
                                fontSize: theme("fontSize.lg")[0],
                                lineHeight: theme("fontSize.lg")[1].lineHeight,
                            },
                        },
                        p: {
                            color: theme("colors.brand.text"),
                            fontWeight: "400",
                            fontSize: theme("fontSize.base")[0],
                            lineHeight: theme("fontSize.base")[1].lineHeight,
                            marginTop: theme("spacing.0"),
                            marginBottom: theme("spacing.2"),
                            [`@media (min-width: ${screens.tablet})`]: {
                                fontSize: theme("fontSize.lg")[0],
                                lineHeight: theme("fontSize.xl")[1].lineHeight,
                            },
                        },
                    },
                },
            }),
        },
    },
    plugins: [typography],
};
