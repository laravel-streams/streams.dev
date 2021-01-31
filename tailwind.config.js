module.exports = {
    purge: {
        enabled: true,
        content: [
            './storage/framework/views/*.php',
            './resources/**/*.blade.php',
            './resources/**/*.js',
            './resources/**/*.vue',
            './docs/*.md',
            './vendor/streams/**/docs/*.md',
        ],
        options: {
            whitelist: [],
        }
    },

    theme: {
        extend: {},
    },
    variants: {},
    plugins: [],
}
