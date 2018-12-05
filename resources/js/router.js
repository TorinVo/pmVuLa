import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

let routes = [
    { path: '/dashboard', component: require('./components/Dashboard.vue') },
    { path: '/developer', component: require('./components/Developer.vue') },
    { path: '/profile', component: require('./components/Profile.vue') },
    { path: '/user', component: require('./components/Users.vue') },
    { path: '/project', component: require('./components/Projects.vue') },
    { path: '/ticket', component: require('./components/TicketDashboard.vue') },
    { 
        path: '/ticket/:ticket', 
        component: require('./components/TicketPage.vue'), 
        name: 'ticket'
     },
];

let router = new VueRouter({
    linkActiveClass: 'active',
    mode: 'history',
    routes,
    scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0 }
    }
});

export default router;