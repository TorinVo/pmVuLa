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
        this.$store.dispatch('actionPostOpenFetch')
    },
    data() {
        return {
            navSelect: false,
            selectedContact: null,
            talkList: [
                {
                    name: 'Alexander Pierce',
                    date: new Date(),
                    profileImage: 'http://cfile9.uf.tistory.com/image/25270C4853F7057D09BFD3',
                    message: `Is this template really for free? That's unbelievable`,
                    isMine: false
                },
                {
                    name: 'Sarah Bullock',
                    date: new Date(),
                    profileImage: 'http://cfile9.uf.tistory.com/image/25270C4853F7057D09BFD3',
                    message: `You better believe it!`,
                    isMine: true
                },
                {
                    name: 'Sarah Bullock',
                    date: new Date(),
                    profileImage: 'http://cfile9.uf.tistory.com/image/25270C4853F7057D09BFD3',
                    message: `You better believe it!`,
                    isMine: true
                },
                {
                    name: 'Sarah Bullock',
                    date: new Date(),
                    profileImage: 'http://cfile9.uf.tistory.com/image/25270C4853F7057D09BFD3',
                    message: `You better believe it!`,
                    isMine: true
                }
            ],
            // contacts: [{
            //     name: 'Count Dracula',
            //     profileImage: 'http://cfile9.uf.tistory.com/image/25270C4853F7057D09BFD3',
            //     latestMessage: 'How have you been? I was...',
            //     latestDate: new Date()
            // }]
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
    $(document).on('click', '[data-widget="chat-pane-toggle"]', function (event) {
        if (event) {
            event.preventDefault();
        }

        $(this).parents(".direct-chat").first().toggleClass("direct-chat-contacts-open");

    });

    var element = $('.floating-chat');

    setTimeout(function () {
        element.addClass('enter');
    }, 1000);

    element.click(openElement);

    function openElement() {
        var messages = element.find('.messages');
        //var textInput = element.find('.text-box');
        element.find('>i').hide();
        element.addClass('expand');
        element.find('.chat').addClass('enter');
        //var strLength = textInput.val().length * 2;
        // textInput.keydown(onMetaAndEnter).prop("disabled", false).focus();
        element.off('click', openElement);
        element.find('[data-widget="closechat"]').click(closeElement);
        element.find('#sendMessage').click(sendNewMessage);
        messages.scrollTop(messages.prop("scrollHeight"));
    }

    function closeElement() {
        element.find('.chat').removeClass('enter').hide();
        element.find('>i').show();
        element.removeClass('expand');
        element.find('.header button').off('click', closeElement);
        element.find('#sendMessage').off('click', sendNewMessage);
        element.find('.text-box').off('keydown', onMetaAndEnter).prop("disabled", true).blur();
        setTimeout(function () {
            element.find('.chat').removeClass('enter').show()
            element.click(openElement);
        }, 500);
    }

    function sendNewMessage() {
        var userInput = $('.text-box');
        var newMessage = userInput.html().replace(/\<div\>|\<br.*?\>/ig, '\n').replace(/\<\/div\>/g, '').trim().replace(/\n/g, '<br>');

        if (!newMessage) return;

        var messagesContainer = $('.messages');

        messagesContainer.append([
            '<li class="self">',
            newMessage,
            '</li>'
        ].join(''));

        // clean out old message
        userInput.html('');
        // focus on input
        userInput.focus();

        messagesContainer.finish().animate({
            scrollTop: messagesContainer.prop("scrollHeight")
        }, 250);
    }

    function onMetaAndEnter(event) {
        if ((event.metaKey || event.ctrlKey) && event.keyCode == 13) {
            sendNewMessage();
        }
    }
})
