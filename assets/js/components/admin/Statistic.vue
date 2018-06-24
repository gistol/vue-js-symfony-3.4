<template>
    <div class="container w40 w95sm tile">
        <form name="statistic" v-on:submit.prevent="handleStatistic">
            <div class="field">
                <label for="type">Type de recherche</label>
                <select name="type" id="type">
                    <option value="navigation">Navigation</option>
                    <option value="search">Recherches</option>
                </select>
            </div>
            <div class="field">
                <label for="start">Du</label>
                <input type="date" name="start" id="start"/>
                <label for="end">Au</label>
                <input type="date" name="end" id="end"/>
            </div>

            <input type="submit" class="button-submit"/>
        </form>
    </div>
</template>

<script>
    export default {
        name: "statistic",

        data() {

        },

        mounted() {
            this.$store.dispatch('getStati')
        }
    }
</script>