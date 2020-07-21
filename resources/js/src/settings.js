window.settings = () => {

    return {

        show: false,
        doc: false,
        wrapper: false,

        init() {
            this.doc = document.querySelector('.o-doc');
            this.wrapper = document.documentElement;
        },
        
        onClick() {
            if (!this.doc || !this.wrapper) return;
            const dis = this;

            if (this.show === false) {
                this.wrapper.classList.add('o-html--show-settings-menu');
                window.addEventListener('transitionend', function onShowSettingsMenu(e) {
                    if (e.target.classList.contains('o-doc')) {
                        window.removeEventListener('transitionend', onShowSettingsMenu);
                        dis.show = !dis.show;
                    }
                })
            } else {
                this.wrapper.classList.remove('o-html--show-settings-menu');
                window.addEventListener('transitionend', function onHideSettingsMenu(e) {
                    if (e.target.classList.contains('o-doc')) {
                        window.removeEventListener('transitionend', onHideSettingsMenu)
                        dis.show = !dis.show;
                    }
                })
            }
        },
    }
}