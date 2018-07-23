<template>
    <div class="container w40 w95sm">
        <div class="tile" v-if="newsletters.length > 0">
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
                        <td><button class="button-delete" v-bind:data-id='newsletter.id' v-on:click='unsubscribe'><font-awesome-icon v-bind:icon="trashIcon"></font-awesome-icon> Supprimer</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="tile" v-else>
            <p>Aucun abonné.</p>
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

                const req = this.ajaxRequest('GET', '/admin/newsletters');

                req.onload = () => {
                    if (req.status >= 200 && req.status < 400) {
                        this.$store.commit('hideMessage');
                        this.newsletters = JSON.parse(req.responseText);
                    }
                };

                req.send();
            },

            unsubscribe(e) {

                this.$store.dispatch('postData', {
                    url: "/newsletter/remove/" + e.target.getAttribute('data-id') + "/" + localStorage.getItem('token')
                }).then(resp => {
                    this.$store.commit('displayServerMessage', resp);
                    e.target.parentNode.parentNode.remove();
                }).catch(err => {
                    console.log('Error : ' + err);
                });
            }
        },

        created() {
            this.$store.commit('displayWaitingForData');
            this.getNewsletters();
        }
    }
</script>