const Mixins = {

    data() {
        return {
            value: '',
            title: '',
            images: [],
            nbImages: 0
        }
    },

    components: {
        'child-form': {
            template:
            "<div>" +
            "<label for='image'>Image {{ item + 1 }}</label>" +
            "<img v-if='this.$props.image.src !== undefined' :src='src' />" +
            "<input type='file'/>" +
            "<label>Contenu {{ item + 1 }}</label>" +
            "<input type='text' :value='image.content'/>" +
            "<button v-on:click='remove'>Supprimer</button>" +
            "</div>",

            data() {
                return {
                    src: './images/' + this.$props.image.src
                }
            },

            props: ['item', 'image'],

            mounted() {
                this.$el.querySelector('input[type="file"]').setAttribute('name', 'image[' + (this.item) + ']');
                this.$el.querySelector('input[type="text"]').setAttribute('name', 'content[' + (this.item) + ']');
            },

            methods: {
                remove(e) {
                    this.$emit('decrement');
                    e.target.parentNode.remove();
                }
            }
        },
    },

    methods: {

        /* Common to CreateArticle and EditArticle components */

        addForm() {
            this.images.push(this.nbImages);
            this.nbImages++;
        },

        /* Common to CreateArticle and EditArticle components */

        handleSubmit(uri) {

            if (this.title.length < 3) {
                alert("Le titre doit contenir au moins 3 caractères.");
            } else {
                this.$store.dispatch('postData', {
                    url: '/vue-js-symfony-3.4/web/app_dev.php/admin/' + uri,
                    value: new FormData(this.$el.querySelector('form'))
                }).then((data) => {
                    console.log(data);
                }).catch((err) => {
                    console.log('Erreur : ' + err)
                });

            }
        },

        handleCreation() {
            this.handleSubmit('create');
        },

        handleEdition() {
            this.handleSubmit('articles/edit/' + this.$route.params.id);
        },
    },
};

export default Mixins;