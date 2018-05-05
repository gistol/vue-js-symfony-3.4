<template>
    <div>
        <nav id="admin_navbar">
            <ul>
                <router-link v-bind:to="{name: 'createArticle'}">Créer un article</router-link>
                <router-link v-bind:to="{name: 'comments'}">Commentaires</router-link>
                <router-link v-bind:to="{name: 'newsletter'}">Newsletter</router-link>
                <a id="logout" v-on:click="logout">Déconnexion</a>
            </ul>
        </nav>
        <router-view></router-view>
    </div>
</template>

<script>

    export default {
        name: 'Admin',

        data() {
            return {
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

<style>
    #logout:hover{
        cursor: pointer;
        color: orangered;
    }
</style>

