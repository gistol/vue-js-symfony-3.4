<template>
    <div>
        <form v-on:submit.prevent="handleSubmit" enctype="multipart/form-data">

            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <div>
                <label for="image_1">Image 1</label>
                <input type="file" id="image_1" name="image[]"/>
            </div>

            <div>
                <label for="content_1">Contenu 1</label>
                <input type="text" id="content_1" name="content[]"/>
            </div>

            <child-form v-for="(item, key) in children" :item="item"></child-form>

            <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

            <input type="submit"/>

        </form>

        <button v-on:click.prevent="addForm">Ajouter une image</button>
    </div>
</template>

<script>

    export default {
        name: 'CreateArticle',

        data() {
            return {
                title: '',
                nbInput: undefined,
                csrf_token: undefined,
                children: []
            }
        },

        components: {
            'child-form': {
                template:
                "<div>" +
                    "<label for='image'>Image {{ item }}</label>" +
                    "<input type='file' name='image[]'/>" +
                    "<label>Contenu {{ item }}</label>" +
                    "<input type='text' name='content[]'/>" +
                    "<button v-on:click='remove'>Supprimer</button>" +
                "</div>",
                props: ['item'],
                methods: {
                    remove(e) {
                        e.target.parentNode.remove();
                    }
                }
            },
        },

        methods: {

            addForm() {
                this.nbInput++;
                this.children.push(this.nbInput);
            },

            handleSubmit() {

                if (this.title.length < 3) {
                    alert("Le titre doit contenir au moins 3 caractères.");
                } else {
                    this.$store.dispatch('postData', {
                        url: '/vue-js-symfony-3.4/web/app_dev.php/admin/create',
                        value: new FormData(this.$el.querySelector('form'))
                    }).then((data) => {
                        console.log(data);
                    }).catch((err) => {
                        console.log('Erreur : ' + err)
                    });

                }
            },
        },

        created() {
            this.$store.dispatch('getCsrfToken').then((data) => {
                this.csrf_token = data
            });
        },

        mounted() {
            this.nbInput = this.$el.querySelectorAll('input[name="content[]"').length;
        }
    }

</script>