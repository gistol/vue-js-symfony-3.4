<template>
    <div id="app">
        <nav>
            <ul>
                <router-link v-bind:to="{name: 'home_user'}" tag="li">Accueil</router-link>
            </ul>
        </nav>
        <h1 v-if="this.$route.name === 'home_user'">Accueil</h1>
        <router-view></router-view>
        <footer>
            <form v-on:submit.prevent="addToNewsletter" :enctype="enctype">
                <label for="email"></label>
                <input type="email" v-model="newsletter" name="email" ref="newsletter" id="email" placeholder="Votre email"/>
                <input type="submit" value="Envoyer"/>
            </form>
        </footer>
    </div>
</template>

<script>
    import Mixin from '../mixins';

    export default {
        name: 'User',
        mixins: [Mixin]
    }
</script>