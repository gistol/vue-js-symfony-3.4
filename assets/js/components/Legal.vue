<template>
    <div class="container w40 w95sm tile">
        <p>{{ legal.content }}</p>
    </div>
</template>

<script>
    export default {
        name: 'legal-user',

        data() {
            return {
                legal: undefined
            }
        },

        mounted() {
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