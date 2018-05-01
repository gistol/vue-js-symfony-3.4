<template>
    <p>{{ title }}</p>
</template>

<script>
    export default {
        name: 'example',
        data(){
            return {
                title: this.$store.state.title
            }
        }
    }
</script>