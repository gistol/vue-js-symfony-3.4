<template>
    <div class="container w40 w95sm">
        <div class="tile mt5" v-for="(contact, key) in contacts">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <b>Le {{ contact.date|formatDate }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td><b>De : </b>{{ contact.email }}</td>
                    </tr>
                    <tr>
                        <td>{{ contact.message }}</td>
                    </tr>
                </tbody>
            </table>
            <button class="button-delete mauto" v-on:click="deleteContact(contact.token, key)">
                <font-awesome-icon v-bind:icon="faTrashIcon"/>
                &nbsp;Supprimer
            </button>
        </div>
    </div>
</template>
<script>

    import Mixin from '../../mixins';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faTrash from '@fortawesome/fontawesome-free-solid/faTrash';

    export default {

        name: 'contact',

        data() {
            return {
                contacts: [],
                faTrashIcon: faTrash
            }
        },

        mixins: [Mixin],

        filters: {
            formatDate(date) {
                return new Date(date.timestamp*1000).toLocaleDateString();
            }
        },

        components: {
            FontAwesomeIcon
        },

        methods: {
            deleteContact(token, key) {
                const req = this.ajaxRequest("GET", "/admin/contact/delete/" + token);

                req.onload = () => {
                    if (req.status >= 200 && req.status < 400) {
                        this.$store.commit('displayServerMessage', req.responseText);

                        /* Removing in the comments object the object whose id corresponds to the argument (id) */
                        this.contacts.splice(key);
                    }
                };

                req.send();
            }
        },

        created() {

            const req = this.ajaxRequest('GET', '/admin/contacts');

            req.onload = () => {
                if (req.status >= 200 && req.status < 400) {
                    this.contacts = JSON.parse(req.responseText);
                }
            };

            req.send();
        }
    }
</script>