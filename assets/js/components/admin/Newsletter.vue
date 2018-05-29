<template>
    <div>
        <table>
            <tbody v-for="newsletter in newsletters">
                <tr>{{ newsletter.email }}</tr>
                <tr>{{ newsletter.date|formatDate }}</tr>
            </tbody>
        </table>
    </div>
</template>

<script>

    export default {
        name: 'Newsletter',

        data() {
            return {
                newsletters: undefined
            }
        },

        filters: {
            formatDate(date) {
                return new Date(date.timestamp*1000).toLocaleDateString();
            }
        },

        methods: {
            getNewsletters() {
                fetch('/vue-js-symfony-3.4/web/app_dev.php/admin/newsletters')
                    .then(response => response.json())
                    .then(newsletters => {
                        this.newsletters = newsletters;
                    });
            }
        },

        created() {
            this.getNewsletters();
        }
    }
</script>