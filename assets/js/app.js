import Vue from 'vue';
import Router from './router';
import Store from './store';
require('../../assets/css/app.scss');

new Vue({
    el: "#app",
    router: Router,
    store: Store
});