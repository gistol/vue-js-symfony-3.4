<template>
    <div class="container">
        <div v-for='article in articles' class="article">
            <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                <img v-if="index === 0" v-for="(image, index) in article.images" :src="'./images/' + image.src" :alt="image.title"/>
            </router-link>

                <h2>{{ article.title }}</h2>
            <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                <button class="button-link">En savoir plus</button>
            </router-link>

        </div>
    </div>
</template>

<script>

    import { mapState } from 'vuex';
    import Mixin from '../mixins';

    export default {
        name: 'Homepage',

        data(){
            return {}
        },

        computed: mapState({
            articles: (state) => state.articles
        }),

        mixins: [Mixin],

        created() {
            this.$store.dispatch('getArticles');
            this.$store.dispatch('getCsrfToken');
        },
    }
</script>