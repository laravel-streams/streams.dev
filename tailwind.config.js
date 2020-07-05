module.exports = {
  purge: {
    enabled:false,
    content: [
      './resources/views/**/*.html',
      './resources/js/**/*.vue',
      './resources/js/**/*.js',
    ],
    options: {
      whitelist: [],
    }
  },
    
  theme: {
    colors:{
      black:"var(--color-black)",
      white:"var(--color-white)",
      dark:"var(--color-dark)",
      light:"var(--color-light)",
      highlight:"var(--color-highlight)",
    },
    extend: {},
  },
  variants: {},
  plugins: [],
}
