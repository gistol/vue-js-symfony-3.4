<template>
    <div>
        <div class="container_flex">
            <div v-for='article in articles' class="article">
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <div :style="{
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        height: '225px',
                        backgroundImage: 'url('+'./images/' + image.src +')'
                     }" v-if="index === 0" v-for="(image, index) in article.images" :alt="image.title">
                    </div>
                    <h2>{{ article.title }}</h2>
                </router-link>

                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <button class="button-link m10">En savoir plus</button>
                </router-link>
            </div>
        </div>
        <div class="container">
            <button v-if="articlesCount > nbArticles" v-on:click="addArticles" class="button-default">Plus d'articles</button>
        </div>
    </div>
</template>

<script>

    import { mapState } from 'vuex';
    import Mixin from '../mixins';

    export default {
        name: 'Homepage',

        computed : mapState({
            articles: (state) => state.articles,
            nbArticles: (state) => state.articles.length,
            articlesCount: (state) => state.articlesCount,
        }),

        mixins: [Mixin],

        methods: {
            addArticles() {
                this.$store.dispatch('getArticles');
            }
        },

        created() {
            this.$store.dispatch('getArticles');
            this.$store.dispatch('getNumberOfArticles');
            this.$store.dispatch('getCsrfToken');
        },
    }
</script>