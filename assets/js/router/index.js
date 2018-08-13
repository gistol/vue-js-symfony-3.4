import Vue from 'vue';
import VueRouter from 'vue-router';

/* Components */
import User from '../components/User';
import Homepage from '../components/Homepage';
import Article from '../components/Article';
import Category from '../components/Category';
import LegalUser from '../components/Legal';
import Contact from '../components/Contact';
import Admin from '../components/admin/Admin';
import CreateArticle from '../components/admin/CreateArticle';
import HomepageAdmin from '../components/admin/Homepage';
import EditArticle from '../components/admin/EditArticle';
import Comment from '../components/admin/Comment';
import Newsletter from '../components/admin/Newsletter';
import Statistic from '../components/admin/Statistic';
import LegalAdmin from '../components/admin/Legal';
import PageNotFound from '../components/PageNotFound';
import Login from '../components/admin/Login';
import AdminContact from '../components/admin/Contact';

Vue.use(VueRouter);

/* Registering the app routes */
const Router = new VueRouter({

    routes: [
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
                    name: 'homepageAdmin',
                    component: HomepageAdmin,
                    props: true
                },
                {
                    path: 'articles/edit/:token',
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
                    path: 'statistic',
                    name: 'statistic',
                    component: Statistic
                },
                {
                    path: 'legal',
                    name: 'legal-admin',
                    component: LegalAdmin
                },
                {
                    path: 'comments',
                    name: 'comments',
                    component: Comment
                },
                {
                    path: 'contacts',
                    name: 'contacts',
                    component: AdminContact
                },
                {
                    path: 'logout',
                    name: 'logout'
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
            path: '/',
            component: User,
            children: [
                {
                    path: '',
                    name: 'home_user',
                    component: Homepage,
                    props: {default: true}
                },
                {
                    path: '/article/:slug',
                    name: 'article',
                    component: Article,
                    props: true
                },
                {
                    path: '/category',
                    name: 'categories',
                    component: Category,
                    props: true
                },
                {
                    path: '/category/:category',
                    name: 'category',
                    component: Category,
                    props: true
                },
                {
                    path: '/mentions-legales',
                    name: 'legal',
                    component: LegalUser
                },
                {
                    path: 'contact',
                    name: 'contact',
                    component: Contact
                },
                {
                    path: '*',
                    component: PageNotFound
                }
            ]
        }
    ]
});

/* Global navigation guard */
Router.beforeEach((to, from, next) => {

    /* Getting routes matching the current one, including the parent route */
    /* Then, only the parent route may contain the requiresAuth meta property */
    const requiresAuth = to.matched.some(record => {
        return record.meta.requiresAuth;
    });

    if(requiresAuth) {

        userAuthenticated().then(data => {
            next();
        }).catch(err => {
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

        req.open("GET", '/getSessionToken');
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