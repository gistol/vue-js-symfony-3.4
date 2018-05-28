<template>
    <div class='container w40'>
        <h2>{{ articlesCount }} article(s)</h2>
        <table v-for="article in articles">
            <thead>
                <tr>
                    <th>{{ article.title }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <img v-for="(image, key) in article.images" v-if='typeof image !== undefined && key === 0' :src="'./images/' + image.src"/>
                    </td>
                </tr>
                <tr>
                    <td class="list_articles_actions">
                        <button class="button-default w150" v-on:click="deleteArticle" :data-id="article.id">Supprimer</button>
                        <router-link  v-bind:to="{name: 'editArticle', params: {id: article.id}}">
                            <button class="button-default w150">Editer</button>
                        </router-link>
                    </td>
                </tr>
            </tbody>
        </table>
        <button v-if="articlesCount > nbArticles" v-on:click="addArticles" class="button-default button-add">Plus d'articles</button>
    </div>
</template>

<script>

    import Mixin from '../../mixins';
    import { mapState } from 'vuex';

    export default {

        name: 'ListArticles',

        computed : mapState({
            articles: (state) => state.articles,
            nbArticles: (state) => state.articles.length,
            articlesCount: (state) => state.articlesCount,
        }),

        mixins: [Mixin],

        methods: {

            deleteArticle(e) {

                this.$store.dispatch('postData', {
                   url: '/vue-js-symfony-3.4/web/app_dev.php/admin/articles/delete/' + e.target.getAttribute('data-id')
                })
                .then((data) => {
                    this.$store.commit('displayServerMessage', data);
                    e.target.parentNode.parentNode.parentNode.parentNode.remove();
                })
                .catch((err) => {
                    this.$store.commit('displayServerMessage', err);
                });
            },

            addArticles() {
                this.$store.dispatch('getArticles');
            }
        },

        created() {
            this.$store.dispatch('getArticles');
            this.$store.dispatch('getNumberOfArticles');
        },
    }
</script>