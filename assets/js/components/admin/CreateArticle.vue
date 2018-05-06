<template>
    <div>
        <form v-on:submit.prevent="handleSubmit" enctype="multipart/form-data">

            <div>
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" v-model="title"/>
            </div>

            <div>
                <label for="image_1">Image 1</label>
                <input type="file" id="image_1" name="image"/>
            </div>

            <div>
                <label for="content_1">Contenu 1</label>
                <input type="text" id="content_1" name="content[]"/>
            </div>

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
                csrf_token: undefined
            }
        },

        methods: {

            addForm() {

                let nbInput = this.nbInput++;
                nbInput++;

                class Form {
                    constructor() {

                        const div = document.createElement('div');
                        const labelImage = document.createElement('label');
                        const labelContent = document.createElement('label');
                        const inputImage = document.createElement('input');
                        const inputContent = document.createElement('input');
                        const button = document.createElement('button');

                        labelImage.innerText = 'Image n°' + nbInput;
                        labelImage.setAttribute('for', 'image_' + nbInput);
                        labelContent.innerText = 'Contenu n°' + nbInput;
                        labelContent.setAttribute('for', 'content_' + nbInput);

                        inputImage.name = 'image[]';
                        inputImage.id = 'image_' + nbInput;
                        inputImage.type = 'file';

                        inputContent.name = 'content[]';
                        inputContent.id = 'content_' + nbInput;
                        inputContent.type = 'text';

                        button.innerText = 'Supprimer';
                        button.addEventListener('click', (e) => {
                            e.target.parentNode.remove();
                        });

                        div.appendChild(labelImage);
                        div.appendChild(inputImage);
                        div.appendChild(labelContent);
                        div.appendChild(inputContent);
                        div.appendChild(button);

                        return div;
                    }
                }

                const newForm = new Form();
                const form = this.$el.querySelector('form');
                form.insertBefore(newForm, form.lastElementChild)
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