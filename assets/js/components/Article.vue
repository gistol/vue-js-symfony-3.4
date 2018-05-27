<template>
    <div>
        <div class='container w40'>
            <figure v-for="image in article.images">
                <img :src="'./images/' + image.src" :alt='image.title' />
                <figcaption>{{ image.content }}</figcaption>
            </figure>
        </div>
        <div id="comment_modal" v-show="show">
            <button @click="showForm" class="button-delete">Fermer</button>
            <form v-on:submit.prevent="handleComment" :enctype="enctype">

                <div>
                    <label for="username">Nom</label>
                    <input type="text" id="username" name="username" v-model="username"/>
                </div>

                <div>
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" v-model="email"/>
                </div>

                <div>
                    <label for="comment">Commentaire</label>
                    <textarea id="comment" name="comment" v-model="comment"></textarea>
                </div>

                <input type="hidden" name="csrf_token" v-bind:value='csrf_token'/>

                <input type="submit" class="button-submit mauto"/>
            </form>
        </div>
        <div class='container'>
            <button @click="showForm()" class="button-default mauto">Commenter</button>
        </div>
    </div>
</template>

<script>

    import Mixin from '../mixins';

    export default {

        name: 'Article',

        data() {
            return {
                article: [],
                username: undefined,
                email: undefined,
                comment: undefined,
                show: false, /* Modal */
            }
        },

        mixins: [Mixin],

        computed: {
            csrf_token() {
                return this.$store.state.csrf_token;
            },
        },

        methods: {

            getArticle(slug) {
                fetch('http://localhost/vue-js-symfony-3.4/web/app_dev.php/article/' + slug)
                    .then((data) => data.json())
                    .then((data) => {
                        this.article = data;
                    })
                    .catch((err) => {
                        console.log('Err => ' + err)
                    })
            },

            /* Function to open or close the modal */
            /* Also called in the mixin when the user submits their message */
            showForm(e) {
                this.show = !this.show;
            }
        },

        created() {
            this.getArticle(this.$route.params.slug);
            this.$store.dispatch('getCsrfToken');
        },
    }
</script>

<style lang="scss" scoped>

    @import "../../../assets/css/variables";

    #comment_modal {
        position: fixed;
        z-index: 1;
        background: rgba(0,0,0,.8);
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
    }

    img {
        width: 100%;
    }
</style>