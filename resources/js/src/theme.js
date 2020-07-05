window.theme = () => {
    return {
        onDark:function(){
            this.clear();
            document.body.classList.add('o-theme--dark')
        },
        onLight:function(){
            this.clear();
            document.body.classList.add('o-theme--light')
        },
        clear:function(){
            document.body.classList.remove("o-theme--light","o-theme--dark")
        },
       
    }
}