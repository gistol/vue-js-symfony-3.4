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
        postData(context, data) {

            const req = window.XMLHttpRequest?
                new XMLHttpRequest() :
                new ActiveXObject("Microsoft.XMLHTTP");

            req.open("POST", data.url, true);
            req.addEventListener("load", () => {
                if (req.status >= 200 && req.status < 400) {
                    alert(req.responseText)
                }
            });

            if (data.contentType) {
                req.setRequestHeader("Content-Type", data.contentType);
            }

            req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            req.send(data.value);
        }
    }
});

new Vue({
    el: "#app",
    router: Router,
    store: Store
});