<template>
    <div class='container w40 w95sm'>
        <h2 v-if="articlesCount.length > 0">{{ articlesCount }} article(s)</h2>
        <div class="tile" style='margin: 30px 0;' v-for="article in articles">
            <p>{{ article.title }}</p>
            <img class='image' v-for="(image, key) in article.images" v-if='typeof image !== undefined && key === 0' :src="'./images/' + image.src"/>
            <div class="button-group">
                <router-link  v-bind:to="{name: 'editArticle', params: {id: article.id}}">
                    <button class="button-default w150"><font-awesome-icon v-bind:icon="editIcon"/>&nbsp;&nbsp;Editer</button>
                </router-link>
                <button class="button-delete w150" v-on:click="deleteArticle" v-bind:data-id="article.id"><font-awesome-icon v-bind:icon="trashIcon"></font-awesome-icon> Supprimer</button>
            </div>
        </div>
        <button v-if="articlesCount > nbArticles" v-on:click="addArticles" class="button-default m10"><font-awesome-icon v-bind:icon="plusIcon"></font-awesome-icon> Plus d'articles</button>
    </div>
</template>

<script>

    import Mixin from '../../mixins';
    import { mapState } from 'vuex';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faPlusCircle from '@fortawesome/fontawesome-free-solid/faPlusCircle';
    import faTrash from '@fortawesome/fontawesome-free-solid/faTrash';
    import faEdit from '@fortawesome/fontawesome-free-solid/faPencilAlt'

    export default {

        name: 'ListArticles',

        data() {
            return {
                trashIcon: faTrash,
                plusIcon: faPlusCircle,
                editIcon: faEdit
            }
        },

        components: {
            FontAwesomeIcon
        },

        computed : mapState({
            articles: (state) => state.articles,
            nbArticles: (state) => state.articles.length,
            articlesCount: (state) => state.articlesCount,
        }),

        mixins: [Mixin],

        methods: {

            deleteArticle(e) {

                if (confirm("Voulez-vous supprimer cet article ?")) {
                    this.$store.dispatch('postData', {
                        url: '/admin/articles/delete/' + e.target.getAttribute('data-id')
                    })
                        .then(data => {
                            this.$store.commit('displayServerMessage', data);
                            e.target.parentNode.parentNode.remove();
                        })
                        .catch((err) => {
                            this.$store.commit('displayServerMessage', err);
                        });
                }
            },

            addArticles() {
                this.$store.dispatch('getArticles');
            }
        },

        created() {
            this.$store.commit('displayWaitingForData');
            this.$store.dispatch('getArticles');
            this.$store.dispatch('getNumberOfArticles');
        },
    }
</script>

<style scoped>
    .article {
        width: 100%;
    }

</style>