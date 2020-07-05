window.theme = () => {

    return {
        init() {
            if (!localStorage.getItem('theme')) {
                this.onDark();
            } else { 
                document.documentElement.classList.remove('o-theme--light','o-theme--dark')
                document.documentElement.classList.add(`o-theme--${localStorage.getItem('theme')}`)
            }
        },
        onDark() {
            localStorage.setItem('theme', 'dark');
            document.documentElement.classList.remove('o-theme--light')
        },
        onLight() {
            localStorage.setItem('theme', 'light');
            document.documentElement.classList.add('o-theme--light')
        }
    }
}