<template>
    <div id="article">
        <div v-for='article in articles'>
            <h1> {{ article.title }} </h1>
            <p> {{ article.content }} </p>
            <router-link v-bind:to="{name: 'article', params: {id: article.id} }">Accéder à l'article</router-link>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'homepage',

        data(){
            return {
                articles: undefined,
            }
        },

        methods: {
            getArticles() {
                fetch('/vue-js-symfony-3.4/web/app_dev.php/articles')
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.length > 0) {
                            this.articles = data;
                        }
                    })
            }
        },

        created() {
            this.getArticles();
        },
    }
</script>