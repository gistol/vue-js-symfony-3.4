import Vue from 'vue';
import VueRouter from 'vue-router';
import Homepage from '../components/Homepage'
import Articles from '../components/Articles'
import Admin from '../components/Admin'

Vue.use(VueRouter);

const Router = new VueRouter({
    routes: [
        {
            path: '/',
            component: Homepage
        },
        {
            path: '/article/:id',
            name: 'article',
            component: Articles,
        },
        {
            path: '/admin/article',
            component: Admin,
            meta: {
                requiresAuth: true
            }
        }

    ]
});

export default Router;