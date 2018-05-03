<template>
    <div>
        <form v-on:submit.prevent="handleSubmit" enctype="multipart/form-data">
            <label for="title">Titre</label>
            <input type="text" id="title" name="title" v-model="title"/>

            <label for="content">Contenu</label>
            <input type="text" id="content" name="content"/>

            <label for="image">Image</label>
            <input type="file" id="image" name="image"/>

            <input type="submit"/>
        </form>
    </div>
</template>

<script>
    export default {
        name: 'admin',

        data() {
            return {
                title: ""
            }
        },

        methods: {
            handleSubmit() {

                if (this.title.length < 3) {
                    alert("Le titre doit contenir au moins 3 caractères.");
                    return;
                }

                this.$store.dispatch('postData', {
                    url: '/vue-js-symfony-3.4/web/app_dev.php/admin/article',
                    value: new FormData(this.$el.querySelector('form')),
                    contentType: false
                })
            }
        },
    }
</script>