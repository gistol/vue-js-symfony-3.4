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

<script>

    import Mixin from '../mixins';

    export default {
        name: 'Homepage',

        data(){
            return {
                articles: undefined,
                image: undefined
            }
        },

        mixins: [Mixin],

        beforeMount() {
            this.getArticles();
        },
    }
</script>