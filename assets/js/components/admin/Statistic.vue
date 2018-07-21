<template>
    <div class="container w40 w95sm">
        <div class="tile">
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
                        <option value="0">Non</option>
                        <option value="1">Oui</option>
                    </select>
                </div>
                <div class="field">
                    <label for="start">Du</label>
                    <input type="date" name="start" id="start"/>
                    <label for="end">Au</label>
                    <input type="date" name="end" id="end"/>
                </div>

                <input type="hidden" name="csrf_token" v-bind:value="csrf_token"/>

                <input type="submit" class="button-submit"/>
            </form>
        </div>

        <transition name="fade" class="ml5">
            <div class="tile" v-if="total !== 0">
                <table>
                    <thead>
                        <tr>
                            <th>Détail</th>
                            <th>Total</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>{{ total }}</td>
                            <td>100%</td>
                        </tr>
                        <tr v-for="(stat, key) in stats">
                            <td>{{ key }}</td>
                            <td>{{ stat }}</td>
                            <td>{{ ((stat/total)*100).toFixed(0) }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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

                    Object.values(stats).map(stat => {
                        el.total+=Number(stat);
                    });

                    if (el.total > 0) {
                        this.stats = this.sortData(stats);
                    } else {
                        this.$store.commit("displayServerMessage", "Aucun résultat.")
                    }
                })
            },

            sortData(obj) {
                let sortedObjArray = Object.keys(obj).sort((a, b) => obj[b] - obj[a]);
                let newObj = {};
                sortedObjArray.forEach(key => {newObj[key] = obj[key]});
                obj = {};
                return newObj;
            }
        },

        computed: {
            csrf_token() {
                return this.$store.state.statistic_csrf_token;
            },
        },

        mixins: [Mixin],

        mounted() {
            this.$store.dispatch('getCsrfToken', "statistic");
        }
    }
</script>