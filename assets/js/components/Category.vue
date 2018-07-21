<template>
    <div>
        <div class="container w95sm" v-if="loaded">
            <form v-on:submit.prevent="handleSubmit" class="tile w100">
                <label for="choose_category">Rechercher des recettes par catégorie</label>
                <select id='choose_category' name="category">
                    <option v-for='category in allCategories' v-bind:value="category.category">{{ category.category }}</option>
                </select>
                <input type="submit" class="button-submit" value="Rechercher"/>
            </form>
        </div>
        <div v-show="loaded" class="container_flex w95sm">
            <div v-for='article in articles' class="tile">
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <div class='image' :style="{
                            backgroundSize: 'cover',
                            backgroundPosition: 'center',
                            height: '225px',
                            backgroundImage: 'url('+'./images/' + image.src +')'
                         }" v-if="index === 0" v-for="(image, index) in article.images" v-bind:alt="image.title">
                    </div>
                </router-link>
                <h2>{{ article.title }}</h2>
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <button class="button-default m10">En savoir plus</button>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script>

    import Mixin from '../mixins'

    export default {
        name: 'category',

        data() {
            return {
                articles: [],
                allCategories: []
            }
        },

        computed: {
            loaded() {
                return this.$parent.loaded;
            }
        },

        mixins: [Mixin],

        methods: {
            handleSubmit(e) {
                const category = e.target.elements.category.value;

                /* If no category selected */
                if (category === '') return;
                
                this.$parent.loadAnimation();

                const req = this.ajaxRequest('GET', '/category/' + category);

                req.onload = () => {
                    if (req.status >= 200 && req.status < 400) {
                        this.$parent.cancelAnimation();
                        this.articles = JSON.parse(req.responseText);
                    }
                };

                req.send();
            },
        },

        mounted() {

            this.$store.dispatch('saveData', {
                data: this.$route.fullPath,
                type: 'navigation'
            });

            /* If coming from an article */
            if (this.$route.name === "category") {

                const req = this.ajaxRequest('GET', '/category/' + this.$route.params.category);

                req.onload = () => {
                    if (req.status >= 200 && req.status < 400) {
                        this.articles = JSON.parse(req.responseText);
                    }
                };

                req.send();
            }

            /* If not coming from an article */
            const req = this.ajaxRequest('GET', '/categories');

            req.onload = () => {
                if (req.status >= 200 && req.status < 400) {
                    this.allCategories = Object.values(JSON.parse(req.responseText))
                        .map(cat => Object.assign(cat, {category: cat.category.slice(0, 1).toUpperCase() + cat.category.slice(1)}));
                    this.$parent.cancelAnimation();
                }
            };

            req.send();

            /* If not aready cached */
            if (this.allCategories.length === 0) {
                this.$parent.loadAnimation();
            }
        }
    }
</script>

<style scoped>
    @media all and (min-width: 1024px) {
        .tile {
            width: 30%!important;
        }
    }

    @media all and (min-width: 768px) and (max-width: 1023px) {
        .tile {
            width: 48%!important;
        }
    }

    @media all and (max-width: 767px) {
        .tile {
            width: 95%!important;
        }
    }

</style>