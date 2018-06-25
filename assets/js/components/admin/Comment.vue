<template>
    <div class="container w40 w95sm">
        <div class="tile" v-for="(comment, key) in comments">
            <p v-bind:style="style">{{ comment.username }}, le {{ comment.date|formatShortDate }}</p>
            <p>{{ comment.email }}</p>
            <p>{{ comment.comment }}</p>
            <p v-bind:style="style">Article: {{ comment.article.title }}</p>
            <div class="button-group">
                <button v-if="comment.published" v-on:click='updateStatus(false, comment.id, key)' class="button-delete">Masquer</button>
                <button v-else v-on:click='updateStatus(true, comment.id, key)' class="button-default">Publier</button>
                <button class="button-delete"><i class="fas fa-trash-alt"></i><font-awesome-icon v-bind:icon="trashIcon"></font-awesome-icon> Supprimer</button>
            </div>
        </div>
    </div>
</template>

<script>

    import Mixin from './../../mixins';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faTrash from '@fortawesome/fontawesome-free-solid/faTrash';

    export default {
        name: 'Comment',

        data() {
            return {
                comments: [],
                trashIcon: faTrash
            }
        },

        components: {
            FontAwesomeIcon
        },

        computed: {
            style() {
                return {
                    borderBottom: '1px solid lightgrey',
                }
            }
        },

        mixins: [Mixin],

        methods: {
            getComments() {
                fetch('/admin/comments')
                    .then(response => response.json())
                    .then(comments => {
                        this.$store.commit('hideMessage');
                        this.comments = comments;
                    });
            },

            updateStatus(bool, id, key) {
                this.$store.dispatch('postData', {
                    url: "/admin/comment/status",
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