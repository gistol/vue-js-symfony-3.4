<template>
    <div>
        <form v-on:submit.prevent="handleCreation" enctype="multipart/form-data">

            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" :item="i" :key='image' :image="image"></child-form>

            <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

            <input type="submit"/>

        </form>

        <button v-on:click.prevent="addForm">Ajouter une image</button>
    </div>
</template>

<script>

    import Mixin from '../../mixins';

    export default {

        name: 'CreateArticle',

        mixins: [Mixin],

        computed: {
            csrf_token() {
                return this.$store.state.csrf_token;
            }
        },

        created() {
            this.addForm();
            this.$store.dispatch('getCsrfToken');
        }
    }

</script>