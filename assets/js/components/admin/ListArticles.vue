<template>
    <div class='container'>
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
                    <td>
                        <button v-on:click="deleteArticle" :data-id="article.id">Supprimer</button>
                        <router-link  v-bind:to="{name: 'editArticle', params: {id: article.id}}">
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

        name: 'ListArticles',

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