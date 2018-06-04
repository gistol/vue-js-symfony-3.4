<template>
    <div id="app_user">
        <nav>
            <img v-if='smallDevice' v-on:click="showMenu" id="burger" src="images/threelines.png" />
            <ul id="menu">
                <router-link v-bind:to="{name: 'home_user'}" tag="li">Accueil</router-link>
            </ul>
        </nav>
        <router-view></router-view>
        <footer>
            <form autocomplete="off" name='newsletter' v-on:submit.prevent="addToNewsletter" :enctype="enctype">
                <input type="email" v-model="newsletter" name="email" ref="newsletter" id="email" placeholder="Votre email"/>
                <input type="hidden" name="csrf_token"/>
                <input type="submit" value="Je m'abonne" class="button-submit mauto"/>
            </form>
        </footer>
        <server-message :displayMessage="displayMessage">{{ message }}</server-message>
    </div>
</template>

<script>
    import Mixin from '../mixins/index';
    import MenuMixin from '../mixins/menuMixin';
    import { mapState } from 'vuex';

    export default {

        name: 'User',

        data() {
            return {
                formName: 'newsletter'
            }
        },

        mixins: [Mixin, MenuMixin],

        computed: mapState([
            'displayMessage',
            'message',
        ]),

        mounted() {
            this.$store.dispatch('getCsrfToken', this.formName).then(token => {
                this.setCsrfToken(this.formName, token)
            });
        }
    }
</script>