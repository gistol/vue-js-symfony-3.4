<template>
    <div>
        <form v-on:submit.prevent="handleSubmit" enctype="multipart/form-data">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" v-model="username"/>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"/>

            <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

            <input type="submit"/>
        </form>
    </div>
</template>

<script>

    export default {
        name: 'Login',

        data() {
            return {
                username: undefined,
                password: undefined
            }
        },

        computed: {
            csrf_token() {
                return this.$store.state.csrf_token;
            }
        },

        methods: {
            handleSubmit() {
                this.$store.dispatch('postData', {
                    url: '/vue-js-symfony-3.4/web/app_dev.php/login',
                    value: new FormData(this.$el.querySelector('form')),
                    contentType: false

                }).then((data) => {

                    if (!data.token) {
                        alert(data);
                    } else {
                        localStorage.setItem('token', data.token);
                        this.$router.push({name: 'home_admin'});
                    }
                    
                }).catch((err) => {
                    console.log(err);
                });
            }
        },

        created() {
            this.$store.dispatch('getCsrfToken');
        }
    }
</script>
