import Vue from 'vue'
import Vuex from 'vuex';

Vue.use(Vuex);

const Store = new Vuex.Store({
    state:{
        articles: [],
        endloading: false,
        remain: true,
        lastId: 0,
        csrf_token: undefined
    },

    mutations: {
        addArticles(state, articles) {
            articles.forEach((article, index, array) => {
                /* Getting the id of the last article in the array for the next request */
                if (index === array.length - 1) {
                    this.lastId = article.id;
                }
                state.articles.push(article);
                state.endloading = true;
            });
        },

        addCsrfToken(state, csrf_token) {
            state.csrf_token = csrf_token
        }
    },

    actions: {

        getCsrfToken(context) {

            const req = getRequestObject("GET", '/vue-js-symfony-3.4/web/app_dev.php/csrf_token');

            req.addEventListener("load", () => {

                if (req.status >= 200 && req.status < 400) {
                    const resp = JSON.parse(req.responseText);
                    context.commit('addCsrfToken', resp.csrf_token);
                } else {
                    if (this.$route.name === 'login') {
                        this.$store.push({name: 'login'})
                    }
                }
            });

            req.send();
        },

        postData(context, data) {

            return new Promise((resolve, reject) => {

                const req = getRequestObject("POST", data.url);

                req.addEventListener("load", () => {
                    if (req.status >= 200 && req.status < 400) {
                        resolve(JSON.parse(req.responseText));
                    } else {
                        reject(req.responseText);
                    }
                });

                if (data.contentType) {
                    req.setRequestHeader("Content-Type", data.contentType);
                }

                req.send(data.value);
            });
        },

        getArticles(context) {
            fetch('/vue-js-symfony-3.4/web/app_dev.php/articles/' + this.lastId)
                .then((res) => res.json())
                .then((data) => {
                    if (data.length < 9) {
                        context.state.remain = false;
                    }
                    if (data.length > 0) {
                        context.commit('addArticles', data);
                    }
                })
                .catch((err) => {
                    console.log('Erreur : ' + err)
                })
        },
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