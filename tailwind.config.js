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
    fontSize: {
      xs: '0.75rem',
      sm: '0.875rem',
      base: '1rem',
      h1:'3.052rem',
      h2:'2.441rem',
      h3:'1.953rem',
      h4:'1.563rem',
      h5:'1.25rem',
      h6:'1.25rem',
      
      h0:'1rem',
      
    },
    extend: {},
  },
  variants: {},
  plugins: [],
}
