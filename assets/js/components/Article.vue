<template>
    <div>

    </div>
</template>

<script>
    export default {
        name: 'Article',

        data() {
            return {
                article: undefined
            }
        },
        created() {
            this.getArticle(this.$route.params.slug);
        },

        methods: {
            getArticle(slug) {

                fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/article/' + slug)
                    .then((data) => data.json())
                    .then((data) => {
                        console.log(data);
                    })
                    .catch((err) => {
                        console.log('Err => ' + err)
                    })
            }
        }
    }
</script>