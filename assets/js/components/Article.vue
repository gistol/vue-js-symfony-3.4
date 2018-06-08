<template>
    <div>
        <div class='container w40 w95sm'>
            <h2>{{ article.title }}</h2>
            <figure class='tile' v-for="image in article.images">
                <img :src="'./images/' + image.src" :alt='image.title' />
                <figcaption class="white_space">{{ image.content }}</figcaption>
            </figure>
            <div class="tile" v-if="loaded">
                <p>Catégories associées à l'article</p>
                <div class="category">
                    <router-link v-for='category in article.categories' v-bind:to="{name: 'category', params: {category: category.category} }" class="category_link">{{ category.category|capitalize }}</router-link>
                </div>
            </div>
            <div class="tile-comment" v-if='loaded' v-on:click="displayComment = !displayComment">
                <i class="fas fa-plus-circle" v-if='!displayComment'></i>
                <i class="fas fa-minus-circle" v-if='displayComment'></i>
                Commentaires
                <div class="comment" v-if="displayComment && comment.published" v-for="comment in article.comments">
                    <p>{{ comment.username }} le {{ comment.date|formatShortDate }}</p>
                    <p class="white_space">{{ comment.comment }}</p>
                </div>
            </div>
        </div>
        <div id="comment_modal" v-show="show">
            <button @click="showForm" class="button-delete">Fermer</button>
            <form autocomplete="off" name='comment_article' v-on:submit.prevent="handleComment" :enctype="enctype">
                <div class="field">
                    <label for="username">Nom</label>
                    <input type="text" id="username" name="username" v-model="username"/>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" v-model="email"/>
                </div>

                <div class="field">
                    <label for="comment">Commentaire</label>
                    <textarea id="comment" name="comment" v-model="comment"></textarea>
                </div>

                <input type="hidden" name="csrf_token"/>

                <input type="submit" class="button-submit m10"/>
            </form>
        </div>
        <div v-if="loaded" class='container'>
            <button @click="showForm" class="button-default mauto"><i class="far fa-comment"></i> Commenter</button>
        </div>
    </div>
</template>

<script>

    import Mixin from '../mixins';

    export default {

        name: 'Article',

        data() {
            return {
                formName: 'comment_article',
                article: [],
                loaded: false,
                username: undefined,
                email: undefined,
                comment: undefined,
                show: false, /* Modal */
                displayComment: false
            }
        },

        mixins: [Mixin],

        methods: {

            getArticle(slug) {
                fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/article/' + slug)
                    .then(data => data.json())
                    .then(data => {
                        this.article = data;
                        this.loaded = true;
                    })
                    .catch((err) => {
                        console.log('Err => ' + err)
                    })
            },

            /* Function to open or close the modal */
            /* Also called in the mixin when the user submits their message */
            showForm(e) {
                this.show = !this.show;
            },
        },

        created() {
            this.getArticle(this.$route.params.slug);
        },

        mounted() {
            this.$store.dispatch('getCsrfToken', this.formName)
                .then(token => {
                    this.setCsrfToken(this.formName, token)
                });
        }
    }
</script>

<style lang="scss" scoped>

    @import "../../../assets/css/variables";

    .category {
        @extend %flex;
    }

    #comment_modal {
        position: fixed;
        z-index: 1;
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