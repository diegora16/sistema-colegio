/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
      },
      colors: {
        primary: {
          DEFAULT: '#07863f',
          dark:    '#065f2e',
          darker:  '#04471e',
        },
        gold:  '#cd921c',
        cream: '#f6eac2',
      },
    },
  },
  plugins: [],
}