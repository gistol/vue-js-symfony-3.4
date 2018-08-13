<template>
    <div id="app_user">
        <nav>
            <div id="burger" v-if='smallDevice' v-on:click="showMenu">
                <font-awesome-icon style="color: #fff;" v-bind:icon="barsIcon" size="lg"/>
            </div>
            <ul id="menu" v-on:click="resetMenu()">
                <router-link v-bind:to="{name: 'home_user'}" tag="li" >Accueil</router-link>
                <router-link v-bind:to="{name: 'categories'}" tag="li" >Catégories</router-link>
                <router-link v-bind:to="{name: 'contact'}" tag="li">Contact</router-link>
            </ul>
            <form name='search' v-on:submit.prevent="handleSearchSubmit" v-on:keyup="handleSearchSubmit">
                <input type="search" name="search" autocomplete="off" placeholder="Rechercher"/>
                <font-awesome-icon v-bind:icon="spinnerIcon" class="search-form-spinner" style="color: #fff;" v-show="showSpinner"/>
                <ul v-if="showSuggestionList">
                   <li v-for="title in searchResult" v-on:click="addSuggestion">{{ title.title }}</li>
                </ul>
                <input type="hidden" name="csrf_token" v-bind:value="search_csrf_token"/>
                <button v-if="!showSpinner" type="submit"><font-awesome-icon v-bind:icon="searchIcon" style="color: #fff;"/></button>
            </form>
        </nav>
        <router-view></router-view>
        <div id="loaderContainer" v-show="loading">
            <div id="loader">
                <font-awesome-icon v-bind:icon="spinnerIcon" class="fa-2x loading-page-spinner"/>
            </div>
        </div>
        <footer>
            <form autocomplete="off" name='newsletter' v-on:submit.prevent="addToNewsletter" :enctype="enctype">
                <input type="email" v-model="newsletter" name="email" id="email" placeholder="Votre email"/>
                <input type="hidden" name="csrf_token" v-bind:value="newsletter_csrf_token"/>
                <input type="submit" value="Je m'abonne" class="button-submit"/>
            </form>
            <div id="legal_mentions">
                &copy; 2018 Côté Desserts |  &nbsp;<router-link v-bind:to="{name: 'legal'}">Mentions légales</router-link>
            </div>
        </footer>
        <server-message v-bind:displayMessage="displayMessage">{{ message }}</server-message>
    </div>
</template>

<script>

    import Mixin from '../mixins/index';
    import MenuMixin from '../mixins/menuMixin';
    import { mapState } from 'vuex';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome'
    import faSearch from '@fortawesome/fontawesome-free-solid/faSearch'
    import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner'
    import faBars from '@fortawesome/fontawesome-free-solid/faBars'

    export default {

        name: 'User',

        data() {
            return {
                searchIcon: faSearch,
                spinnerIcon: faSpinner,
                barsIcon: faBars,
                showSuggestionList: false,
                showSpinner: false,
                animation: undefined,
                animation2: undefined,
                counter: 0,
                index: 0,
                loaders: [],
                loading: true,
                loaded: false,
            }
        },

        mixins: [Mixin, MenuMixin],

        components: {
            FontAwesomeIcon
        },

        methods: {
            addSuggestion(e) {
                if (e.target.innerText !== "Aucune suggestion") {
                    this.$el.querySelector("input[name='search']").value = e.target.innerText;
                }
                this.showSuggestionList = false;
            },
        },

        computed: mapState({
            displayMessage: (state) => state.displayMessage,
            message: (state) => state.message,
            search_csrf_token: (state) => state.search_csrf_token,
            newsletter_csrf_token: (state) => state.newsletter_csrf_token,
        }),

        watch: {

            loaded(val){

                /* Must check whether true as setting back to false would execute code below */
                if(val) {
                    this.$store.dispatch('getCsrfToken', 'newsletter');
                    this.$store.dispatch('getCsrfToken', 'search');
                }
            },
        },
    }
</script>