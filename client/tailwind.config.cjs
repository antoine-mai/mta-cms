/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./index.html",
        "./src/**/*.{js,ts,jsx,tsx}",
    ],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                "primary": "var(--color-primary)",
                "primary-hover": "var(--color-primary-hover)",
                "background-main": "var(--color-background-main)",
                "background-light": "var(--color-background-light)",
                "background-dark": "var(--color-background-dark)",
                "sidebar-slim": "var(--color-sidebar-slim)",
                "sidebar-wide": "var(--color-sidebar-wide)",
                "card-bg": "var(--color-card-bg)",
                "border-light": "var(--color-border-light)",
                "text-main": "var(--color-text-main)",
                "text-secondary": "var(--color-text-secondary)",
                "text-muted": "var(--color-text-muted)",
            },
            fontFamily: {
                "sans": ["Inter", "sans-serif"],
                "display": ["Manrope", "sans-serif"]
            },
            borderRadius: {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
        },
    },
    plugins: [],
}
