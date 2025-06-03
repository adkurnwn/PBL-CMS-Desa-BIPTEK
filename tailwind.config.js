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
                'accent1': 'var(--accent1, #244031)',
                'accent2': 'var(--accent2, #3B614A)',
                'type1': '#F6F5F1',
                'type2': '#40483D',
                'secondary': '#DEA521',
                'semiBlack':"#1E1E1E",
                'selectedBG': 'color-mix(in srgb, white 82%, var(--accent2))',
                'selectedText': 'var(--accent2, #4C7660)',
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
                'emailText': '#404DBD',
                'filterBorder': '#6B7280',

            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                merriweather: ['Merriweather', ...defaultTheme.fontFamily.serif],
                'merriweather-sans': ['Merriweather Sans', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                'sm': '640px',
                md: '800px', // Mengubah md dari 768px menjadi 800px
                lg: '1025px', // Mengubah lg dari 1024px menjadi 1025px
                xl: '1280px',
                '2xl': '1440px',
                '3xl': '1536px',
                '4xl': '1920px', // Your default design width
            },
            maxWidth: {
                '10xl': '1440px', 
                '12xl': '1600px', 
              },
            fontSize: {
                'xxs': '0.700rem', // 10px
            },
        },
    },

    plugins: [forms],
};
