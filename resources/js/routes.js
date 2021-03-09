import VueRouter from 'vue-router';
import Login from './layout/Login'
import Baseline from './layout/Baseline'
import NotFound from './views/error/404'
// import UnAuthorized from '../views/error/403'

const isAuth = (to, from, next) => {
    const loggedIn = localStorage.getItem('user')
    if (to.matched.some(record => record.meta.auth) && !loggedIn) next({ name: 'login' })
    else next()
}
let routes = [
    {
        name: 'login',
        path: '/login',
        component: require('./views/Login.vue').default,
        meta: {
            layout: Login
        }
    },
    {
        name: 'home',
        path: '/',
        component: require('./views/Home.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter:  isAuth
    },
    {
        name: 'inventories',
        path: '/inventories',
        component: require('./views/Inventories.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter: isAuth,
    },
    {
        name: 'products',
        path: '/products',
        component: require('./views/Products.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter: isAuth,
    },
    {
        name: 'companies',
        path: '/companies',
        component: require('./views/Companies.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter: isAuth,
    },
    {
        name: 'branches',
        path: '/branches',
        component: require('./views/Branches.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter: isAuth,
    },
    {
        name: 'warehouses',
        path: '/warehouses',
        component: require('./views/Warehouses.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter: isAuth,
    },
    {
        name: 'storages',
        path: '/storages',
        component: require('./views/Storages.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter: isAuth,
    },
    {
        name: 'transactions',
        path: '/transactions/:transaction_type/warehouse/:warehouse_id/status/:status',
        component: require('./views/Transactions.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter: isAuth,
    },
    {
        name: 'adjustments',
        path: '/adjustments',
        component: require('./views/Adjustments.vue').default
    },
    {
        name: 'users',
        path: '/users',
        component: require('./views/Users.vue').default,
        meta: {
            auth: true,
            layout: Baseline,
        },
        beforeEnter: isAuth,
    },
    { path: '/404', name: '404', component: NotFound, meta: {layout: NotFound} },
    // { path: '/403', name: '403', component: UnAuthorized, meta: {layout: UnAuthorized} },
    { path: '*', redirect: '/404' },
];

const router = new VueRouter({
    mode: 'history',
    routes,
    linkExactActiveClass: 'highlighted',
});

router.beforeResolve((to, from, next) => {
    // If this isn't an initial page load.
    if (to.name) {
        // Start the route progress bar.
        NProgress.start()
    }
    next()
});

router.afterEach((to, from) => {
    // Complete the animation of the route progress bar.
    NProgress.done()
});
export default router