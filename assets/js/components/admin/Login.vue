<template>
    <div class="container w40">
        <form v-on:submit.prevent="handleSubmit" enctype="multipart/form-data" :style="style">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" v-model="username"/>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"/>

            <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

            <input type="submit" class="button-submit"/>
        </form>
        <div v-show='serverMessage' id="server_message"></div>
    </div>
</template>

<script>

    import Mixin from '../../mixins';

    export default {
        name: 'Login',

        data() {
            return {
                username: undefined,
                password: undefined,
                serverMessage: false,
                divServerMessage : undefined,
                style: {
                    marginTop: '80px'
                }
            }
        },

        computed: {
            csrf_token() {
                return this.$store.state.csrf_token;
            },
        },

        mixins: [Mixin],

        methods: {

            displayMessageServer(message, redirect) {

                this.serverMessage = true;

                if (redirect) {

                    this.divServerMessage.innerText = 'Authentification réussie.';

                    let i = 3;

                    const redirectInterval = setInterval(() => {

                        if (i > 0) {
                            this.divServerMessage.innerText = 'Vous allez être redirigé(e) dans ' + i + ' seconde'.concat(i > 1 ? 's' : '');
                            --i;
                        } else {
                            clearInterval(redirectInterval);
                            this.$router.push({name: 'home_admin'});
                        }
                    }, 1000);
                } else {
                    this.divServerMessage.innerText = message;
                }
            },

            handleSubmit() {
                this.$store.dispatch('postData', {
                    url: '/vue-js-symfony-3.4/web/app_dev.php/login',
                    value: new FormData(this.$el.querySelector('form')),
                    contentType: false

                }).then((data) => {

                    if (!data.token) {
                        this.displayMessageServer(data);
                    } else  {

                        localStorage.setItem('token', data.token);
                        this.displayMessageServer(data, true);
                    }
                    
                }).catch((err) => {
                    this.displayMessageServer(err);
                });
            }
        },

        created() {
            this.$store.dispatch('getCsrfToken');
        },

        mounted() {
            this.divServerMessage = this.$el.querySelector("#server_message");
        }
    }
</script>