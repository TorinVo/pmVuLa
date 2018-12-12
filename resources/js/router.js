import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

let routes = [
    { path: '/', component: require('./components/Dashboard.vue'), name: 'dashboard' },
    { path: '/developer', component: require('./components/Developer.vue'), name: 'developer' },
    { path: '/profile', component: require('./components/Profile.vue'), name: 'profile' },
    { path: '/users', component: require('./components/Users.vue'), name: 'users' },
    { path: '/projects', component: require('./components/Projects.vue'), name: 'projects' },
    { path: '/tickets', component: require('./components/TicketDashboard.vue'), name: 'tickets' },
    { path: '/ticket/:ticket', component: require('./components/TicketPage.vue'), name: 'ticket' },
    { path: '*', component: require('./components/Page404.vue'), name: 'notfound' },
];

let router = new VueRouter({
    // linkExactActiveClass: 'active',
    mode: 'history',
    routes,
    scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0 }
    }
});

export default router;