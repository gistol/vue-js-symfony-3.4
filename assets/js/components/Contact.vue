<template>
    <div>
        <h2>Formulaire de contact</h2>
        <div class="container w40 w95sm">
            <div class="tile">
                <form name='contact' v-on:submit.prevent="handleContact">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" v-model="email"/>
                    <div class="error">{{ email_error }}</div>

                    <label for="message">Message</label>
                    <textarea name="message" id="message" v-model="message"></textarea>
                    <div class="error" v-if="undefined !== message_error">{{ message_error }}</div>

                    <input type="hidden" name="csrf_token" v-bind:value="csrf_token"/>

                    <div v-if="email.length > 0 && message.length > 0">
                        <label for="robot">Veuillez saisir le code ci-dessous</label>
                        <input type="text" v-model='robot' id="robot" placeholder="Je ne suis pas un robot" v-bind:style="style"/>
                        <div class="error" v-if="undefined !== robot_error">{{ robot_error }}</div>
                        <div class="mt5">{{ randomVal }}</div>
                    </div>
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
                robot_error: undefined,
                robot: '',
                email: '',
                message: '',
                randomVal: '',
                style: '',
                isValueValid: false
            }
        },

        mixins: [Mixin],

        computed: {
            csrf_token() {
                return this.$store.state.contact_csrf_token
            },
        },

        watch: {
            robot(val) {
                if (Number(val) === this.randomVal) {
                    this.style = {
                        borderBottom: '2px solid green'
                    };
                    this.isValueValid = true;
                    this.robot_error = undefined;
                } else {
                    this.style = {
                        borderBottom: '2px solid #b22222'
                    };
                    this.isValueValid = false;
                }
            }
        },

        methods: {
            random() {
                this.randomVal = Math.floor((Math.random() * 100000) + 1);
            }
        },

        created() {
            this.cancelSpinnerAnimation();
        },

        mounted() {
            this.random();
            this.$store.dispatch('getCsrfToken', 'contact');
            this.$store.dispatch('saveData', {
                data: this.$route.fullPath,
                type: 'navigation'
            });
        }
    }
</script>