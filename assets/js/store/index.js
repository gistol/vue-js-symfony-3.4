import Vue from 'vue'
import Vuex from 'vuex';

Vue.use(Vuex);

const Store = new Vuex.Store({
    state:{
        articles: []
    },
    mutations: {

    },
    actions: {
        postData(context, data) {

            return new Promise((resolve, reject) => {
                const req = window.XMLHttpRequest ?
                    new XMLHttpRequest() :
                    new ActiveXObject("Microsoft.XMLHTTP");

                req.open("POST", data.url, true);
                req.addEventListener("load", () => {
                    if (req.status >= 200 && req.status < 400) {
                        resolve(JSON.parse(req.responseText));
                    } else {
                        reject(req.statusText);
                    }
                });

                if (data.contentType) {
                    req.setRequestHeader("Content-Type", data.contentType);
                }

                req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                req.send(data.value);
            });
        }
    }
});

export default Store;