require('./bootstrap');
import 'fullcalendar';
window.Vue = require('vue');
//import Vuex from 'vuex';
import moment from 'moment';

import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
Vue.use(datePicker);
jQuery.extend(true, jQuery.fn.datetimepicker.defaults, {
    icons: {
      time: 'fas fa-clock',
      date: 'fas fa-calendar',
      up: 'fas fa-arrow-up',
      down: 'fas fa-arrow-down',
      previous: 'fas fa-chevron-left',
      next: 'fas fa-chevron-right',
      today: 'fas fa-calendar-check',
      clear: 'fas fa-trash-alt',
      close: 'fas fa-times-circle'
    }
});
import {
    Form,
    HasError,
    AlertError
} from 'vform'

window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

window.getVideoId = require('get-video-id');

import swal from 'sweetalert2';
window.swal = swal;
const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
window.toast = toast;

import router from './router.js';
import store from './store';
//Vue.use(Vuex)

// const store = new Vuex.Store({
//     modules: {
//         storeUser,
//     }
// })

import VueProgressBar from 'vue-progressbar'

Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '50px'
})

import Gate from "./Gate";
Vue.prototype.$gate = new Gate(window.user);

Vue.filter('upText', function (value) {
    if (!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
});

Vue.filter('myDate', function (value, fomat) {
    if (!fomat)
        return moment(value).format('MM/DD/YYYY HH:mm:ss');
    return moment(value).format(fomat);
});

window.Fire = new Vue();

Vue.component(
    'chat-box',
    require('./components/ChatBox.vue')
);

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue')
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue')
);

Vue.component(
    'not-found',
    require('./components/Page404.vue')
);

Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('breadcrumb', require('./components/Breadcrumb.vue'));

Vue.component('va-direct-chat', require('./components/widgets/VADirectChat.vue'));
Vue.component('calendar', require('./components/Calendar.vue'));

Vue.component('example-component', require('./components/ExampleComponent.vue'));


//const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => {
//     return Vue.component(_.last(key.split('/')).split('.')[0], files(key))
// })

const app = new Vue({
    el: '#app',
    router,
    store,
    mounted () {
        this.$Progress.finish()
    },
    beforeMount() {
        if(this.$gate.idLogin())
            this.$store.dispatch('actionPostOpenFetch')
    },
    data() {
        return {
            navSelect: false,
        }
    },
    methods: {
        navChange: function () {
            let name = this.$route.name
            this.navSelect = name
        }
    },
    created() {
        this.navChange()
        this.$Progress.start()
        this.$router.beforeEach((to, from, next) => {
            if (to.meta.progress !== undefined) {
              let meta = to.meta.progress
              this.$Progress.parseMeta(meta)
            }
            this.$Progress.start()
            next()
        })
        this.$router.afterEach((to, from) => {
            this.$Progress.finish()
        })
        Echo.channel('notification')
            .listen('MessageNotification', (data) => {
                let type = data.message.type;
                let message = data.message.message;
                if (type == 'ticketOpen') {
                    switch (message) {
                        case 'open':
                            this.$store.dispatch('actionPostOpenUpFetch')
                            break;

                        default:
                            this.$store.dispatch('actionPostOpenDownFetch')
                            break;
                    }
                }
            })
    },
    computed: {
        ticketOpen() {
            return this.$store.state.storePost.postOpen
        },
    },
    watch: {
        '$route': 'navChange'
    }
});