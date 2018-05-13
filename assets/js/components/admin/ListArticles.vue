<template>
    <div>
        <table v-for="article in articles">
            <tbody>
                <tr>
                    <td> {{ article.title }} </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

    import Mixin from '../../mixins'

    export default {
        data() {
            return {
                articles: undefined
            }
        },

        mixins: [Mixin],

        beforeMount() {
            this.getArticles();
        },
    }
</script>