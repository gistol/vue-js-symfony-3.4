<template>
    <div>
        <div class='container'>
            <figure v-for="image in article.images">
                <img :src="'./images/' + image.src" :alt='image.title' />
                <figcaption>{{ image.content }}</figcaption>
            </figure>
        </div>
        <div id="comment_modal" v-show="show">
            <div v-bind:style="width">
                <button @click="showForm" class="button-close">Fermer</button>
                <form v-on:submit.prevent="handleComment" enctype="multipart/form-data">

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

                    <input type="submit" class="button-submit"/>
                </form>
            </div>
        </div>
        <div class='container'>
            <button ref='modal_opener' @click="showForm" class="button-link">Commenter</button>
        </div>
    </div>
</template>

<script>

    import mapState from 'vuex';
    import Mixin from '../mixins';

    export default {
        name: 'Article',

        data() {
            return {
                username: undefined,
                email: undefined,
                comment: undefined,
                article: undefined,
                show: false,
            }
        },

        computed: {
            csrf_token() {
                return this.$store.state.csrf_token;
            },
        },

        mixins: [Mixin],

        created() {
            this.getArticle(this.$route.params.slug);
            this.$store.dispatch('getCsrfToken');
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

            showForm(e) {
                this.show = !this.show;
                this.$refs.modal_opener = this.show ? "Masquer" : "Commenter";
            }
        }
    }
</script>

<style lang="scss" scoped>

    @import "../../../assets/css/variables";

    .container {
        width: 50%;

        img {
            @extend %max-width;
        }
    }

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

        button {
            position: absolute;
            top: 5px;
            right: 5px;
        }

        form {

            padding: 10px;

            background: $light-grey;

            /* This rule overwrites the one defined in variables.scss only for width */
            @include loopItem("input[type=text]", 'width', 90%);

            textarea {
                height: 200px;
                width: 90%;
                resize: none;
                padding: 5px;
            }

            input[type='submit'] {
                margin: 10px auto;
            }
        }
    }

    button {
        margin: 10px auto;
    }
</style>