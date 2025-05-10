import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['Plus Jakarta Sans', 'Inter var', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
                secondary: {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#8b5cf6',
                    600: '#7c3aed',
                    700: '#6d28d9',
                    800: '#5b21b6',
                    900: '#4c1d95',
                    950: '#2e1065',
                },
                dark: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                },
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            boxShadow: {
                'soft-sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                soft: '0 2px 15px 0 rgba(0, 0, 0, 0.05)',
                'soft-md': '0 4px 20px 0 rgba(0, 0, 0, 0.05)',
                'soft-lg': '0 10px 30px 0 rgba(0, 0, 0, 0.05)',
                'soft-xl': '0 20px 50px 0 rgba(0, 0, 0, 0.05)',
                'inner-soft': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.05)',
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'slide-in-right': 'slideInRight 0.3s ease-out',
                'slide-in-left': 'slideInLeft 0.3s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: 0 },
                    '100%': { opacity: 1 },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: 0 },
                    '100%': { transform: 'translateY(0)', opacity: 1 },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-10px)', opacity: 0 },
                    '100%': { transform: 'translateY(0)', opacity: 1 },
                },
                slideInRight: {
                    '0%': { transform: 'translateX(10px)', opacity: 0 },
                    '100%': { transform: 'translateX(0)', opacity: 1 },
                },
                slideInLeft: {
                    '0%': { transform: 'translateX(-10px)', opacity: 0 },
                    '100%': { transform: 'translateX(0)', opacity: 1 },
                },
            },
            transitionProperty: {
                'height': 'height',
                'spacing': 'margin, padding',
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        a: {
                            color: theme('colors.primary.600'),
                            '&:hover': {
                                color: theme('colors.primary.500'),
                            },
                        },
                        h1: {
                            fontFamily: theme('fontFamily.display').join(', '),
                            fontWeight: '700',
                        },
                        h2: {
                            fontFamily: theme('fontFamily.display').join(', '),
                            fontWeight: '600',
                        },
                        h3: {
                            fontFamily: theme('fontFamily.display').join(', '),
                            fontWeight: '600',
                        },
                    },
                },
                dark: {
                    css: {
                        color: theme('colors.dark.200'),
                        a: {
                            color: theme('colors.primary.400'),
                            '&:hover': {
                                color: theme('colors.primary.300'),
                            },
                        },
                        strong: {
                            color: theme('colors.dark.100'),
                        },
                        h1: {
                            color: theme('colors.dark.100'),
                        },
                        h2: {
                            color: theme('colors.dark.100'),
                        },
                        h3: {
                            color: theme('colors.dark.100'),
                        },
                        h4: {
                            color: theme('colors.dark.100'),
                        },
                    },
                },
            }),
        },
    },

    plugins: [
        forms,
        typography,
        function({ addBase, theme }) {
            addBase({
                'body': {
                    fontFeatureSettings: '"cv02", "cv03", "cv04", "cv11"',
                },
                '.dark body': {
                    colorScheme: 'dark',
                },
            });
        },
    ],
};
