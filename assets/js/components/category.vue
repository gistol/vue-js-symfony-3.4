<template>
    <div>
        <div class="container w95sm" v-if="this.$parent.loaded">
            <form v-on:submit.prevent="handleSubmit" class="tile w100">
                <label for="choose_category">Rechercher par catégorie</label>
                <select id='choose_category' name="category">
                    <option v-for='category in allCategories' v-bind:value="category.category">{{ category.category }}</option>
                </select>
                <input type="submit" class="button-submit" value="Envoyer"/>
            </form>
        </div>
        <div class="container_flex w95sm">
            <div v-if="loaded" v-for='article in articles' class="tile">
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <div class='image' :style="{
                            backgroundSize: 'cover',
                            backgroundPosition: 'center',
                            height: '225px',
                            backgroundImage: 'url('+'./images/' + image.src +')'
                         }" v-if="index === 0" v-for="(image, index) in article.images" :alt="image.title">
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
    export default {
        name: 'category',
        data() {
            return {
                articles: [],
                allCategories: []
            }
        },

        methods: {
            handleSubmit(e) {
                const category = e.target.elements.category.value;

                this.$parent.loadAnimation();

                fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/category/' + category)
                    .then(data => data.json())
                    .then(articles => {
                        this.articles = articles;
                        this.$parent.cancelAnimation();
                    });
            }
        },

        created() {

            /* If not coming from an article */
            if (this.$route.name === "category") {
                fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/category/' + this.$route.params.category)
                    .then(data => data.json())
                    .then(articles => {
                        this.articles = articles;
                    });
            }

            fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/categories')
                .then(data => data.json())
                .then(categories => {
                    this.allCategories = categories;
                    this.$parent.cancelAnimation();
                })
        },

        mounted() {
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