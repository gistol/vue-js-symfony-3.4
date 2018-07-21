<template>
    <div>
        <h2>Mentions légales</h2>
        <div class="container w40 w95sm">
            <p class="tile white_space" v-html="content"></p>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'legal-user',

        data() {
            return {
                content: ''
            }
        },

        mounted() {
            fetch('/legal')
                .then(data => data.json())
                .then(legal => {
                    if (legal instanceof Object) {
                        this.content = legal.content;
                        this.$parent.cancelAnimation();
                        this.$parent.loaded = true;
                    }
                })
                .catch(err => {
                    console.log("Erreur : " + err);
                });

            this.$parent.loadAnimation();
            this.$store.dispatch('saveData', {
                data: this.$route.fullPath,
                type: 'navigation'
            });
        }
    }
</script>