<template>
    <div class="container w40 w95sm text-centered">
        <p>Oops, page non trouvée ! </p>
        <font-awesome-icon v-bind:icon="frownIcon" size="6x"/>
    </div>
</template>

<script>

    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faFrown from '@fortawesome/fontawesome-free-solid/faFrown';

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

        mounted() {

            this.$store.dispatch('saveData', {
                data: "pathError",
                type: 'navigation'
            });
        }
    }
</script>