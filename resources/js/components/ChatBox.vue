<template>
    <div class="card card-success card-outline direct-chat direct-chat-success">
        <div class="card-header d-flex p-0">
            <h3 class="card-title p-2 mb-0">Comments</h3>

            <div class="card-tools">
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                      <i class="fa fa-cogs"></i> Actions</button>
                    <div class="dropdown-menu float-right" role="menu" x-placement="bottom-start">
                      <a href="#" class="dropdown-item">Add new event</a>
                      <a href="#" class="dropdown-item">Clear events</a>
                      <div class="dropdown-divider"></div>
                      <a href="#" class="dropdown-item">View calendar</a>
                    </div>
                  </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <!-- Conversations are loaded here -->
            <div class="direct-chat-messages" ref="message" id="wap-message" style="height:350px">
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
                <input type="text" name="message" @keydown="typing" v-model="message" placeholder="Comment / description" class="form-control">
                <span class="input-group-append">
                    <button type="button" @click="sendMessage" class="btn btn-warning">Add Comment</button>
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
            }
        },
        data() {
            return {
                autoload: null,
                message: '',
                comments: []
            }
        },

        methods: {
            typing(e) {
                if(e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }        
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
                    console.log(response.data);
                    if(response.data.length > 0){
                        response.data.forEach(function (item, index) {
                           Fire.$emit('added_message', item);
                        });
                    }   
                    // this.$nextTick(() => {
                    //     this.toBottom();
                    // })
                    
                })
                .catch(error => {
                    console.log(error)
                })
            },
            sendMessage() {
                if(!this.message || this.message.trim() === '') {
                    return
                }
                axios.post('/api/comment', {
                    message: this.message.trim(),
                    ticket: this.postId
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
                .catch(error => {
                    console.log(error)
                })
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
            }
        },
        created() {
            let self = this;
            this.loadMessage();
            // this.autoload = setInterval(function(){
            //     self.loadMoreMessage()
            // }, 15000);
            
            Echo.private('messages.' + this.$route.params.ticket)
            .listen('MessagePosted', ({message}) => {
                Fire.$emit('added_message', message);
            });
        },

        watch: {
            '$route': 'loadMessage'
        },

        mounted() {
            Fire.$on('added_message', (message) => {
                let obj = this.comments.find(obj => obj.id == message.id)
                if(obj) obj.comments = message.comments
                else this.comments.push(message)
                this.toBottom()
            });

            // Echo.channel('chatroom')
            // .listen('MessagePosted', (data) => {
            //     Fire.$emit('added_message', data.message);
            // });
        },
        beforeDestroy() {
            clearInterval(this.autoload);
            this.autoload = null;
        }
    }
</script>
