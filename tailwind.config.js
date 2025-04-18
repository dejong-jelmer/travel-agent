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
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': '10px'
            },
            height: {
                'screen-minus-nav': 'calc(100vh - 6rem)',
            },
            colors: {
                'deep-green': '#2F4F4F',
                'light-green': '#AFCB98',
                'ochre-yellow': '#D4A017',
                'light-beige': '#F4EDE2',
                'warm-terracotta': '#C96F53',
                'deep-blue': '#1B3A4B',
                'light-blue': '#A7C6DA',
                'background-peach': '#FAF3EB ',
                // custom
                'custom-light': '#8ecae6',
                'custom-primary': '#219ebc',
                'custom-dark': '#231942',
                'custom-secondary': '#ffb703',
                'custom-accent': '#fb8500',
                'custom-red': '#f44336'
            },
            screens: screens, // {tablet: '700px', laptop: '900px', desktop: '1200px', wide: '1800px'}
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
