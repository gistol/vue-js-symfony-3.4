<template>
    <div class='container'>
        <figure v-for="image in article.images">
            <img :src="'./images/' + image.src" :alt='image.title' />
            <figcaption>{{ image.content }}</figcaption>
        </figure>
        <form v-on:submit.prevent="handleComment" enctype="multipart/form-data">

            <div>
                <label for="username">Nom</label>
                <input type="text" id="username" name="username" v-model="username"/>
            </div>

            <div>
                <label for="email">Email</label>
                <input type="text" id="email" name="email" v-model="email"/>
            </div>

            <div>
                <label for="comment">Commentaire</label>
                <input type="text" id="comment" name="comment" v-model="comment"/>
            </div>

            <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

            <input type="submit"/>

        </form>
    </div>
</template>

<script>

    import Mixin from '../mixins';

    export default {
        name: 'Article',

        data() {
            return {
                username: undefined,
                email: undefined,
                comment: undefined,
                article: undefined,
            }
        },

        computed: {
            csrf_token() {
                return this.$store.state.csrf_token;
            }
        },

        mixins: [Mixin],

        created() {
            this.getArticle(this.$route.params.slug);
            this.$store.dispatch('getCsrfToken')
        },

        methods: {
            getArticle(slug) {
                fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/article/' + slug)
                    .then((data) => data.json())
                    .then((data) => {
                        this.article = data;
                    })
                    .catch((err) => {
                        console.log('Err => ' + err)
                    })
            }
        }
    }
</script>

<style scoped>
    .container {
        width: 50%;
    }

    img{
        width: 100%;
    }

</style>