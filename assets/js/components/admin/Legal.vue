<template>
    <div class="container w40 w95sm">
       <form name='legal' class="tile" v-on:submit.prevent="handleLegal">
           <div class="field">
               <label for="content">Mentions Légales</label>
               <textarea id="content" name="content">{{ legal }}</textarea>
           </div>
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
                legal: undefined
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
                    this.legal = legal;
                })
                .catch(err => {
                    console.log("Erreur : " + err);
                })
        }
    }
</script>