<template>
    <div id="app_user">
        <nav>
            <ul>
                <router-link v-bind:to="{name: 'home_user'}" tag="li">Accueil</router-link>
            </ul>
        </nav>
        <router-view></router-view>
        <footer>
            <form v-on:submit.prevent="addToNewsletter" :enctype="enctype">
                <input type="email" v-model="newsletter" name="email" ref="newsletter" id="email" placeholder="Votre email"/>
                <input type="submit" value="Je m'abonne" class="button-submit mauto"/>
            </form>
        </footer>
        <server-message :displayMessage="displayMessage">{{ message }}</server-message>
    </div>
</template>

<script>
    import Mixin from '../mixins';
    import { mapState } from 'vuex';

    export default {

        name: 'User',

        mixins: [Mixin],

        computed: mapState(['displayMessage', 'message'])
    }
</script>