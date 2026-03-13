import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                background: '#fafafa',
                foreground: '#1a1a1a',
                primary: '#313e50',
                'primary-foreground': '#ffffff',
                secondary: '#5c6672',
                'secondary-foreground': '#ffffff',
                muted: '#f3f4f6',
                'muted-foreground': '#6b7280',
                accent: '#6c6f7f',
                'accent-foreground': '#ffffff',
                destructive: '#ef4444',
                'destructive-foreground': '#ffffff',
                card: '#ffffff',
                'card-foreground': '#1a1a1a',
            },
            borderRadius: {
                lg: '0.75rem',
                xl: '1rem',
            },
        },
    },

    plugins: [forms],
};
