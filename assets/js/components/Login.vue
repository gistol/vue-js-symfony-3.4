<template>
    <div>
        <form v-on:submit.prevent="handleSubmit" enctype="multipart/form-data">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" v-model="username"/>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password"/>

            <input type="hidden" name="csrf_token" />

            <input type="submit"/>
        </form>
    </div>
</template>

<script>

    import Mixins from '../mixins';

    export default {
        data() {
            return {
                username: undefined,
                password: undefined
            }
        },

        mixins: [Mixins],

        methods: {
            handleSubmit() {
                this.$store.dispatch('postData', {
                    url: '/vue-js-symfony-3.4/web/app_dev.php/login',
                    value: new FormData(this.$el.querySelector('form')),
                    contentType: false

                }).then((data) => {
                    if (!data.token) {
                        alert(data);
                    }

                    if (data.token) {
                        localStorage.setItem('token', data.token);
                    }

                }).catch((err) => {
                    console.log(err);
                });
            }
        },
    }
</script>
