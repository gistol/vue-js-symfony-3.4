<template>
    <div class="container w40">
        <form v-on:submit.prevent="handleEdition" :enctype="enctype">
            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" :item="i" :image="image"></child-form>

            <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

            <input type="submit" class="button-submit"/>

            <button v-on:click.prevent="addForm" class="button-default button-add">Ajouter une image</button>
        </form>
    </div>
</template>

<script>

    import Mixin from '../../mixins';

    export default {

        name: 'EditArticle',

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
                    if (this.images.length === 0) {
                        this.addForm();
                    }
                })
                .catch((err) => {
                    console.log(err);
                });

            this.$store.dispatch('getCsrfToken');
        }
    }
</script>