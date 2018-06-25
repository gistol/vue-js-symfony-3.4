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
                    <option selected></option>
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

        <transition name="fade" class="m10">
            <table v-if="total !== 0">
                <thead>
                    <tr>
                        <th>Détail</th>
                        <th>Total</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(stat, key) in stats">
                        <td>{{ key }}</td>
                        <td>{{ stat }}</td>
                        <td>{{ ((stat/total)*100).toFixed(0) }}%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>{{ total }}</td>
                        <td>100%</td>
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
                stats: [],
                total: 0,
            }
        },

        methods: {
            handleStatistics(e) {
                const el = this;
                const formData = new FormData(e.target);
                el.total = 0;
                this.$store.dispatch('postData', {
                    value: formData,
                    url: "/admin/statistics"
                }).then(stats =>{
                    Object.values(stats).forEach(stat => {
                        el.total+=Number(stat);
                    });

                    this.stats = stats;
                })
            }
        },

        mixins: [Mixin]
    }
</script>