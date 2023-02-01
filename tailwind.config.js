/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: 'jit',
  purge: {
    enabled: false,
    options: {
      whitelist: [],
    }
  },
  content: [
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
    './streams/data/**/*.html',
    './streams/data/**/*.json',
    './streams/data/**/*.md',
    './streams/*.json',
    './docs/*.md',
  ],
  theme: {
    extend: {
      fontFamily: {
        'mono': ['monospace']
      }
    }
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}
