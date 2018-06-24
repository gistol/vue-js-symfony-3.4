<template>
    <div id="app_user">
        <nav>
            <img v-if='smallDevice' v-on:click="showMenu" id="burger" src="images/threelines.png" />
            <ul id="menu">
                <router-link v-bind:to="{name: 'home_user'}" tag="li">Accueil</router-link>
                <router-link v-bind:to="{name: 'categories'}" tag="li">Catégories</router-link>
            </ul>
            <form name='search' v-on:submit.prevent="handleSearchSubmit" v-on:keyup="handleSearchSubmit">
                <input type="search" name="search" autocomplete="off" placeholder="Rechercher"/>
                <font-awesome-icon :icon="spinnerIcon" style="color: #fff;" v-show="showSpinner"/>
                <ul v-if="showSuggestionList">
                   <li v-for="title in searchResult" v-on:click="addSuggestion">{{ title.title }}</li>
                </ul>
                <input type="hidden" name="csrf_token" v-bind:value="search_csrf_token"/>
                <button type="submit"><font-awesome-icon v-bind:icon="searchIcon" style="color: #fff;"/></button>
            </form>
        </nav>
        <router-view></router-view>
        <div id="loaderContainer" v-show="this.loading">
            <div id="loader">
                <div id="loader2"></div>
                <div id="loader3"></div>
                <div id="loader4"></div>
                <div id="loader5"></div>
            </div>
            <p>Chargement en cours</p>
        </div>
        <footer>
            <form autocomplete="off" name='newsletter' v-on:submit.prevent="addToNewsletter" :enctype="enctype">
                <input type="email" v-model="newsletter" name="email" ref="newsletter" id="email" placeholder="Votre email"/>
                <input type="hidden" name="csrf_token" v-bind:value="newsletter_csrf_token"/>
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
    import faSpinner from '@fortawesome/fontawesome-free-solid/faSpinner'

    export default {

        name: 'User',

        data() {
            return {
                searchIcon: faSearch,
                spinnerIcon: faSpinner,
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

            moveToTop() {
                this.counter += 5;

                if(this.counter <= 40){
                    this.loaders[this.index].style.bottom = "" + this.counter + "px";
                    this.animation = requestAnimationFrame(this.moveToTop);
                }
                else{
                    ++this.index;
                    this.animation2 = requestAnimationFrame(this.moveToBottom);
                }
            },

            moveToBottom() {
                this.counter -= 5;

                if(this.counter >= 0){
                    this.loaders[this.index-1].style.bottom = "" + this.counter + "px";
                    this.animation2 = requestAnimationFrame(this.moveToBottom);
                } else{
                    if(this.index === 4){
                        this.index = 0
                    }
                    this.animation = requestAnimationFrame(this.moveToTop);
                }
            },

            loadAnimation() {

                this.loading = true;

                let requestAnimationFrame =
                    window.requestAnimationFrame ||
                    window.webkitRequestAnimationFrame;

                /* Elements inside the loader square */
                for(let i = 2; i < 6; ++i){
                    this.loaders.push(document.getElementById("loader" + i + ""));
                }

                this.animation = requestAnimationFrame(this.moveToTop);
            },

            cancelAnimation() {
                let cancelAnimationFrame =
                    window.cancelAnimationFrame ||
                    window.webkitCancelAnimationFrame;

                cancelAnimationFrame(this.animation);
                cancelAnimationFrame(this.animation2);

                /* For next request */
                this.loaded = true;
                this.loading = false;
                this.index = 0;

                this.loaders.forEach((loader) => {
                    loader.style.bottom = '0';
                });
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

        mounted() {

            // this.$store.dispatch('saveData', this.$route.name)
        }
    }
</script>