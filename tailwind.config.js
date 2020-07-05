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
      background:"var(--color-background)",
      surface:"var(--color-surface)",
      accent:"var(--color-accent)",
      font:"var(--color-font)",
    },
    extend: {},
  },
  variants: {},
  plugins: [],
}
