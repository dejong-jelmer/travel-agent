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
                primary: {
                    default: '#2F3E46',    // Donkergroengrijs
                    dark: '#1B3A4B',       // Donkerbluaw variant voor tekst/contrast
                },
                secondary: {
                    sage: '#AFCB98',       // Saliegroen - Duurzaamheid/natuur accent
                    stone: '#A3BCCB',      // Zachtblauw - Subtiele accenten, borders
                },
                accent: {
                    earth: '#DCC7AA',
                    gold: '#D4A017',
                    terracotta: '#B17C65',
                },
                neutral: {
                    25: '#FAFAFA',
                    50: '#F2F4F3',
                },
                status: {
                    error: '#C5534A',
                    success: '#6B8E5A',
                    warning: '#D4A017',
                }
            },
            // colors: {
            //     primary: {
            //         green: '#2F3E46', // Bosgroen - Duurzaamheid, natuur, stabiliteit
            //     },
            //     secondary: {
            //         blue: '#A3BCCB', // Zachtblauw - Betrouwbaarheid, rust
            //     },
            //     accent: {
            //         earth: '#DCC7AA', // Warm zand - Aards, avontuurlijk maar kalm
            //         yellow: '#D4A017', // Goudgeel - Warme, rijke gele diepe, maar gedempte uitstraling
            //     },
            //     background: {
            //         gray: '#F2F4F3', //	Mistgrijs -	Licht, schoon, neutraal
            //         green: '#AFCB98', // Zachtgroen - Zachte, gedempte groentint. Kalm en natuurlijk.
            //     },
            //     contrast: {
            //         blue: '#1B3A4B', // Nachtblauw	- Diepte, betrouwbaarheid
            //         pink: '#B17C65', // Koperroze - Gedempt, elegant, warm
            //     },
            // },
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
