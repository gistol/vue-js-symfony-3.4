<template>
    <div>
        <nav id="admin_navbar">
            <ul>
                <router-link v-bind:to="{name: 'createArticle'}">Créer un article</router-link>
                <router-link v-bind:to="{name: 'listArticle'}">Liste des articles</router-link>
                <router-link v-bind:to="{name: 'comments'}">Commentaires</router-link>
                <router-link v-bind:to="{name: 'newsletter'}">Newsletter</router-link>
                <a id="logout" v-on:click="logout">Déconnexion</a>
            </ul>
        </nav>
        <p v-if="loading">Chargement café</p>
        <router-view></router-view>
    </div>
</template>

<script>

    export default {
        name: 'Admin',

        data() {
            return {
                loading: false,
                title: undefined,
            }
        },

        methods: {
            logout() {
                localStorage.removeItem('token');
                this.$router.push({name: 'login'});
            }
        }
    }
</script>

