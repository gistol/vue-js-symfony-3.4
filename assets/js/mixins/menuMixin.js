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
                this.$el.querySelector('#menu').style.left =
                    this.menuIsDisplayed ? '-50vw' : 0;

                this.menuIsDisplayed = !this.menuIsDisplayed;
            }
        }
    },

    created() {
        this.smallDevice = window.innerWidth < 1024;
        this.menuIsDisplayed = false;
    }
}