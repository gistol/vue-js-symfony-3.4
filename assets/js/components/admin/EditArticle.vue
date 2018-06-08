<template>
    <div class="container w40 w95sm">
        <form name='create_edit_article' v-on:submit.prevent="handleEdition" :enctype="enctype">
            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" :item="i" :image="image"></child-form>
            <category-form v-for="(category, index) in categories" :index="index" :category="category"></category-form>

            <input type="hidden" name="csrf_token"/>

            <input type="submit" class="button-submit"/>
        </form>
        <div class="button-group">
            <button v-on:click.prevent="addCategoryForm" class="button-default w150"><i class="fas fa-plus-circle"></i> Catégorie</button>
            <button v-on:click.prevent="addForm" class="button-default w150"><i class="fas fa-plus-circle"></i> Image</button>
        </div>
    </div>
</template>

<script>

    import Mixin from '../../mixins';

    export default {

        name: 'EditArticle',

        data() {
            return {
                formName: 'create_edit_article'
            }
        },

        mixins: [Mixin],

        computed: {
            csrf_token() {
                return this.$store.state.csrf_token;
            }
        },

        created() {
            fetch('/vue-js-symfony-3.4/web/app_dev.php/admin/articles/edit/' + this.$route.params.id)
                .then((data) => data.json())
                .then((data) => {
                    this.title = data.title;
                    data.images.forEach((image) => {
                        this.images.push(image);
                    });

                    data.categories.forEach((category) => {
                        this.categories.push(category);
                    });

                    /* Display an empty form if no form filled before */
                    if (this.images.length === 0) {
                        this.addForm();
                    }
                })
                .catch((err) => {
                    console.log(err);
                });
        },

        mounted() {
            this.$store.dispatch('getCsrfToken', this.formName)
                .then(token => {
                    this.setCsrfToken(this.formName, token);
                });
        }
    }
</script>