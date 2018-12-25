require('./bootstrap');

window.Vue = require('vue');
//import Vuex from 'vuex';
import moment from 'moment';
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

Vue.component('example-component', require('./components/ExampleComponent.vue'));


// const files = require.context('./', true, /\.vue$/i)

// files.keys().map(key => {
//     return Vue.component(_.last(key.split('/')).split('.')[0], files(key))
// })

const app = new Vue({
    el: '#app',
    router,
    store,
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

$(function () {
    if ($('#remember')) {
        $('#remember').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        })
    }
});