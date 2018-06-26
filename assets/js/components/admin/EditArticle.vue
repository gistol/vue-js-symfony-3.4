<template>
    <div class="container w40 w95sm">
        <form name='create_edit_article' v-on:submit.prevent="handleEdition" v-bind:enctype="enctype" class="tile">
            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" :item="i" v-bind:image="image"></child-form>
            <category-form v-for="(category, index) in categories" v-bind:index="index" :category="category"></category-form>

            <div class="field">
                <label for="pdf">PDF</label>
                <input type="file" name="pdf" id="pdf"/>
                <font-awesome-icon v-if="pdf !== undefined" v-bind:icon="deleteIcon" v-on:click="deletePDF"/>
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
    import faTime from '@fortawesome/fontawesome-free-solid/faTimes';

    export default {

        name: 'EditArticle',

        data() {
            return {
                plusIcon: faPlusCircle,
                deleteIcon: faTime,
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
            fetch('/admin/articles/edit/' + this.$route.params.id)
                .then(data => data.json())
                .then(data => {
                    this.title = data.title;
                    if (null !== data.pdf) {
                        this.pdf = data.pdf;
                    }
                    data.images.forEach((image) => {
                        this.images.push(image);
                    });

                    data.categories.forEach(category => {
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
            this.$store.dispatch('getCsrfToken', "create_edit_article");
        }
    }
</script>