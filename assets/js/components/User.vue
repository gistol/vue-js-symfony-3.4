<template>
    <div id="app">
        <nav>
            <ul>
                <router-link v-bind:to="{name: 'home_user'}" tag="li">Accueil</router-link>
            </ul>
        </nav>
        <h1 v-if="this.$route.name === 'home_user'">Accueil</h1>
        <router-view></router-view>
    </div>
</template>

<script>
    export default {
        name: 'User'
    }
</script>