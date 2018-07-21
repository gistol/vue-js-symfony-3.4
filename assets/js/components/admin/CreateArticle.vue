<template>
    <div class="container w40 w95sm">
        <form name='create_edit_article' class='tile' v-on:submit.prevent="handleCreation" :enctype="enctype">

            <div class="field">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" :item="i" :key='image' :image="image"></child-form>
            <category-form v-for="(category, index) in categories" :index="index" :category="category"></category-form>

            <div class="field">
                <font-awesome-icon v-bind:icon="pdfIcon"></font-awesome-icon>
                <label for="pdf">PDF</label>
                <input type="file" name="pdf" id="pdf"/>
            </div>

            <div class="field">
                <label for="newsletter">Envoyer la newsletter</label>
                <input type="checkbox" name='newsletter' id="newsletter"/>
            </div>

            <input type="hidden" name="csrf_token" v-bind:value="csrf_token"/>

            <button type="submit" class="button-submit">Envoyer</button>
        </form>

        <div class="button-group">
            <button v-on:click.prevent="addCategoryForm" class="button-default w150"><font-awesome-icon v-bind:icon='plusIcon'></font-awesome-icon> Catégorie</button>
            <button v-on:click.prevent="addForm" class="button-default w150"><font-awesome-icon v-bind:icon='plusIcon'></font-awesome-icon> Image</button>
        </div>
    </div>
</template>

<script>

    import Mixin from '../../mixins';
    import { mapState } from 'vuex';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faPlus from '@fortawesome/fontawesome-free-solid/faPlusCircle';
    import faPDF from '@fortawesome/fontawesome-free-solid/faFilePdf';

    export default {

        name: 'CreateArticle',

        mixins: [Mixin],

        data() {
            return {
                plusIcon: faPlus,
                pdfIcon: faPDF,
            }
        },

        components: {
            FontAwesomeIcon
        },

        computed: mapState({
            csrf_token: (state) => state.create_edit_article_csrf_token
        }),

        mounted() {
            this.addForm();

            this.$store.dispatch('getCsrfToken', "create_edit_article");
        }
    }

</script>