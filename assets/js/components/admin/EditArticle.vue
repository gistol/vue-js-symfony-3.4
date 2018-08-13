<template>
    <div class="container w40 w95sm">
        <form name='create_edit_article' v-on:submit.prevent="handleEdition" v-bind:enctype="enctype" class="tile">
            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" v-bind:item="i" v-bind:image="image"></child-form>
            <category-form v-for="(category, index) in categories" v-bind:index="index" :category="category"></category-form>

            <div class="field">
                <font-awesome-icon v-bind:icon="pdfIcon" size="lg"/>
                <label for="pdf" style="display: inline-block;">PDF</label>
                <input type="file" name="pdf" id="pdf"/>
                <button class="button-delete" v-if="pdf !== undefined">
                    <font-awesome-icon v-bind:icon="trashIcon" v-on:click="deletePDF"/>
                    Supprimer
                </button>
            </div>
            <input type="hidden" name="csrf_token" v-bind:value="csrf_token"/>

            <input type="submit" class="button-submit"/>
        </form>
        <div class="button-group">
            <button v-on:click.prevent="addCategoryForm" class="button-default w150"><font-awesome-icon :icon="plusIcon"/> Catégorie</button>
            <button v-on:click.prevent="addForm" class="button-default w150"><font-awesome-icon :icon="plusIcon"/> Image</button>
        </div>
    </div>
</template>

<script>

    import Mixin from '../../mixins';
    import { mapState } from 'vuex';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faPlusCircle from '@fortawesome/fontawesome-free-solid/faPlusCircle';
    import faTrash from '@fortawesome/fontawesome-free-solid/faTrash';
    import faPDF from '@fortawesome/fontawesome-free-solid/faFilePdf';

    export default {

        name: 'EditArticle',

        data() {
            return {
                plusIcon: faPlusCircle,
                trashIcon: faTrash,
                pdfIcon: faPDF
            }
        },

        mixins: [Mixin],

        components: {
            FontAwesomeIcon
        },

        computed: mapState({
            csrf_token: (state) => state.create_edit_article_csrf_token
        }),

        methods: {
            deletePDF() {
                const formData = new FormData();
                formData.append("pdf", this.pdf);

                this.$store.dispatch('postData', {
                        url: '/delete/pdf',
                        value: formData,
                    })
                    .then((resp) => {
                        this.$store.commit("displayServerMessage", resp);
                        this.pdf = undefined;
                    })
                    .catch((err) => {
                        console.log("Erreur : " + err);
                    });
            }
        },

        created() {

            const req = this.ajaxRequest("GET", '/admin/articles/edit/' + this.$route.params.token);

            req.onload = () => {
                if (req.status >= 200 && req.status < 400) {

                    let article = JSON.parse(req.responseText);

                    this.title = article.title;

                    if (null !== article.pdf) this.pdf = article.pdf;

                    article.images.map(image => {
                        this.images.push(image);
                    });

                    article.categories.map(category => {
                        this.categories.push(category);
                    });

                    /* Display an empty form if no form filled before */
                    if (this.images.length === 0) this.addForm();
                } else {
                    console.log('Error : ' + req.responseText);
                }
            };

            req.send();
        },

        mounted() {
            this.$store.dispatch('getCsrfToken', "create_edit_article");
        }
    }
</script>