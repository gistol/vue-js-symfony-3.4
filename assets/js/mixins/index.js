const Mixins = {

    data() {
        return {
            value: '',
            title: '',
            images: [],
            nbImages: 0,
            enctype: 'multipart/form-data',
            newsletter: undefined,
            newsletterFormValid: false
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
                    /* Child component src */
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

    watch: {
        newsletter(val) {
            const field = this.$refs.newsletter;
            if (!new RegExp(/^[\w.-]+@[\w.-]{2,}\.[a-z]{2,4}$/).test(val)) {
                field.style.border = "2px solid red";
            } else {
                field.style.border = "2px solid green";
                this.newsletterFormValid = true;
            }
        }
    },

    methods: {

        /* Common to CreateArticle and EditArticle components */
        addForm() {
            this.images.push(this.nbImages);
            this.nbImages++;
        },

        /* Common to CreateArticle, EditArticle and Article component (comments) */

        handleSubmit(uri) {

            const route = this.$route.name;

            if (route === "createArticle" || route === "editArticle") {
                if (this.title.length < 3) {
                    alert("Le titre doit contenir au moins 3 caractères.");
                    return;
                }
            }

            this.$store.dispatch('postData', {
                url: '/vue-js-symfony-3.4/web/app_dev.php' + uri,
                value: new FormData(this.$el.querySelector('form'))
            }).then((data) => {
                console.log(data);
            }).catch((err) => {
                console.log('Erreur : ' + err)
            });
        },

        handleCreation() {
            this.handleSubmit('/admin/create');
        },

        handleEdition() {
            this.handleSubmit('/admin/articles/edit/' + this.$route.params.id);
        },

        handleComment() {
            this.handleSubmit('/article/' + this.$route.params.slug + '/comment');
        },

        addToNewsletter() {
            if (this.newsletterFormValid) {
                this.handleSubmit('/newsletter');
            } else {
                alert("Adresse email invalide !");
            }
        }
    },
};

export default Mixins;