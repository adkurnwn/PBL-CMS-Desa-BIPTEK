import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                'accent1': '#244031',
                'accent2': '#3B614A',
                'cream': '#EEEBE3',
                'semiBlack':"1E1E1E"
                
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                merriweather: ['Merriweather', ...defaultTheme.fontFamily.serif],
                'merriweather-sans': ['Merriweather Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
