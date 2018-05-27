const Mixins = {

    data() {
        return {
            value: '',
            title: '',
            images: [],
            nbImages: 0,
            enctype: 'multipart/form-data',
            newsletter: undefined,
            newsletterFormValid: false,
        }
    },

    components: {
        'server-message': {
            template: "<div :style='style' v-show='displayMessage'><slot></slot></div>",

            props: ['displayMessage'],

            computed: {
                style() {
                    return {
                        padding: '20px',
                        position: 'fixed',
                        top: '60px',
                        left: '0',
                        background: 'rgba(0, 0, 0, .8)',
                        color: '#fff'
                    }
                }
            },
        },

        'child-form': {
            template:
            "<div style='margin: 10px auto;'>" +
            "<label for='image'>Image {{ item + 1 }}</label>" +
            "<img v-if='this.$props.image.src !== undefined' v-bind:src='src'/>" +
            "<img v-bind:src='preview'/>" +
            "<input type='file' v-on:change='loadFile'/>" +
            "<label>Contenu {{ item + 1 }}</label>" +
            "<textarea :value='image.content' :style='textAreaH'></textarea>" +
            "<button v-on:click='remove' class='button-delete'>Supprimer</button>" +
            "</div>",

            data() {
                return {
                    /* Child component src */
                    src: './images/' + this.$props.image.src,
                    preview: undefined,
                    textAreaH: {
                        height: '150px',
                        resize: 'none'
                    },
                }
            },

            props: ['item', 'image'],

            mounted() {
                this.$el.querySelector('input[type="file"]').setAttribute('name', 'image[' + (this.item) + ']');
                this.$el.querySelector('textarea').setAttribute('name', 'content[' + (this.item) + ']');
            },

            methods: {
                remove(e) {
                    this.$emit('decrement');
                    e.target.parentNode.remove();
                },

                loadFile(e) {
                    this.preview = URL.createObjectURL(e.target.files[0]);
                },
            },
        },
    },

    watch: {
        newsletter(val) {
            const field = this.$refs.newsletter;
            if (!new RegExp(/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-]{2,4}$/).test(val)) {
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

        displayServerMessage(message) {
            this.$store.state.displayMessage = !this.$store.state.displayMessage;
            this.$store.state.message = message;
            setTimeout(() => {
                this.$store.state.displayMessage = !this.$store.state.displayMessage;
            }, 4000);
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
                this.displayServerMessage(data);
            }).catch((err) => {
                this.displayServerMessage('Erreur : ' + err)
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
            this.showForm();
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