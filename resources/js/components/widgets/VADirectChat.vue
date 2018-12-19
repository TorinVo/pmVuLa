<template>
    <div class="floating-chat" ref="fchat">
        <i class="fa fa-comments" style="color: white;" aria-hidden="true"></i>
        <i class="badge badge-danger count-unread" v-if="countUnread">{{ countUnread }}</i>
         <div class="chat">
            <!-- DIRECT CHAT -->
            <div class="card direct-chat card-outline" :class="[boxColor, directChatColor, {'direct-chat-contacts-open': openContact}]">
                <div class="card-header">
                    <h3 class="card-title">{{ (selectedContact) ? selectedContact.name : 'Select a Contact' }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-toggle="tooltip" @click="openContact = !openContact" title="Contacts">
                            <i class="fa fa-comments"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-widget="closechat"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="card-body" style="overflow: hidden;">
                    <!-- Conversations are loaded here -->
                    <div class="direct-chat-messages v-scroll" ref="feed" style="height: 240px;">
                        <va-direct-chat-item v-for="(item, index) in talklist" :key="index" :name="item.name" :date="item.created_at"
                            :profileImage="item.photo" :message="item.text" :isMine="item.isMine"></va-direct-chat-item>
                    </div>
                    <!--/.direct-chat-messages-->

                    <!-- Contacts are loaded here -->
                    <va-direct-chat-contact :contacts="contacts" @selected="startConversationWith"></va-direct-chat-contact>
                    
                </div>
                <div class="footer">
                    <textarea class="text-box" v-model="message" @keydown="typing"></textarea>
                    <button  @click="sendMessage">send</button>
                </div>
            </div>
            <!--/.direct-chat -->
         </div>
    </div>
</template>

<script>
    import VADirectChatItem from './VADirectChatItem.vue'
    import VADirectChatContact from './VADirectChatContact.vue'
    export default {
        name: 'va-direct-chat',
        beforeMount() {
            this.$store.dispatch('actionUserFetch')
        },
        props: {
            theme: {
                type: String,
                default: 'primary'
            },
            title: {
                type: String
            },
            placeholder: {
                type: String,
                default: 'Type Message ...'
            }
        },
        methods: {
            scrollToBottom() {
                setTimeout(() => {
                    $(this.$refs.feed)
                    .animate({ scrollTop: (this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight)}, 500);
                    //this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
                }, 50);
            },
            startConversationWith(contact) {
                this.updateUnreadCount(contact.id, true);
                this.openContact = false;
                axios.get(`/api/message/${contact.id}`)
                    .then((response) => {
                        this.talklist = response.data;
                        this.selectedContact = contact;
                    })
            },
            typing(e) {
                if(e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }        
            },
            sendMessage() {
                if(!this.message || this.message.trim() === '') {
                    return
                }
                axios.post('/api/message', {
                    contact_id: this.selectedContact.id,
                    text: this.message,
                })
                .then(response => {
                    //Fire.$emit('added_message', response.data);
                    this.saveNewMessage(response.data);
                    this.message = ''
                })
                .catch(error => {
                    console.log(error)
                })
            },
            saveNewMessage(message) {
               this.talklist.push(message);
            },
            hanleIncoming(message) {
                if (this.selectedContact && message.from == this.selectedContact.id) {
                    this.saveNewMessage(message);
                    return;
                }
                this.updateUnreadCount(message.from, false);
            },
            updateUnreadCount(contact, reset) {
                this.$store.dispatch('actionUpdateUnreadCount', { contact, reset })
            }
        },
        data() {
            return {
                selectedContact: null,
                talklist: [],
                message: '',
                openContact: false
            };
        },
        computed: {
            contacts() {
                return this.$store.state.storeUser.users
            },
            countUnread() {
                return this.$store.getters.countUnread
            },
            badgeColor() {
                switch (this.theme) {
                    case 'primary':
                        return 'bg-primary'
                    case 'success':
                        return 'bg-green'
                    case 'warning':
                        return 'bg-yellow'
                    case 'danger':
                        return 'bg-red'
                    default:
                        return 'bg-light-blue'
                }
            },
            boxColor() {
                switch (this.theme) {
                    case 'primary':
                    case 'success':
                    case 'warning':
                    case 'danger':
                    case 'vue':
                        return `card-${this.theme}`
                    default:
                        return 'card-primary'
                }
            },
            directChatColor() {
                switch (this.theme) {
                    case 'primary':
                    case 'success':
                    case 'warning':
                    case 'danger':
                    case 'vue':
                        return `direct-chat-${this.theme}`
                    default:
                        return 'direct-chat-primary'
                }
            }
        },
        watch: {
            talklist(contact) {
                this.scrollToBottom();
            }
        },
        mounted() {
            let vm = this
            Echo.private(`chats.${this.$gate.idLogin()}`)
                .listen('NewMessage', ({message}) => {
                    this.hanleIncoming(message);
                });
            
            var element = $(vm.$refs.fchat)

            setTimeout(function () {
                element.addClass("enter");
            }, 1000);
            
            element.click(openElement);

            function openElement() {
                var messages = element.find('.messages');
                element.find('>i').hide();
                element.addClass('expand');
                element.find('.chat').addClass('enter');
                element.off('click', openElement);
                element.find('[data-widget="closechat"]').click(closeElement);
            }

            function closeElement() {
                element.find('.chat').removeClass('enter').hide();
                element.find('>i').show();
                element.removeClass('expand');
                element.find('.header button').off('click', closeElement);
                setTimeout(function () {
                    element.find('.chat').removeClass('enter').show()
                    element.click(openElement);
                }, 500);
            }
        },
        created() {
            if(!this.selectedContact)
                this.openContact = true
        },
        components: {
            'va-direct-chat-item': VADirectChatItem,
            'va-direct-chat-contact': VADirectChatContact
        }
    }

</script>
