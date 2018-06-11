<template>
    <div id="app_user">
        <nav>
            <img v-if='smallDevice' v-on:click="showMenu" id="burger" src="images/threelines.png" />
            <ul id="menu">
                <router-link v-bind:to="{name: 'home_user'}" tag="li">Accueil</router-link>
                <router-link v-bind:to="{name: 'categories'}" tag="li">Catégories</router-link>
            </ul>
            <form name='search' v-on:submit.prevent="handleSearchSubmit" v-on:keyup="handleSearchSubmit">
                <input type="search" name="search" autocomplete="off" />
                <ul v-if="showSuggestionList">
                   <li v-for="title in searchResult" v-on:click="addSuggestion">{{ title.title }}</li>
                </ul>
                <input type="hidden" name="csrf_token"/>
                <button type="submit"><font-awesome-icon v-bind:icon="searchIcon" /></button>
            </form>
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
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome'
    import faSearch from '@fortawesome/fontawesome-free-solid/faSearch'

    export default {

        name: 'User',

        data() {
            return {
                formNewsletterName: 'newsletter',
                formSearchName: 'search',
                searchIcon: faSearch,
                showSuggestionList: false
            }
        },

        mixins: [Mixin, MenuMixin],

        components: {
            FontAwesomeIcon
        },

        methods: {
            addSuggestion(e) {
                this.$el.querySelector("input[name='search']").value = e.target.innerText;
                this.showSuggestionList = false;
            }
        },

        computed: mapState([
            'displayMessage',
            'message',
        ]),

        mounted() {
            this.$store.dispatch('getCsrfToken', this.formNewsletterName).then(token => {
                this.setCsrfToken(this.formNewsletterName, token)
            });

            this.$store.dispatch('getCsrfToken', this.formSearchName).then(token => {
                this.setCsrfToken(this.formSearchName, token)
            });
        }
    }
</script>