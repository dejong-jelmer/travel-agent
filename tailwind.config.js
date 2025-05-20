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
                poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
                tahu : ['tahu', ...defaultTheme.fontFamily.sans],
                reenie: ['Reenie Beanie', 'cursive'],
            },
            fontSize: {
                '2xs': '10px'
            },
            height: {
                'screen-minus-nav': 'calc(100vh - 6rem)',
            },
            colors: {
                primary: {
                    green: '#1B3A4B', // Bosgroen - Duurzaamheid, natuur, stabiliteit
                },
                secondary: {
                    blue: '#A3BCCB', // Zachtblauw - Betrouwbaarheid, rust
                },
                accent: {
                    earth: '#DCC7AA', // Warm zand - Aards, avontuurlijk maar kalm
                },
                background: {
                    gray: '#F2F4F3', //	Mistgrijs -	Licht, schoon, neutraal
                },
                contrast: {
                    blue: '#2F3E46' , // Nachtblauw	- Diepte, betrouwbaarheid
                    pink: '#B17C65', // Koperroze - Gedempt, elegant, warm
                },
                //🎨 Voorbeeldgebruik:
                // Primair (Bosgroen): Knoppen, links, headers.
                // Secundair (Zachtblauw): Achtergronden van secties of iconen.
                // Accent (Warm zand): Call-to-action-knoppen, illustraties, hover-effecten.
                // Mistgrijs: Algemene achtergrond, whitespace.
                // Nachtblauw: Footer, titels, belangrijke nadruk.
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
