import Vue from 'vue'
import Vuex from 'vuex';

Vue.use(Vuex);

const Store = new Vuex.Store({
    state: {
        articles: [],
        articlesCount: undefined,
        lastId: 0,
        displayMessage: false,
        message: '',
        timer: undefined,
        loaded: false,
        comment_article_csrf_token: undefined,
        search_csrf_token: undefined,
        newsletter_csrf_token: undefined,
        login_csrf_token: undefined,
        create_edit_article_csrf_token: undefined,
        statistic_csrf_token: undefined,
        legal_csrf_token: undefined
    },

    mutations: {

        addArticles(state, articles) {
            articles.forEach((article, index, array) => {
                /* Getting the id of the last article in the array for the next request */
                if (index === array.length - 1) {
                    this.lastId = article.id;
                }
                state.articles.push(article);
            });
        },

        setNumberOfArticles(state, data) {
            state.articlesCount = data;
        },

        displayServerMessage(state, message) {
            /* if a message is already being displayed */
            /* Reset the timer */
            if (state.displayMessage) clearTimeout(state.timer);
            state.displayMessage = true;
            state.message = message;
            state.timer = setTimeout(() => {
                state.displayMessage = !state.displayMessage;
            }, 4000);
        },

        displaySendingRequest() {
            this.commit('displayServerMessage', 'Envoi en cours.');
        },

        displayWaitingForData() {
            this.commit('displayServerMessage', 'Réception en cours.');
        },

        hideMessage(state) {
            state.displayMessage = false;
            clearTimeout(state.timer);
        },

        setCsrfToken(state, data) {
            state[data.prop] = data.csrf_token;
        }
    },

    actions: {

        getCsrfToken({commit}, sender) {

            const req = getRequestObject("POST", '/csrf_token');

            req.addEventListener("load", () => {

                if (req.status >= 200 && req.status < 400) {

                    commit("setCsrfToken", {
                        prop: sender.concat('_csrf_token'), /* E.g : comment_article_csrf_token */
                        csrf_token: JSON.parse(req.responseText).csrf_token
                    });

                } else {
                    if (this.$route.name === 'login') {
                        this.$store.push({name: 'login'})
                    }
                }
            });

            req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            req.send('sender=' + sender);
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

        getArticles({commit}) {
            return new Promise(resolve => {
                fetch('/articles/' + this.lastId)
                    .then(res => res.json())
                    .then(data => {
                        commit('hideMessage');

                        if (data.length > 0) {
                            commit('addArticles', data);
                        }

                        resolve();
                    })
                    .catch((err) => {
                        console.log('Erreur : ' + err)
                    });
            });
        },

        getNumberOfArticles({commit}) {
            fetch('/articlesCount')
                .then(res => res.json())
                .then(data => {
                    commit('setNumberOfArticles', data);
            });
        },

        saveData(context, data) {
            const formData = new FormData();
            formData.append("data", data.data);
            formData.append("type", data.type);
            getRequestObject("POST", "/statistics").send(formData);
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