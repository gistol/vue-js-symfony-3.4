<template>
    <div class="container">
        <div v-for='article in articles' class="article">
            <h1> {{ article.title }} </h1>
            <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                <div v-if="index === 0" v-for="(image, index) in article.images">
                    <img :src="'./images/' + image.src" :alt="image.title"/>
                    <p>{{ image.content }}</p>
                </div>
                Accéder à l'article
            </router-link>
        </div>
    </div>
</template>

<style scoped>

    .container *{
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .container {
        width: 80%;
        display: flex;
        display: -webkit-flex;
        flex-wrap: wrap;
        -webkit-flex-wrap: wrap;
        margin: 0 auto;
        justify-content: space-around;
        -webkit-justify-content: space-around;
    }

    .article {
        width: 30%;
        padding: 5px;
        border: 1px solid #d8d8d8;
        margin-top: 5px;
    }

    img{
        width: 100%;
    }

</style>

<script>

    export default {
        name: 'Homepage',

        data(){
            return {
                articles: undefined,
                image: undefined
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
                    .catch((err) => {
                        console.log('Erreur : ' + err)
                    })
            }
        },

        beforeMount() {
            this.getArticles();
        },
    }
</script>