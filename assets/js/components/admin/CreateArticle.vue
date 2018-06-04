<template>
    <div class="container w40 w95sm">
        <form name='create_edit_article' v-on:submit.prevent="handleCreation" :enctype="enctype">

            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" :item="i" :key='image' :image="image"></child-form>
            <category-form v-for="(category, index) in categories" :index="index" :category="category"></category-form>

            <input type="hidden" name="csrf_token"/>

            <input type="submit" class="button-submit"/>
        </form>

        <div class="list_actions">
            <button v-on:click.prevent="addCategoryForm" class="button-default">Ajouter une Category</button>
            <button v-on:click.prevent="addForm" class="button-default ml5">Ajouter une image</button>
        </div>
    </div>
</template>

<script>

    import Mixin from '../../mixins';

    export default {

        name: 'CreateArticle',

        data() {
            return {
                formName: 'create_edit_article',
            }
        },

        mixins: [Mixin],

        computed: {
            csrf_token() {
                return this.$store.state.csrf_token;
            }
        },

        mounted() {
            this.addForm();
            this.$store.dispatch('getCsrfToken', this.formName)
                .then(token => {
                    this.setCsrfToken(this.formName, token);
                });
        }
    }

</script>