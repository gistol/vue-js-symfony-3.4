<template>
    <div>
        <table>
            <tbody v-for="comment in comments">
                <tr>{{ comment.username }}</tr>
                <tr>{{ comment.email }}</tr>
                <tr>{{ comment.comment }}</tr>
                <tr>{{ comment.article.title }}</tr>
                <tr>{{ comment.date }}</tr>
            </tbody>
        </table>
    </div>
</template>

<script>

    export default {
        name: 'Comment',

        data() {
            return {
                comments: undefined
            }
        },

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
            this.getComments();
        }
    }
</script>