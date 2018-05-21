<template>
    <div class="container">
        <form v-on:submit.prevent="handleCreation" :enctype="enctype" :style="width">

            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" :item="i" :key='image' :image="image"></child-form>

            <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

            <input type="submit" class="button-submit"/>
            <button v-on:click.prevent="addForm" class="button-default" :style="buttonAdd">Ajouter une image</button>
        </form>
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