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
            document.documentElement.classList.remove('o-theme--light','o-theme--dark')
            localStorage.setItem('theme', 'dark');
            document.documentElement.classList.add('o-theme--dark')
        },
        onLight() {
            document.documentElement.classList.remove('o-theme--light','o-theme--dark')
            localStorage.setItem('theme', 'light');
            document.documentElement.classList.add('o-theme--light')
        }
    }
}
