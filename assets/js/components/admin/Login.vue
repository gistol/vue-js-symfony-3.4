<template>
    <div class="container w40 w95sm">
        <form name='login' class='tile' autocomplete="off" v-on:submit.prevent="handleSubmit" enctype="multipart/form-data" :style="style">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" v-model="username"/>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"/>

            <input type="hidden" name="csrf_token" v-bind:value="csrf_token"/>

            <input type="submit" class="button-submit"/>
        </form>
        <server-message v-bind:displayMessage="displayMessage">{{ message }}</server-message>
    </div>
</template>

<script>

    import Mixin from '../../mixins';
    import { mapState } from 'vuex';

    export default {
        name: 'Login',

        data() {
            return {
                username: undefined,
                password: undefined,
                style: {
                    marginTop: '80px'
                }
            }
        },

        computed: mapState({
            displayMessage: (state) => state.displayMessage,
            message: (state) => state.message,
            csrf_token : (state) => state.login_csrf_token
        }),

        mixins: [Mixin],

        methods: {

            handleSubmit() {

                this.$store.commit('displaySendingRequest');

                const formData = new FormData(this.$el.querySelector('form'));
                formData.append('sender', 'login');

                this.$store.dispatch('postData', {
                    url: '/login',
                    value: formData,
                    contentType: false

                }).then((data) => {

                    if (!data.token) {
                        this.$store.commit('displayServerMessage', data);
                    } else {
                        localStorage.setItem('token', data.token);
                        this.$store.commit('displayServerMessage', 'Authentification réussie.');
                        this.$router.push({name: 'home_admin'});
                    }
                    
                }).catch((err) => {
                    this.$store.commit('displayServerMessage', err);
                });
            }
        },

        mounted() {
            this.$store.dispatch('getCsrfToken', 'login');
        },
    }
</script>