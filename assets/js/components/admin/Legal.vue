<template>
    <div class="container w40 w95sm">
       <form name='legal' class="tile" v-on:submit.prevent="handleLegal">
           <div class="field">
               <label for="content">Mentions Légales</label>
               <textarea id="content" name="content" v-model="content"></textarea>
           </div>
           <input type="hidden" name='update' v-if="toUpdate" value="true"/>
           <input type="hidden" name='csrf_token' v-bind:value="csrf_token" />
           <input type="submit" class="button-submit"/>
       </form>
    </div>
</template>

<script>

    import Mixin from '../../mixins';
    import { mapState } from 'vuex';

    export default {

        name: 'legal-admin',

        data() {
            return {
                content: '',
                toUpdate: false
            }
        },

        computed: mapState({
            csrf_token: (state) => state.legal_csrf_token
        }),

        mixins: [Mixin],

        mounted() {

            this.$store.dispatch('getCsrfToken', 'legal');

            fetch('/legal')
                .then(data => data.json())
                .then(legal => {
                    if (legal instanceof Object) {
                        this.content = legal.content;
                        this.toUpdate = true;
                    }
                })
                .catch(err => {
                    console.log("Erreur : " + err);
                })
        }
    }
</script>