<template>
    <div class="container">
        <div class="comment" v-for="(comment, key) in comments.reverse()">
            <p v-bind:style="style">Date: {{ comment.date|formatShortDate }}</p>
            <p>Auteur: {{ comment.username }}</p>
            <p v-bind:style="style">Email: {{ comment.email }}</p>
            <p>Commentaire: {{ comment.comment }}</p>
            <p v-bind:style="style">Article: {{ comment.article.title }}</p>
            <p v-bind:style="style" v-if="comment.published">Publié: Oui</p>
            <p>Publié: Non</p>
            <div class="list_actions">
                <p v-if="!comment.published">
                    <button class="button-delete">Masquer</button>
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
                    background: '#fcfcfc',
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
            }
        },

        created() {
            this.$store.commit('displayWaitingForData');
            this.getComments();
        }
    }

</script>

<style scoped>

    .comment {
        padding: 5px;
        border: 1px solid rgb(163,175,183);
        margin-bottom: 10px;
    }

    p {
        padding: 5px;
        margin: 0;
    }
</style>