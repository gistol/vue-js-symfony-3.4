<template>
    <div>
        <div class="container w40 w95sm" v-if="loaded">
            <form v-on:submit.prevent="handleSubmit" class="tile w100">
                <label for="choose_category">Rechercher par catégorie</label>
                <select id='choose_category' name="category">
                    <option v-for='category in allCategories' v-bind:value="category.category">{{ category.category }}</option>
                </select>
                <input type="submit" class="button-submit" value="Envoyer"/>
            </form>
        </div>
        <div class="container_flex w40 w95sm">
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
                loaded: false,
                articles: [],
                allCategories: []
            }
        },

        methods: {
            handleSubmit(e) {
                const category = e.target.elements.category.value;

                fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/category/' + category)
                    .then(data => data.json())
                    .then(articles => {
                        this.articles = articles;
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
                        this.loaded = true;
                    });
            }

            fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/categories')
                .then(data => data.json())
                .then(categories => {
                    this.allCategories = categories;
                    this.loaded = true;
                })
        }
    }
</script>