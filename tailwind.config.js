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
    fontFamily: {
      sans: "var(--font-body)",
      body: "var(--font-body)",
      display:"var(--font-display)",
    },
    fontSize: {
      sm:"var(--font-size-sm)",
      base:"var(--font-size-base)",
      lg:"var(--font-size-lg)",
      xl:"var(--font-size-xl)",
    },
    fontWeight: {
      normal: "var(--font-weight-normal)",
      bold: "var(--font-weight-bold)",
    },
    lineHeight: {
      none: '1',
      tight: '1.15',
      normal: '1.5',
      relaxed: '1.625',
    },
    zIndex: {
      auto: 'auto',
      '0': '0',
      '10': '10',
      '20': '20',
      '30': '30',
      '40': '40',
      '50': '50',
      '998': '998',
      '999': '999',
    },
    extend: {},
  },
  variants: {},
  plugins: [],
}
