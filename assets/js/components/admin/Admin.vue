<template>
    <div>
        <nav id="admin_navbar">
            <img v-if='smallDevice' v-on:click='showMenu' id="burger" src="images/threelines.png" />
            <ul id="menu">
                <router-link v-bind:to="{name: 'createArticle'}" tag="li">Créer un article</router-link>
                <router-link v-bind:to="{name: 'listArticle'}" tag="li">Liste des articles</router-link>
                <router-link v-bind:to="{name: 'comments'}" tag="li">Commentaires</router-link>
                <router-link v-bind:to="{name: 'newsletter'}" tag="li">Newsletter</router-link>
                <router-link v-bind:to="{name: 'statistic'}" tag="li">Statistiques</router-link>
                <router-link v-bind:to="{name: 'logout'}" tag="li">Déconnexion</router-link>
            </ul>
        </nav>
        <router-view></router-view>
        <server-message :displayMessage="displayMessage">{{ message }}</server-message>
    </div>
</template>

<script>

    import Mixin from '../../mixins/index';
    import MenuMixin from '../../mixins/menuMixin';
    import { mapState } from 'vuex';

    export default {

        name: 'Admin',

        mixins: [Mixin, MenuMixin],

        computed: mapState(['displayMessage', 'message']),

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

