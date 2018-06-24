<template>
    <div>
        <p>Oops, page non trouvée ! </p>
    </div>
</template>

<script>

    export default {
        name: 'PageNotFound',

        mounted() {

            this.$store.dispatch('saveData', {
                data: "pathError",
                type: 'navigation'
            });
        }
    }
</script>