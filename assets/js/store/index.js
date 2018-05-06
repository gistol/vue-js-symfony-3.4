import Vue from 'vue'
import Vuex from 'vuex';

Vue.use(Vuex);

const Store = new Vuex.Store({
    state:{
        articles: [],
    },
    mutations: {

    },
    actions: {

        getCsrfToken() {

            return new Promise((resolve, reject) => {

                const req = getRequestObject("GET", '/vue-js-symfony-3.4/web/app_dev.php/csrf_token');

                req.addEventListener("load", () => {

                    if (req.status >= 200 && req.status < 400) {
                        const resp = JSON.parse(req.responseText);
                        resolve(resp.csrf_token);
                    } else {
                        reject(req.statusText);
                    }
                });

                req.send();
            });
        },

        postData(context, data) {

            return new Promise((resolve, reject) => {

                const req = getRequestObject("POST", data.url);

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

                req.send(data.value);
            });
        }
    }
});

function getRequestObject(method, url) {
    const req = window.XMLHttpRequest ?
        new XMLHttpRequest() :
        new ActiveXObject("Microsoft.XMLHTTP");

    req.open(method, url);

    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");

    return req;
}

export default Store;