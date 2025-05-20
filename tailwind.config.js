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
                'type1': '#F6F5F1',
                'type2': '#40483D',
                'secondary': '#DEA521',
                'semiBlack':"#1E1E1E",
                'selectedBG': '#DBE4DF',
                'selectedText': '#4C7660',
                'menuBG': '#F7F7F9',
                'menuText': '#868080',
                'dangerBG': '#F6C4CE',
                'dangerText': '#DC143C',
                'TopTableBG': '#E4E4E4',
                'TableText': '#111827',
                'greenLabel': '#D1E7DD',
                'greenLabelText': '#198754',
                'yellowLabel': '#FFF3CD',
                'yellowLabelText': '#FFC107',
                'blueLabel': '#E7F1FF',
                'blueLabelText': '#0D6EFD',
                'redLabel': 'rgba(220, 20, 60, 0.25)',
                'redLabelText': '#DC143C',
                'searchIcon': '#A0AEC0',
                'searchBorder': '#C2C2C2',
                'blueBorder': '#2563EB',

            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                merriweather: ['Merriweather', ...defaultTheme.fontFamily.serif],
                'merriweather-sans': ['Merriweather Sans', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                md: '800px', // Mengubah md dari 768px menjadi 800px
                lg: '1025px', // Mengubah lg dari 1024px menjadi 1025px
            },
            maxWidth: {
                '10xl': '1440px', 
                '12xl': '1600px', 
              },
        },
    },

    plugins: [forms],
};
