/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/backend/merchant_panel/**/*.blade.php',
        './resources/js/merchant/**/*.{jsx,js}',
    ],
    theme: {
        extend: {
            colors: {
                wc: {
                    primary: '#059669',
                    'primary-dark': '#047857',
                    'primary-soft': '#ecfdf5',
                    'primary-faint': '#f0fdf4',
                    ink: '#111827',
                    'ink-2': '#334155',
                    muted: '#64748b',
                    'muted-2': '#94a3b8',
                    border: '#e7ebe9',
                    'border-strong': '#dce3df',
                    bg: '#f6f8f7',
                    surface: '#ffffff',
                    'surface-soft': '#fafcfb',
                    danger: '#dc2626',
                    'danger-soft': '#fef2f2',
                    warning: '#d97706',
                    'warning-soft': '#fffbeb',
                    info: '#2563eb',
                    'info-soft': '#eff6ff',
                    success: '#059669',
                    'success-soft': '#ecfdf5',
                    violet: '#7c3aed',
                    'violet-soft': '#f5f3ff',
                },
            },
            fontFamily: {
                sans: ['Inter', 'Nunito', 'system-ui', 'sans-serif'],
            },
            borderRadius: {
                wc: '14px',
                'wc-sm': '10px',
                'wc-xs': '8px',
            },
            boxShadow: {
                wc: '0 1px 2px rgba(16,24,40,.04), 0 1px 3px rgba(16,24,40,.06)',
                'wc-lg': '0 10px 30px rgba(16,24,40,.12)',
                'wc-hover': '0 8px 24px rgba(5,150,105,.14)',
            },
            keyframes: {
                wcFadeUp: {
                    'from': { opacity: '0', transform: 'translateY(12px)' },
                    'to': { opacity: '1', transform: 'translateY(0)' },
                },
                wcFloat: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-6px)' },
                },
                wcRowIn: {
                    'from': { opacity: '0', transform: 'translateY(-6px)' },
                    'to': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                wcFadeUp: 'wcFadeUp .45s cubic-bezier(.4,0,.2,1) both',
                wcFloat: 'wcFloat 3s ease-in-out infinite',
                wcRowIn: 'wcRowIn .3s ease both',
            },
        },
    },
    plugins: [],
};