<template>
    <div>
        <h2>Formulaire de contact</h2>
        <div class="container w40 w95sm">
            <div class="tile">
                <form name='contact' v-on:submit.prevent="handleContact">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email"/>
                    <div class="error">{{ email_error }}</div>

                    <label for="message">Message</label>
                    <textarea name="message" id="message"></textarea>
                    <div class="error" v-if="undefined !== message_error">{{ message_error }}</div>

                    <input type="hidden" name="csrf_token" v-bind:value="csrf_token"/>

                    <input type="submit" class="button-submit"/>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

    import Mixin from '../mixins';

    export default {
        name: 'contact',

        data() {
            return {
                email_error: undefined,
                message_error: undefined,
            }
        },

        mixins: [Mixin],

        computed: {
            csrf_token() {
                return this.$store.state.contact_csrf_token
            }
        },

        created() {
            this.$parent.cancelAnimation();
        },

        mounted() {
            this.$store.dispatch('getCsrfToken', 'contact');
            this.$store.dispatch('saveData', {
                data: this.$route.fullPath,
                type: 'navigation'
            });
        }
    }
</script>