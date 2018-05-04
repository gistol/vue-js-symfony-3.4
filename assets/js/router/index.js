import Vue from 'vue';
import VueRouter from 'vue-router';
import Homepage from '../components/Homepage';
import Article from '../components/Article';
import Admin from '../components/Admin';
import Login from '../components/Login';

Vue.use(VueRouter);

const Router = new VueRouter({
    routes: [
        {
            path: '/',
            component: Homepage,
        },
        {
            path: '/article/:id',
            name: 'article',
            component: Article,
        },
        {
            path: '/admin/article',
            component: Admin,
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/login',
            component: Login,
        }
    ]
});

export default Router;