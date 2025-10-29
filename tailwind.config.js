import defaultTheme from 'tailwindcss/defaultTheme';
import screens from './resources/js/screens.js';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                tahu: ['Tahu', 'system-ui', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', 'system-ui', ...defaultTheme.fontFamily.sans],
                elite: ['SpecialElite', 'system-ui', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': '10px'
            },
            colors: {
                // Brand identity colors
                brand: {
                    primary: '#2F3E46',    // Main brand color - Donkergroengrijs
                    dark: '#1B3A4B',       // Dark variant for text/contrast
                },

                // Nature/sustainability theme colors
                nature: {
                    sage: '#AFCB98',       // Sage green - Sustainability accent
                    earth: '#DCC7AA',      // Earth/sand tone
                    terracotta: '#B17C65', // Terracotta - Warm accent
                },

                // UI accent colors
                ui: {
                    blue: '#A3BCCB',       // Soft blue - Borders, subtle accents
                    gold: '#D4A017',       // Gold - Highlights, premium feel
                },

                // Status feedback colors
                status: {
                    error: '#C5534A',      // Red for errors
                    success: '#6B8E5A',    // Green for success
                    warning: '#D4A017',    // Gold for warnings (shared with ui.gold)
                },

                // Neutral backgrounds
                neutral: {
                    25: '#FAFAFA',         // Lightest background
                    50: '#F2F4F3',         // Light background
                    100: '#E5E7E6',        // Border gray
                    200: '#CBD0CE',        // Subtle dividers
                }
            },
            screens: screens, // {phone: '0px', tablet: '700px', laptop: '900px', desktop: '1200px', wide: '1800px'}
            keyframes: {
                'slide-left-right': {
                    '0%, 100%': { transform: 'translateX(0px)' },
                    '33%': { transform: 'translateX(-25px)' },
                    '66%': { transform: 'translateX(25px)' },
                },
            },
            animation: {
                'wiggle-x': 'slide-left-right 6s ease-in-out infinite',
            },
        },
    },
    plugins: [],
};
