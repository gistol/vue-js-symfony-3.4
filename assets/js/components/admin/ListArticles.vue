<template>
    <div class='container w40'>
        <table v-for="article in articles">
            <tbody>
                <tr>
                    <td> {{ article.title }} </td>
                </tr>
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
        <button v-on:click="addArticles" class="button-default button-add">Plus d'articles</button>
    </div>
</template>

<script>

    import Mixin from '../../mixins';

    export default {

        name: 'ListArticles',

        computed: {
            articles() {
                return this.$store.state.articles;
            }
        },

        Mixins: [Mixin],

        methods: {
            deleteArticle(e) {

                this.$store.dispatch('postData', {
                   url: '/vue-js-symfony-3.4/web/app_dev.php/admin/articles/delete/' + e.target.getAttribute('data-id')
                })
                .then((data) => {
                    this.displayServerMessage(displayServerMessage("L'article a été supprimé !");
                    e.target.parentNode.parentNode.parentNode.parentNode.remove();
                })
                .catch((err) => {
                    this.displayServerMessage('Erreur : ' + err);
                });
            },

            addArticles() {
                this.$store.dispatch('getArticles');
            }
        },

        beforeRouteUpdate(to, next, from) {
            this.$store.state.loading = true
        },

        created() {
            this.$store.dispatch('getArticles');
        },
    }
</script>