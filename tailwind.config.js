import defaultTheme from 'tailwindcss/defaultTheme';

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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            height: {
                'screen-minus-nav': 'calc(100vh - 6rem)',
              },
              colors: {
                'custom-light': '#f9f7f3',
                'custom-primary': '#b5e2fa',
                'custom-secondary': '#0fa3b1',
                'custom-accent': '#eddea4',
                'custom-dark': '#f7a072',
              }
        },
    },
    plugins: [],
};
