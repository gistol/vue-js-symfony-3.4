<template>
    <div>
        <div class="container_flex">
            <div v-for='article in articles' class="article">
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <img v-if="index === 0" v-for="(image, index) in article.images" :src="'./images/' + image.src" :alt="image.title"/>
                    <h2>{{ article.title }}</h2>
                </router-link>

                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <button class="button-link">En savoir plus</button>
                </router-link>
            </div>
        </div>
        <div class="container">
            <button v-if="$store.state.remain" v-on:click="addArticles">Plus d'articles</button>
        </div>
    </div>
</template>

<script>

    import { mapState } from 'vuex';
    import Mixin from '../mixins';

    export default {
        name: 'Homepage',

        computed: mapState({
            articles: (state) => state.articles
        }),

        mixins: [Mixin],

        methods: {
            addArticles() {
                this.$store.dispatch('getArticles');
            }
        },

        created() {
            this.$store.dispatch('getArticles');
            this.$store.dispatch('getCsrfToken');
        },
    }
</script>