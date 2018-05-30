<template>
    <div class="container w40">
        <table>
            <tbody v-for="comment in comments">
                <tr>{{ comment.username }}</tr>
                <tr>{{ comment.email }}</tr>
                <tr>{{ comment.comment }}</tr>
                <tr>{{ comment.article.title }}</tr>
                <tr>{{ comment.date|formatFullDate }}</tr>
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
                comments: undefined
            }
        },

        mixins: [Mixin],

        methods: {
            getComments() {
                fetch('/vue-js-symfony-3.4/web/app_dev.php/admin/comments')
                    .then(response => response.json())
                    .then(comments => {
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