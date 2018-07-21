<template>
    <div>
        <h2>Commentaires</h2>
        <div class="container w40 w95sm">
            <div v-if="length !== 0">
                <div class="tile" v-for="(comment, key) in comments">
                    <p v-bind:style="style">{{ comment.username }}, le {{ comment.date|formatShortDate }}</p>
                    <p>{{ comment.email }}</p>
                    <p>{{ comment.comment }}</p>
                    <p v-bind:style="style">Article: {{ comment.article.title }}</p>
                    <div class="button-group">
                        <button v-if="comment.published" v-on:click='updateStatus(false, comment.id, key)' class="button-delete">Masquer</button>
                        <button v-else v-on:click='updateStatus(true, comment.id, key)' class="button-default">Publier</button>
                        <button class="button-delete" v-on:click="deleteComment(comment.id)">
                            <i class="fas fa-trash-alt"></i>
                            <font-awesome-icon v-bind:icon="trashIcon" />
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
            <div class="tile" v-else>
                <p>Aucun commentaire.</p>
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
                length: 0,
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

                const req = this.ajaxRequest('GET', '/admin/comments');

                req.onload = () => {
                    if (req.status >= 200 && req.status < 400) {
                        this.$store.commit('hideMessage');
                        this.comments = JSON.parse(req.responseText);
                        this.length = Object.keys(this.comments).length;
                    }
                };

                req.send();
            },

            updateStatus(bool, id, key) {

                const reqConfig = {
                    url: "/admin/comment/status",
                    contentType: 'application/x-www-form-urlencoded',
                    value: 'published=' + bool + '&id=' + id
                };

                this.$store.dispatch('postData', reqConfig)
                    .then(() => {
                        this.comments[key].published = bool;
                    })
            },

            deleteComment(id) {
                const req = this.ajaxRequest("GET", "/admin/comment/delete/" + id);

                req.onload = () => {
                    if (req.status >= 200 && req.status < 400) {
                        this.$store.commit('displayServerMessage', req.responseText);

                        /* Removing in the comments object the object whose id corresponds to the argument (id) */
                        this.comments.splice(Object.keys(this.comments).find(key => this.comments[key] === this.comments.find(comment => comment.id === id)), 1);
                    }
                };

                req.send();
            }
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