import Vue from 'vue';
import Example from './components/Example';
import Vuex from 'vuex';

Vue.use(Vuex);

const Store = new Vuex.Store({
    state:{
        title: "This is a component"
    },
    mutations: {

    },
    actions: {

    }
});

new Vue({
    el: "#app",
    components: {
        Example
    },
    store: Store
});