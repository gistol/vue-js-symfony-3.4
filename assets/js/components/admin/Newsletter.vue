<template>
    <div class="container w40" v-if="newsletters.length > 0">
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Date de souscription</th>
                </tr>
            </thead>
            <tbody v-for="newsletter in newsletters">
                <tr>
                    <td>{{ newsletter.email }}</td>
                    <td>{{ newsletter.date|formatShortDate }} </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

    import Mixin from './../../mixins';

    export default {
        name: 'Newsletter',

        data() {
            return {
                newsletters: []
            }
        },

        mixins: [Mixin],

        methods: {
            getNewsletters() {
                fetch('/vue-js-symfony-3.4/web/app_dev.php/admin/newsletters')
                    .then(response => response.json())
                    .then(newsletters => {
                        this.$store.commit('hideMessage');
                        this.newsletters = newsletters;
                    });
            }
        },

        created() {
            this.$store.commit('displayWaitingForData');
            this.getNewsletters();
        }
    }
</script>