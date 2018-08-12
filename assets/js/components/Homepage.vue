<template>
    <div>
        <transition-group name="fade" class="container_flex">
            <div v-for='article in articles' v-bind:key="article.id" class="tile">
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <div class='image' v-bind:style="{
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        height: '225px',
                        backgroundImage: 'url('+'./images/' + article.image_src +')'
                     }" v-bind:alt="article.slug">
                    </div>
                </router-link>
                <h2>{{ article.title }}</h2>
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <button class="button-default m10">En savoir plus</button>
                </router-link>
            </div>
        </transition-group>

        <div v-if="articles.length > 0" style="margin-bottom: 50px;">
            <button v-if="articlesCount > nbArticles" v-on:click="addArticles" class="button-submit">Plus d'articles</button>
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
            getArticles() {
                this.$store.dispatch('getArticles')
                    .then(() => {
                        this.cancelSpinnerAnimation();
                    })
                    .catch(err => {
                        console.log('Erreur : ' + err);
                    })
            },

            addArticles() {
                this.launchSpinnerAnimation();
                this.getArticles();

                setTimeout(() => {
                    window.scrollTo(0, document.body.scrollHeight + 200);
                }, 200);
            },
        },

        created() {
            this.getArticles();
            this.$store.dispatch('getNumberOfArticles');

            this.$store.dispatch('saveData', {
                data: this.$route.fullPath,
                type: 'navigation'
            });
        },

        mounted() {

            if (this.nbArticles === 0) {
                this.launchSpinnerAnimation();
            }
        }
    }
</script>

<style scoped>
    @media all and (min-width: 1024px) {
        .tile {
            width: 30%!important;
        }
    }

    @media all and (min-width: 768px) and (max-width: 1023px) {
        .tile {
            width: 48%!important;
        }
    }

    @media all and (max-width: 767px) {
        .tile {
            width: 95%!important;
        }
    }

</style>