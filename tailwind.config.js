module.exports = {
    purge: {
        enabled: false,
        content: [
            './storage/framework/views/*.php',
            './resources/**/*.blade.php',
            './docs/*.md',
            './vendor/streams/**/docs/*.md',
        ],
        options: {
            whitelist: [],
        }
    },
    plugins: [
        require("@tailwindcss/forms")({
            strategy: 'base', // only generate global styles
            strategy: 'class', // only generate classes
        })
    ]
}
