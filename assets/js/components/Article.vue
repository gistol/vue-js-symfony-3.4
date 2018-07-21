<template>
    <div>
        <div class='container w40 w95sm'>
            <h2>{{ article.title }}</h2>
            <figure class='tile' v-for="(image, index) in article.images">
                <img v-bind:src="'./images/' + image.src" :alt='image.title'/>
                <a v-bind:style='pdfStyle' v-if="null !== article.pdf && index === 0" v-bind:href="'./images/' + article.pdf"><font-awesome-icon v-bind:icon="pdfIcon" size="lg"/> Télécharger la recette en PDF</a>
                <figcaption class="white_space" v-html="image.content"></figcaption>
            </figure>
            <p v-if="loaded && article.categories !== undefined && article.categories.length > 0">Catégories associées à l'article</p>
            <div class="category" v-if="loaded">
                <router-link v-if="article.categories.length > 0" v-for='category in article.categories' v-bind:to="{name: 'category', params: {category: category.category} }" class="category_link">
                    <div class="tile-mh">
                        {{ category.category|capitalize }}
                    </div>
                </router-link>
            </div>

            <div class="tile-comment" v-if='loaded' v-on:click="displayComment = !displayComment">
                <font-awesome-icon v-bind:icon="plusIcon" v-if='!displayComment' />
                <font-awesome-icon v-bind:icon="minusIcon" v-if='displayComment' />
                Commentaires
                <p v-if="displayComment && article.comments.length === 0">Aucun commentaire.</p>
                <div class="comment" v-if="displayComment && comment.published && article.comments.length > 0" v-for="comment in article.comments">
                    <p>{{ comment.username }}, le {{ comment.date|formatShortDate }}</p>
                    <p class="white_space">
                        <font-awesome-icon v-bind:icon="faQuoteLeftIcon" size="xs"/>&nbsp;&nbsp;{{ comment.comment }}&nbsp;&nbsp;<font-awesome-icon v-bind:icon="faQuoteRightIcon" size="xs"/>
                    </p>
                </div>
            </div>
        </div>
        <div id="comment_modal" v-show="show">
            <button v-on:click="showForm" class="button-delete">
                <font-awesome-icon v-bind:icon="closeIcon" />
                Fermer
            </button>
            <form autocomplete="off" class="tile no-shadow" name='comment_article' v-on:submit.prevent="handleComment" v-bind:enctype="enctype">
                <label for="username">Nom</label>
                <input type="text" id="username" name="username" v-model="username"/>
                <p v-if="username_error !== undefined" class="error">{{ username_error }}</p>
                <label for="email">Email</label>
                <input type="text" id="email" name="email" v-model="email"/>
                <p v-if="email_error !== undefined" class="error">{{ email_error }}</p>
                <label for="comment">Commentaire</label>
                <textarea id="comment" name="comment" v-model="comment"></textarea>
                <p v-if="comment_error !== undefined" class="error">{{ comment_error }}</p>
                <input type="hidden" name="csrf_token" v-bind:value="csrf_token"/>
                <input type="submit" class="button-submit m10"/>
            </form>
        </div>
        <div v-if="loaded" class='container'>
            <button @click="showForm" class="button-default mauto"><font-awesome-icon v-bind:icon="commentIcon"></font-awesome-icon> Commenter</button>
        </div>
    </div>
</template>

<script>

    import Mixin from '../mixins';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome'
    import faPlusCircle from '@fortawesome/fontawesome-free-solid/faPlusCircle'
    import faMinusCircle from '@fortawesome/fontawesome-free-solid/faMinusCircle'
    import faComment from '@fortawesome/fontawesome-free-solid/faComment'
    import faPDF from '@fortawesome/fontawesome-free-solid/faFilePdf'
    import faClose from '@fortawesome/fontawesome-free-solid/faWindowClose'
    import faQuoteLeft from '@fortawesome/fontawesome-free-solid/faQuoteLeft';
    import faQuoteRight from '@fortawesome/fontawesome-free-solid/faQuoteRight';

    export default {

        name: 'Article',

        data() {
            return {
                article: [],
                username: undefined,
                email: undefined,
                comment: undefined,
                show: false, /* Modal */
                displayComment: false,
                plusIcon: faPlusCircle,
                minusIcon: faMinusCircle,
                commentIcon: faComment,
                pdfIcon: faPDF,
                closeIcon: faClose,
                faQuoteLeftIcon: faQuoteLeft,
                faQuoteRightIcon: faQuoteRight,
                username_error: undefined,
                email_error: undefined,
                comment_error: undefined,
            }
        },

        mixins: [Mixin],

        components: {
            FontAwesomeIcon
        },

        computed: {

            pdfStyle() {
                return {
                    display: 'block',
                    margin: '20px 0',
                    padding: '10px 0',
                    borderBottom: '1px solid lightgrey'
                }
            },

            loaded() {
                return this.$parent.loaded
            },

            csrf_token() {
                return this.$store.state.comment_article_csrf_token
            },
        },

        methods: {

            /* Function to open or close the modal */
            /* Also called in the mixin when the user submits their message */
            showForm(e) {
                this.show = !this.show;
            },
        },

        created() {
            this.$store.dispatch('getArticle', '/article/' + this.$route.params.slug)
                .then(data => {
                    this.article = data;
                    this.$parent.cancelAnimation();
                    this.$parent.loaded = true;
                })
        },

        mounted() {
            this.$parent.loadAnimation();
            this.$store.dispatch('getCsrfToken', 'comment_article');
            this.$store.dispatch('saveData', {
                data: this.$route.fullPath,
                type: 'navigation'
            });
        },
    }
</script>

<style lang="scss" scoped>

    @import "../../../assets/css/variables";

    .category {
        @extend %flex;
        @extend %wrap;
        margin: 20px auto;
    }

    #comment_modal {
        position: fixed;
        z-index: 3;
        background: rgba(0,0,0,.7);
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        @extend %flex;
        @extend %align-center;
        @extend %justify-center;

        form {
            @include width(40%);
        }

        form * {
            @extend %border-box;
        }

        button {
            position: absolute;
            top: 5px;
            right: 5px;
        }

        @media all and (max-width: 1024px){
            form {
                width: 90%;
            }
        }
    }

    img {
        width: 100%;
    }
</style>