import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './public/assets/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                // Horus Group signature palette: deep teal with a restrained gold accent.
                primary: {
                    50: '#EAF5F2',
                    100: '#D4ECE6',
                    200: '#A9D9CE',
                    300: '#79C2B4',
                    400: '#4BAA9A',
                    500: '#238F82',
                    600: '#14766E',
                    700: '#0D625F',
                    800: '#084B4B',
                    900: '#063638',
                    DEFAULT: '#14766E',
                    dark: '#084B4B',
                    light: '#238F82',
                },
                // Warm gold accent inspired by the construction and decor sector.
                accent: {
                    50: '#FCF8E9',
                    100: '#F7EDC7',
                    200: '#EFDB91',
                    300: '#E7C65F',
                    400: '#D8B744',
                    500: '#C8A635',
                    600: '#A98727',
                    700: '#80651F',
                    DEFAULT: '#C8A635',
                },
                // Warm neutral backgrounds for product photography and catalog screens.
                canvas: {
                    base: '#F7F8F5',
                    subtle: '#EEF2EE',
                    dark: '#E1E9E4',
                },
                surface: {
                    card: '#FFFFFF',
                    base: '#FFFFFF',
                    hover: '#F9F7F2',
                    border: '#EDE7DC',
                    subtle: '#F6F3EC',
                    champagne: '#E4E9E4',
                },
                // Deep Charcoal Typography for maximum legibility
                charcoal: {
                    heading: '#111827',
                    DEFAULT: '#1F2937',
                    secondary: '#4B5563',
                    muted: '#6B7280',
                    light: '#9CA3AF',
                },
                // Legacy compatibility tokens
                gold: {
                    champagne: '#D8B744',
                    satin: '#C8A635',
                    bronze: '#A98727',
                    dark: '#80651F',
                    DEFAULT: '#C8A635',
                },
                obsidian: {
                    DEFAULT: '#111827',
                    secondary: '#4B5563',
                    muted: '#6B7280',
                },
                espresso: {
                    main: '#111827',
                    secondary: '#4B5563',
                    muted: '#6B7280',
                },
                emerald: {
                    wa: '#25D366',
                },
                    rose: {
                    promo: '#14766E',
                },
            },
            fontFamily: {
                sans: ['Cairo', 'Alexandria', 'Readex Pro', ...defaultTheme.fontFamily.sans],
                arabic: ['Cairo', 'Alexandria', 'Readex Pro', 'sans-serif'],
                display: ['Cairo', 'Alexandria', 'sans-serif'],
                royal: ['Cairo', 'serif'],
            },
            borderRadius: {
                'card': '1rem',
                'btn': '0.75rem',
                'pill': '9999px',
            },
            boxShadow: {
                'card-soft': '0 2px 12px -2px rgba(115, 14, 31, 0.05), 0 4px 16px -2px rgba(0, 0, 0, 0.04)',
                'card-hover': '0 12px 28px -4px rgba(115, 14, 31, 0.10), 0 8px 16px -4px rgba(0, 0, 0, 0.06)',
                'maroon-glow': '0 4px 20px -2px rgba(115, 14, 31, 0.35)',
                'amber-glow': '0 4px 20px -2px rgba(217, 119, 6, 0.35)',
                'card-luxury': '0 4px 20px rgba(0, 0, 0, 0.04)',
                'gold-subtle': '0 4px 16px rgba(217, 119, 6, 0.18)',
            },
            keyframes: {
                'marquee-scroll': {
                    '0%': { transform: 'translateX(0%)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                'pulse-subtle': {
                    '0%, 100%': { transform: 'scale(1)', opacity: '1' },
                    '50%': { transform: 'scale(1.05)', opacity: '0.92' },
                },
                'float': {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-6px)' },
                },
                'glow-pulse': {
                    '0%, 100%': { boxShadow: '0 0 15px rgba(220, 38, 38, 0.4)' },
                    '50%': { boxShadow: '0 0 25px rgba(249, 115, 22, 0.8)' },
                },
                'shimmer': {
                    '100%': { transform: 'translateX(100%)' },
                }
            },
            animation: {
                'marquee': 'marquee-scroll 25s linear infinite',
                'pulse-subtle': 'pulse-subtle 2s infinite ease-in-out',
                'float': 'float 3s ease-in-out infinite',
                'glow': 'glow-pulse 2s infinite ease-in-out',
            },
        },
    },

    plugins: [forms],
};
