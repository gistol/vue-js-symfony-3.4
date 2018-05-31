<template>
    <div class="container w40">
        <table v-for="(comment, key) in comments">
            <tbody>
                <tr>
                    <td><b>Commentaire n°{{ key +1}}</b></td>
                </tr>
                <tr>
                    <td><b>Auteur : </b>{{ comment.username }}</td>
                </tr>
                <tr>
                    <td>
                        <b>Email : </b>{{ comment.email }}
                    </td>
                </tr>
                <tr>
                    <td><b>Commentaire : </b>{{ comment.comment }}</td>
                </tr>
                <tr>
                    <td><b>Article : </b>{{ comment.article.title }}</td>
                </tr>
                <tr>
                    <td><b>Date : </b>{{ comment.date|formatFullDate }}</td>
                </tr>
            </tbody>
        </table>
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