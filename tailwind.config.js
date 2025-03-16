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
                sans: ['Roboto', ...defaultTheme.fontFamily.sans],
            },
            height: {
                'screen-minus-nav': 'calc(100vh - 6rem)',
              },
              colors: {
                'custom-light': '#8ecae6',
                'custom-primary': '#219ebc',
                'custom-dark': '#023047',
                'custom-secondary': '#ffb703',
                'custom-accent': '#fb8500',
              },
              textShadow: {
                'default': '2px 2px 4px rgba(0, 0, 0, 0.5)',
                'lg': '4px 4px 8px rgba(0, 0, 0, 0.5)',
                'xl': '6px 6px 12px rgba(0, 0, 0, 0.5)',
              }
        },
    },
    plugins: [
    function({ addUtilities }) {
        const newUtilities = {
          '.text-shadow': {
            textShadow: '2px 2px 4px rgba(0, 0, 0, 0.5)',
          },
          '.text-shadow-lg': {
            textShadow: '4px 4px 8px rgba(0, 0, 0, 0.5)',
          },
          '.text-shadow-xl': {
            textShadow: '6px 6px 12px rgba(0, 0, 0, 0.5)',
          },
        };
        addUtilities(newUtilities);
      }
    ],
};
