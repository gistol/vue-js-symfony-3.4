import Vue from 'vue';
import VueRouter from 'vue-router';

/* Components */
import User from '../components/User';
import Homepage from '../components/Homepage';
import Article from '../components/Article';
import Admin from '../components/admin/Admin';
import CreateArticle from '../components/admin/CreateArticle';
import ListArticles from '../components/admin/ListArticles';
import EditArticle from '../components/admin/EditArticle';
import Comment from '../components/admin/Comment';
import Newsletter from '../components/admin/Newsletter';
import PageNotFound from '../components/PageNotFound';
import Login from '../components/admin/Login';

Vue.use(VueRouter);

/* Registering the app routes */
const Router = new VueRouter({
    routes: [
        {
            path: '/',
            component: User,
            children: [
                {
                    path: '',
                    name: 'home_user',
                    component: Homepage,
                },
                {
                    path: 'article/:slug',
                    name: 'article',
                    component: Article,
                    props: true
                },
            ]
        },
        {
            path: '/admin',
            name: 'home_admin',
            component: Admin,
            children: [
                {
                    path: 'create',
                    name: 'createArticle',
                    component: CreateArticle,
                },
                {
                    path: 'articles',
                    name: 'listArticle',
                    component: ListArticles,
                    props: true
                },
                {
                    path: 'articles/edit/:id',
                    name: 'editArticle',
                    component: EditArticle,
                    props: true
                },
                {
                    path: 'newsletter',
                    name: 'newsletter',
                    component: Newsletter
                },
                {
                    path: 'comments',
                    name: 'comments',
                    component: Comment
                }
            ],
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
        },
        {
            path: '*',
            component: PageNotFound
        }
    ]
});

/* Global navigation guard */
Router.beforeEach((to, from, next) => {

    /* Getting routes matching the current one, including the parent route */
    /* Then, only the parent route may contain the requiresAuth meta property */
    const requiresAuth = to.matched.some((record) => {
        return record.meta.requiresAuth;
    });

    if(requiresAuth) {

        userAuthenticated().then((data) => {
            console.log('Succès : ' + data);
            next();
        }).catch((err) => {
            console.log('Erreur : ' + err);
            next('/login');
        });

    } else {
        next();
    }
});

/* Ajax request to get the token stored in session */
function userAuthenticated() {

    return new Promise((resolve, reject) => {

        if (!localStorage.getItem('token')) {
            reject('Token inexistant.');
        }

        const req = window.XMLHttpRequest ?
            new XMLHttpRequest() :
            new ActiveXObject("Microsoft.XMLHTTP");

        req.open("GET", '/vue-js-symfony-3.4/web/app_dev.php/getSessionToken');
        req.addEventListener("load", () => {

            if (req.status >= 200 && req.status < 400) {

                const resp = JSON.parse(req.responseText);

                /* If the token received from the API is the same as the
                   one stored in localStorage (received when identifying
                   for the first time) : Authentication is ok
                   Else, possible errors : token not set in session
                   Or fake token added thanks to basic console command =>
                   will result in invalid token message
                 */
                if (localStorage.getItem('token') === resp) {
                    resolve('Identification réussie');
                } else if (resp.err) {
                    reject(resp.err);
                } else {
                    reject('Token invalide.');
                }

            } else {
                reject(req.statusText);
            }
        });

        req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        req.send();
    });
}

export default Router;