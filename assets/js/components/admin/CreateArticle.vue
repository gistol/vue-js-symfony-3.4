<template>
    <div class="container w40 w95sm">
        <form name='create_edit_article' class='tile' v-on:submit.prevent="handleCreation" :enctype="enctype">

            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" :item="i" :key='image' :image="image"></child-form>
            <category-form v-for="(category, index) in categories" :index="index" :category="category"></category-form>

            <div>
                <label for="newsletter">Envoyer la newsletter</label>
                <input type="checkbox" name='newsletter' id="newsletter"/>
            </div>

            <input type="hidden" name="csrf_token" v-bind:value="csrf_token"/>

            <button type="submit" class="button-submit">Envoyer</button>
        </form>

        <div class="button-group">
            <button v-on:click.prevent="addCategoryForm" class="button-default w150"><i class="fas fa-plus-circle"></i> Catégorie</button>
            <button v-on:click.prevent="addForm" class="button-default w150"><i class="fas fa-plus-circle"></i> Image</button>
        </div>
    </div>
</template>

<script>

    import Mixin from '../../mixins';
    import { mapState } from 'vuex';

    export default {

        name: 'CreateArticle',

        mixins: [Mixin],

        computed: mapState({
            csrf_token: (state) => state.create_edit_article_csrf_token
        }),

        mounted() {
            this.addForm();

            this.$store.dispatch('getCsrfToken', "create_edit_article");
        }
    }

</script>