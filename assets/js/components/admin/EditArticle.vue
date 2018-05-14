<template>
    <div class="container">
        <form v-on:submit.prevent="handleEdition" enctype="multipart/form-data">

            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <child-form v-for="(image, i) in images" v-on:decrement='decrementNbImages(i)' :item='i' :image="image"></child-form>

            <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

            <input type="submit"/>

            <button v-on:click.prevent="addForm">Ajouter une image</button>

        </form>
    </div>
</template>

<script>

    import Mixin from '../../mixins';

    export default {

        name: 'EditArticle',

        mixins: [Mixin],

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
                })
        }
    }
</script>