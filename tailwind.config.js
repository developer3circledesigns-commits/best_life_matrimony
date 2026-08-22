import { heroui } from "@heroui/react"

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
    "./node_modules/@heroui/theme/dist/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        gurgundy: "#8b0000",
      },
    },
  },
  darkMode: "class",
  plugins: [
    heroui({
      themes: {
        light: {
          colors: {
            background: "#fdf9f1",
            foreground: "#2b1a1e",
            primary: {
              50: "#fbf3f5",
              100: "#f4e0e4",
              200: "#e7bcc5",
              300: "#d58fa0",
              400: "#b85a72",
              500: "#9a3350",
              600: "#800020",
              700: "#6b001b",
              800: "#540015",
              900: "#3d000f",
              DEFAULT: "#800020",
              foreground: "#fdf6e9",
            },
            secondary: {
              50: "#fdf8ec",
              100: "#faeecd",
              200: "#f3dc9c",
              300: "#e9c76e",
              400: "#dcb04a",
              500: "#c9a227",
              600: "#a9881e",
              700: "#8a6d19",
              800: "#6b5314",
              900: "#4c3a0e",
              DEFAULT: "#c9a227",
              foreground: "#3d2a00",
            },
            gurgundy: "#8b0000",
            focus: "#800020",
          },
        },
        dark: {
          colors: {
            background: "#170f12",
            foreground: "#f3e6d8",
            primary: {
              50: "#fdf3f5",
              100: "#f4dfe4",
              200: "#e7b9c4",
              300: "#d58b9c",
              400: "#bf5870",
              500: "#b03a56",
              600: "#a02a46",
              700: "#8a203a",
              800: "#6b1a2f",
              900: "#4d1222",
              DEFAULT: "#b03a56",
              foreground: "#ffffff",
            },
            secondary: {
              50: "#fdf8ec",
              100: "#faeecd",
              200: "#f3dc9c",
              300: "#e9c76e",
              400: "#dcb04a",
              500: "#c9a227",
              600: "#a9881e",
              700: "#8a6d19",
              800: "#6b5314",
              900: "#4c3a0e",
              DEFAULT: "#c9a227",
              foreground: "#2a1d00",
            },
            focus: "#c9a227",
          },
        },
      },
    }),
  ],
}