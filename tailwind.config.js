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
      "font-secondary":"var(--color-font-secondary)"
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
    spacing: {
      px: '1px',
      '0': '0',
      '1': '0.25rem',
      '2': '0.5rem',
      '3': '0.75rem',
      '4': '1rem',
      '5': '1.25rem',
      '6': '1.5rem',
      '8': '2rem',
      '10': '2.5rem',
      '12': '3rem',
      '16': '4rem',
      '20': '5rem',
      '24': '6rem',
      '32': '8rem',
      '40': '10rem',
      '48': '12rem',
      '56': '14rem',
      '64': '16rem',
    },
    extend: {},
  },
  variants: {},
  plugins: [],
}
