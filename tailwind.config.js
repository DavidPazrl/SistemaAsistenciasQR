/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./views/**/*.php",
    "./*.php",
    "./src/**/*.css"
  ],
  theme: {
    extend: {
      colors: {
        primary: "#2563eb",
        danger: "#dc2626",
      },
      fontFamily: {
        title: ["Poppins", "sans-serif"],
      }
    },
  },
  plugins: [],
}
