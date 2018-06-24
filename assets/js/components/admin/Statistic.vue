<template>
    <div class="container w40 w95sm tile">
        <form name="statistic" v-on:submit.prevent="handleStatistics">
            <div class="field">
                <label for="type">Type de recherche</label>
                <select name="type" id="type">
                    <option value="navigation">Navigation</option>
                    <option value="search">Recherches</option>
                </select>
            </div>
            <div class="field">
                <label for="bot">Bot</label>
                <select name="bot" id="bot">
                    <option value="0">Non bot</option>
                    <option value="1">Bot</option>
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

        <transition name="fade">
            <table v-if="stats.length > 0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Uri</th>
                        <th>Total</th>
                        <th>Bot</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="stat in stats">
                        <td>{{ stat.date }}</td>
                        <td>{{ stat.uri }}</td>
                        <td>{{ stat.total }}</td>
                        <td>{{ stat.bot }}</td>
                    </tr>
                </tbody>
            </table>
        </transition>
    </div>
</template>

<script>

    import Mixin from './../../mixins/index';

    export default {
        name: "statistic",

        data() {
            return {
                stats: []
            }
        },

        methods: {
            handleStatistics(e) {
                const formData = new FormData(e.target);
                this.$store.dispatch('postData', {
                    value: formData,
                    url: "/admin/statistics"
                });
            }
        },

        mixins: [Mixin]
    }
</script>