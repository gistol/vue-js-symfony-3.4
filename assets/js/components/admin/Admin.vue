<template>
    <div>
        <nav id="admin_navbar">
            <ul>
                <router-link v-bind:to="{name: 'createArticle'}" tag="li">Créer un article</router-link>
                <router-link v-bind:to="{name: 'listArticle'}" tag="li">Liste des articles</router-link>
                <router-link v-bind:to="{name: 'comments'}" tag="li">Commentaires</router-link>
                <router-link v-bind:to="{name: 'newsletter'}" tag="li">Newsletter</router-link>
                <router-link v-bind:to="{name: 'logout'}" tag="li">Déconnexion</router-link>
            </ul>
        </nav>
        <router-view></router-view>
        <div v-show='serverMessage' id="server_message"></div>
    </div>
</template>

<script>

    import Mixin from '../../mixins';

    export default {
        name: 'Admin',

        data() {
            return {
                loading: false,
                title: undefined,
                serverMessage: false
            }
        },

        mixins: [Mixin],

        beforeRouteUpdate(to, from, next) {
            if (to.name === 'logout') {
                localStorage.removeItem('token');
                next('/login')
            } else {
                next();
            }
        }
    }
</script>

