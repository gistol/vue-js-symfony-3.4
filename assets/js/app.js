import Vue from 'vue';
import Vuex from 'vuex';
import Router from './router'

Vue.use(Vuex);

const Store = new Vuex.Store({
    state:{
        articles: []
    },
    mutations: {

    },
    actions: {

    }
});

new Vue({
    el: "#app",
    router: Router
});