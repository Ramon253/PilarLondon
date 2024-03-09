import {c} from "vite/dist/node/types.d-AKzkD8vd.js";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                'nav-main' : '#6da1e9'
            }
        },
    },
    plugins: [],
}

