<template>
    <div>
        <nav id="admin_navbar">
            <div id="burger" v-if='smallDevice' v-on:click="showMenu">
                <font-awesome-icon style="color: #fff;" v-bind:icon="barsIcon" size="lg"/>
            </div>
            <ul id="menu" v-on:click="resetMenu()"> <!-- menu not used in css but in javascript -->
                <router-link v-bind:to="{name: 'createArticle'}" tag="li">Créer un article</router-link>
                <router-link v-bind:to="{name: 'homepageAdmin'}" tag="li">Liste des articles</router-link>
                <router-link v-bind:to="{name: 'comments'}" tag="li">Commentaires</router-link>
                <router-link v-bind:to="{name: 'newsletter'}" tag="li">Newsletter</router-link>
                <router-link v-bind:to="{name: 'statistic'}" tag="li">Statistiques</router-link>
                <router-link v-bind:to="{name: 'contacts'}" tag="li">Contacts</router-link>
                <router-link v-bind:to="{name: 'legal-admin'}" tag="li">Mentions légales</router-link>
                <router-link v-bind:to="{name: 'logout'}" tag="li">Déconnexion</router-link>
            </ul>
        </nav>
        <router-view></router-view>
        <server-message v-bind:displayMessage="displayMessage">{{ message }}</server-message>
    </div>
</template>

<script>

    import Mixin from '../../mixins/index';
    import MenuMixin from '../../mixins/menuMixin';
    import { mapState } from 'vuex';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faBars from '@fortawesome/fontawesome-free-solid/faBars';

    export default {

        name: 'Admin',

        data() {
            return {
                barsIcon: faBars,
            }
        },

        mixins: [Mixin, MenuMixin],

        computed: mapState(['displayMessage', 'message']),

        components: {
            FontAwesomeIcon
        },

        beforeRouteUpdate(to, from, next) {
            if (to.name === 'logout') {
                localStorage.removeItem('token');
                next('/login')
            } else {
                next();
            }
        },
    }
</script>

