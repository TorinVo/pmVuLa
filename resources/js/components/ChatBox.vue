<template>
    <div class="card card-success card-outline direct-chat direct-chat-success">
        <div class="card-header d-flex p-0">
            <h3 class="card-title p-2 mb-0">Comments</h3>

            <div class="card-tools" style="top: .4rem;">
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                    <i class="fa fa-cogs"></i> Actions</button>
                    <div class="dropdown-menu" role="menu" x-placement="bottom-end">
                      <a href="javascript:void(0)" @click="actionsTicket('reviewed', ticket.id)" v-if="ticket.reviewed == 0 && $gate.isAdminOrDesigner()" class="dropdown-item"><i class="tb-icon fas fa-user-check text-info"></i> Mark as Reviewed</a>
                      <a href="javascript:void(0)" @click="actionsTicket('close', ticket.id)" v-if="ticket.status == 'open'" class="dropdown-item"><i class="tb-icon fas fa-dot-circle"></i> Mark as Closed</a>
                      <a href="javascript:void(0)" @click="actionsTicket('open', ticket.id)" v-else class="dropdown-item"><i class="tb-icon fas fa-dot-circle text-info"></i> Mark as Open</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <!-- Conversations are loaded here -->
            <div class="direct-chat-messages v-scroll" ref="message" id="wap-message" style="height:350px">
                <ChatItem  v-for="comment in comments" 
                    :key="comment.id" 
                    :message="comment">
                </ChatItem>
            </div>
            <!--/.direct-chat-messages-->
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <div class="input-group">
                <!-- <input type="text" name="message" @keydown="typing" v-model="message" placeholder="Comment / description" class="form-control"> -->
                <textarea class="text-box form-control v-scroll" v-model="message" @keydown="typing" placeholder="Comment / description"></textarea>
                <span class="input-group-append">
                    <button type="button" @click="sendMessage" class="btn btn-success">Add Comment</button>
                </span>
            </div>
        </div>
        <!-- /.card-footer-->
    </div>
</template>

<script>
    import ChatItem from './ChatItem.vue'
    import router from '../router';
    export default {
        components: {
            ChatItem
        },
        props: {
            postId:{
                type: Number
            },
            ticket: {
                type: Object
            }
        },
        data() {
            return {
                autoload: null,
                message: '',
                comments: [],
                activeUsers: []
            }
        },

        methods: {
            typing(e) {
                if(e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }        
            },
            actionsTicket(type, id) {
                 this.$emit('actionsTicket', type, id);
            },
            loadMessage() {
                var vm = this;
                axios.get('/api/comment/' + this.$route.params.ticket)
                .then(response => {
                    this.comments = response.data
                    this.$nextTick(() => {
                        this.toBottom();
                    })
                    
                })
                .catch(error => {
                    console.log(error)
                })
            },
            loadMoreMessage(){
                axios.get('/api/comment/' + this.$route.params.ticket, {
                    params: {'last': this.last}
                })
                .then(response => {
                    if(response.data.length > 0){
                        response.data.forEach(function (item, index) {
                           Fire.$emit('added_message', item);
                        });
                    }   
                    // this.$nextTick(() => {
                    //     this.toBottom();
                    // })
                    
                })
                .catch(error => {})
            },
            sendMessage() {
                if(!this.message || this.message.trim() === '') {
                    return
                }
                axios.post('/api/comment', {
                    message: this.message.trim(),
                    ticket: this.postId,
                    views: this.activeIdUsers
                })
                .then(response => {
                    // this.list_messages.push({
                    //     message: this.message,
                    //     created_at: new Date().toJSON().replace(/T|Z/gi, ' '),
                    //     user: this.$root.currentUserLogin
                    // })
                    Fire.$emit('added_message', response.data);
                    this.message = ''
                })
                .catch(error => {})
            },
            toBottom(){
                $("#wap-message")
                .animate({ scrollTop: $('#wap-message')
                .prop("scrollHeight")}, 1000);
            }
        },
        computed: {
            last(){
                return this.comments.length > 0 ? this.comments[Object.keys(this.comments).length-1].id : 0;
            },
            activeIdUsers(){
                return this.activeUsers.length > 0 ? this.activeUsers.map(e => e.id) : '';
            }
        },
        created() {
            let self = this;
            this.loadMessage();
            // this.autoload = setInterval(function(){
            //     self.loadMoreMessage()
            // }, 15000);
        },

        watch: {
            '$route': 'loadMessage'
        },

        mounted() {
            Echo.join('messages.' + this.$route.params.ticket)
            .here((users) => {
                this.activeUsers = users;
            })
            .joining((user) => {
                this.activeUsers.push(user);
            })
            .leaving((user) => {
                this.activeUsers.splice(this.activeUsers.indexOf(user));
            })
            .listen('MessagePosted', ({message}) => {
                Fire.$emit('added_message', message);
            });

            Fire.$on('added_message', (message) => {
                let obj = this.comments.find(obj => obj.id == message.id)
                if(obj) obj.comments = message.comments
                else this.comments.push(message)
                this.toBottom()
            });
        },
        beforeDestroy() {
            //clearInterval(this.autoload);
            //this.autoload = null;
            Echo.leave('messages.' + this.postId);
        }
    }
</script>
