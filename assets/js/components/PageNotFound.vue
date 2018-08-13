<template>
    <div class="container w40 w95sm text-centered">
        <p>Oops, page non trouvée ! </p>
        <font-awesome-icon v-bind:icon="frownIcon" size="8x"/>
    </div>
</template>

<script>

    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faFrown from '@fortawesome/fontawesome-free-solid/faFrown';
    import Mixin from '../mixins';

    export default {
        name: 'PageNotFound',

        data() {
            return {
                frownIcon: faFrown
            }
        },

        components: {
            FontAwesomeIcon
        },

        mixins: [Mixin],

        mounted() {
            this.cancelSpinnerAnimation();

            this.$store.dispatch('saveData', {
                data: "pathError",
                type: 'navigation'
            });
        }
    }
</script>