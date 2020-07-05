window.theme = () => {
    return {
        onDark() {
            document.documentElement.classList.remove('o-theme--light')
        },
        onLight() {
            document.documentElement.classList.add('o-theme--light')
        }
    }
}