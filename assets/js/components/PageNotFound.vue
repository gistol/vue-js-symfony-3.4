<template>
    <div>
        <p>Oops, page non trouvée ! </p>
    </div>
</template>

<script>

    export default {
        name: 'PageNotFound',

        mounted() {
            console.log(this.$route.name);
        }
    }
</script>