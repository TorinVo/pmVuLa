<template>
    <div>
        <!-- Message. Default to the left -->
        <div class="direct-chat-msg" v-if="this.$gate.idLogin() != message.user_id">
            <div class="direct-chat-info clearfix">
                <span class="direct-chat-name float-left">{{ getName }}</span>
                <span class="direct-chat-timestamp float-right">{{ message.created_at | myDate('MMM Do YYYY hh:mm A') }}</span>
            </div>
            <!-- /.direct-chat-info -->
            <img class="direct-chat-img" :src="getProfilePhoto"
                alt="Message User Image">
            <!-- /.direct-chat-img -->
            <div class="direct-chat-text">
               <template v-for="(node, n) in nodes">
                    <LinkItem v-if="n % 2" :to="node" :key="n"></LinkItem>
                    <span v-html="node" v-else :key="n"></span>
                </template>
            </div>
            <!-- /.direct-chat-text -->
            <div class="direct-chat-info clearfix" v-if="userRead">
                <span class="direct-chat-timestamp float-right">Seen by {{userRead}}</span>
            </div>
        </div>
        <!-- /.direct-chat-msg -->

        <!-- Message to the right -->
        <div class="direct-chat-msg right" v-else>
            <div class="direct-chat-info clearfix">
                <span class="direct-chat-name float-right">{{ getName }}</span>
                <span class="direct-chat-timestamp float-left">{{ message.created_at | myDate('MMM Do YYYY hh:mm A') }}</span>
            </div>
            <!-- /.direct-chat-info -->
            <img class="direct-chat-img" :src="getProfilePhoto"
                alt="Message User Image">
            <!-- /.direct-chat-img -->
            <div class="direct-chat-text">
                <template v-for="(node, n) in nodes">
                    <LinkItem v-if="n % 2" :to="node" :key="n"></LinkItem>
                    <span v-html="node" v-else :key="n"></span>
                </template>
            </div>
            <div class="direct-chat-info clearfix" v-if="userRead">
                <span class="direct-chat-timestamp">Seen by {{userRead}}</span>
            </div>
            <!-- /.direct-chat-text -->
        </div>
        <!-- /.direct-chat-msg -->
    </div>
</template>

<script>
    import router from '../router';
    import LinkItem from './LinkItem.vue'
    export default {
        components: {
            LinkItem
        },
        props: {
            message: {
                user: {},
                users: {}
            }
        },
        methods: {
            nl2br(str, replaceMode, isXhtml){
                var breakTag = (isXhtml) ? '<br />' : '<br>';
                var replaceStr = (replaceMode) ? '$1'+ breakTag : '$1'+ breakTag +'$2';
                return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
            },
        },
        computed: {
            getName() {
                return this.message.user.name
            },
            getProfilePhoto() {
                let photo = (this.message.user.photo) ? "/img/profile/"+ this.message.user.photo: '/img/profile/avatar.png';
                return photo;
            },
            nodes() {
                var comments = this.nl2br(this.message.comments);
                return comments.split(/##vuelink([\w]+)/)
            },
            userRead(){
                let me = this.message.user_id;
                if(this.message.users)
                    return this.message.users.filter(function(e){
                        return me != e.id
                    }).map(e => e.name).join(", ");
                else return '';
            }
        },
        mounted() {
            
        }
    }
</script>
