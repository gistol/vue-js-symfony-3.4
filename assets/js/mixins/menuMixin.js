export default {
    data() {
        return {
            smallDevice: undefined,
            menuIsDisplayed: undefined
        }
    },

    methods: {
        showMenu() {
            if (this.smallDevice) {

                if (this.menuIsDisplayed) {
                    document.querySelector('#menu').classList.remove('display_menu');
                } else {
                    document.querySelector('#menu').classList.add('display_menu');
                }

                this.menuIsDisplayed = !this.menuIsDisplayed;
            }
        },

        resetMenu() {
            let sel = document.querySelector('#menu');
            if (null !== sel) {
                document.querySelector('#menu').classList.remove('display_menu');
                this.menuIsDisplayed = false;
            }
        }
    },

    created() {
        this.smallDevice = window.innerWidth < 1024;
    },
}