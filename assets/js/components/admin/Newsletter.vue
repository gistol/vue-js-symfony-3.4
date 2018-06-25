<template>
    <div class="container w40 w95sm" v-if="newsletters.length > 0">
        <div class="tile">
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Date de souscription</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody v-for="newsletter in newsletters">
                    <tr>
                        <td>{{ newsletter.email }}</td>
                        <td>{{ newsletter.date|formatShortDate }} </td>
                        <td><button class="button-delete"><font-awesome-icon v-bind:icon="trashIcon"></font-awesome-icon> Supprimer</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>

    import Mixin from './../../mixins';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faTrash from '@fortawesome/fontawesome-free-solid/faTrash';

    export default {
        name: 'Newsletter',

        data() {
            return {
                newsletters: [],
                trashIcon: faTrash
            }
        },

        components: {
            FontAwesomeIcon
        },

        mixins: [Mixin],

        methods: {
            getNewsletters() {
                fetch('/admin/newsletters')
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