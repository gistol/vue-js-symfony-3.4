<template>
    <div class='container'>
        <table v-for="article in articles">
            <tbody>
                <tr>
                    <td> {{ article.title }} </td>
                </tr>
                <tr>
                    <td>
                        <img :src="'./images/' + article.images[0].src"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <button v-on:click="deleteArticle" :data-id="article.id">Supprimer</button>
                        <router-link  v-bind:to="{name: 'editArticle', params: {slug: article.slug}}">
                            <button>Editer</button>
                        </router-link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

    import Mixin from '../../mixins'

    export default {
        data() {
            return {
                articles: undefined
            }
        },

        mixins: [Mixin],

        methods: {
            deleteArticle(e) {

                this.$store.dispatch('postData', {
                   url: '/vue-js-symfony-3.4/web/app_dev.php/admin/articles/delete/' + e.target.getAttribute('data-id')
                })
                .then((data) => {
                    console.log(data);
                    e.target.parentNode.parentNode.parentNode.parentNode.remove();
                })
                .catch((err) => {
                    console.log("Erreur : " + err);
                });
            }
        },

        beforeMount() {
            this.getArticles();
        },
    }
</script>