import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
import faTrash from '@fortawesome/fontawesome-free-solid/faTrash';

const Mixins = {

    data() {
        return {
            value: '',
            title: '',
            images: [],
            nbImages: 0,
            categories: [],
            nbCategories: 0,
            enctype: 'multipart/form-data',
            newsletter: undefined,
            newsletterFormValid: false,
            timer: undefined,
            searchResult: [],
            showSuggestionList: false
        }
    },

    components: {
        'server-message': {
            template:
            "<transition name='fade'>" +
            "<div v-bind:style='style' v-show='displayMessage'>" +
            "<slot></slot>" +
            "</div>" +
            "</transition>",

            props: ['displayMessage'],

            computed: {
                style() {
                    return {
                        padding: '15px',
                        position: 'fixed',
                        top: '60px',
                        right: '0',
                        background: '#fff',
                        boxShadow: '0 2px 3px rgb(163,175,183)',
                        color: '#76838f',
                        fontSize: '.9em',
                    }
                }
            },
        },

        'child-form': {

            template:
                "<div style='margin: 10px auto;'>" +
                "<label for='image' class='label-file'><i class='fas fa-image'></i> Image</label>" +
                "<img v-if='this.$props.image.src !== undefined' v-bind:src='src'/>" +
                "<img v-bind:src='preview'/>" +
                "<input type='file' v-bind:name='fileName' v-on:change='loadFile'/>" +
                "<label>Contenu</label>" +
                "<textarea :value='image.content' v-bind:name='content' v-bind:style='textAreaH'></textarea>" +
                "<button v-on:click='remove' class='button-delete mt5'><font-awesome-icon v-bind:icon='trashIcon' /> Supprimer</button>" +
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
                    fileName: 'image[' + (this.item) + ']',
                    content: 'content[' + (this.item) + ']',
                    trashIcon: faTrash
                }
            },

            props: ['item', 'image'],

            components: {
                FontAwesomeIcon
            },

            methods: {
                remove() {
                    this.$el.remove();
                },

                loadFile(e) {
                    this.preview = URL.createObjectURL(e.target.files[0]);
                },
            },
        },

        'category-form': {

            template:
                "<div>" +
                "<label v-bind:style='style' v-bind:for='cat'>Catégorie n°{{ index + 1 }}</label>" +
                "<input type='text' v-bind:name='name' :id='cat' :value='this.$props.category.category'/>" +
                "<button v-on:click='remove' class='button-delete mt5'><i class='fas fa-trash-alt'></i> Supprimer</button>" +
                "</div>"
            ,

            data() {
                return {
                    style: {
                        marginTop: '10px',
                        display: 'block'
                    },
                    cat: 'category_' + this.index,
                    name: 'category[' + this.index + ']',
                }
            },

            methods: {
                remove(e) {
                    e.target.parentNode.remove();
                }
            },

            props: ['index', 'category'],
        }
    },

    watch: {
        newsletter(val) {
            const field = this.$refs.newsletter;
            if (new RegExp(/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-]{2,4}$/).test(val)) {
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

        addCategoryForm() {
            this.categories.push(this.nbCategories);
            this.nbCategories++;
        },

        /* Common to CreateArticle, EditArticle and Article component (comments) */

        handleSubmit(url, formName) {

            const route = this.$route.name;

            if (route === "createArticle" || route === "editArticle") {
                if (this.title.length < 3) {
                    alert("Le titre doit contenir au moins 3 caractères.");
                    return;
                }
            }

            if (formName !== "search") {
                this.$store.commit('displaySendingRequest');
            }

            let formData = new FormData(this.$el.querySelector('form[name=' + formName + ']'));
            formData.append('sender', formName);

            this.$store.dispatch('postData', {
                url: url,
                value: formData
            }).then(data => {

                if (formName === 'search') {

                    /* Array of suggestions or article directly (if title sent via form) */
                    if (Array.isArray(data)) {
                        if (data.length > 0) {
                            this.searchResult = data.splice(0, 10).sort()
                        } else {
                            this.searchResult = [{title: "Aucune suggestion"}];
                            setTimeout(() => {
                                this.showSuggestionList = false;
                            }, 2000);
                        }
                    } else {
                        this.$router.push({name: 'article', params: {'slug' : data.slug} });
                        /* Force reloading */
                        this.$router.go();
                    }

                    this.showSuggestionList = true;

                    this.showSpinner = false;
                    this.$el.querySelector("svg[data-icon='spinner']").classList.remove("fa-spin");
                } else {
                    this.$store.commit('displayServerMessage', data);
                }
            }).catch(err => {
                this.$store.commit('displayServerMessage', 'Erreur : ' + err)
            });
        },

        handleCreation() {
            this.handleSubmit('/admin/create', 'create_edit_article');
        },

        handleEdition() {
            this.handleSubmit('/admin/articles/edit/' + this.$route.params.id, 'create_edit_article');
        },

        handleComment() {
            this.handleSubmit('/article/' + this.$route.params.slug + '/comment', 'comment_article');
            this.showForm();
        },

        handleSearchSubmit() {

            /* Resetting the timer */
            clearTimeout(this.timer);

            this.timer = setTimeout(() => {
                if (this.$el.querySelector('input[type="search"]').value.length > 0) {
                    this.handleSubmit('/search', 'search');
                    this.showSpinner = true;
                    this.$el.querySelector("svg[data-icon='spinner']").classList.add("fa-spin");
                }

                this.$store.dispatch('saveData', {
                    data: this.$el.querySelector('input[type="search"]').value,
                    type: 'search'
                });
            }, 700);



            this.$el.addEventListener("click", () => {
                if (this.showSuggestionList) {
                    this.showSuggestionList = false;
                }
            });
        },

        addToNewsletter() {
            if (this.newsletterFormValid) {
                this.handleSubmit('/newsletter', 'newsletter');
            } else {
                alert("Adresse email invalide !");
            }
        },
    },

    filters: {

        formatShortDate(date) {
            return new Date(date.timestamp*1000).toLocaleDateString();
        },

        formatFullDate(date) {
            return 'Le ' + new Date(date.timestamp*1000).toLocaleString();
        },

        capitalize(val) {
            return val.charAt(0).toUpperCase() + val.slice(1);
        }
    },
};

export default Mixins;
