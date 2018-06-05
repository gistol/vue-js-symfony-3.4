<template>
    <div class="container w40 w95sm">
        <div class="tile" v-for="(comment, key) in comments">
            <p v-bind:style="style">Date: {{ comment.date|formatShortDate }}</p>
            <p>Auteur: {{ comment.username }}</p>
            <p v-bind:style="style">Email: {{ comment.email }}</p>
            <p>Commentaire: {{ comment.comment }}</p>
            <p v-bind:style="style">Article: {{ comment.article.title }}</p>
            <p v-if="comment.published">Publié: Oui</p>
            <p v-else>Publié: Non</p>
            <div class="list_actions">
                <p v-if="comment.published">
                    <button v-on:click='updateStatus(false, comment.id, key)' class="button-delete">Masquer</button>
                </p>
                <p v-else>
                    <button v-on:click='updateStatus(true, comment.id, key)' class="button-default">Publier</button>
                </p>
                <p>
                    <button class="button-default">Supprimer</button>
                </p>
            </div>
        </div>
    </div>
</template>

<script>

    import Mixin from './../../mixins';

    export default {
        name: 'Comment',

        data() {
            return {
                comments: []
            }
        },

        computed: {
            style() {
                return {
                    background: '#fafafa',
                }
            }
        },

        mixins: [Mixin],

        methods: {
            getComments() {
                fetch('/vue-js-symfony-3.4/web/app_dev.php/admin/comments')
                    .then(response => response.json())
                    .then(comments => {
                        this.$store.commit('hideMessage');
                        this.comments = comments;
                    });
            },

            updateStatus(bool, id, key) {
                this.$store.dispatch('postData', {
                    url: "/vue-js-symfony-3.4/web/app_dev.php/admin/comment/status",
                    contentType: 'application/x-www-form-urlencoded',
                    value: 'published=' + bool + '&id=' + id
                }).then(() => {
                    this.comments[key].published = bool;
                })
            },
        },

        created() {
            this.$store.commit('displayWaitingForData');
            this.getComments();
        }
    }

</script>

<style scoped>

    p {
        padding: 5px;
        margin: 0;
    }
</style>