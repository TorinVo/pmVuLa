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
                    <link-item v-if="n % 2" :to="node" :key="n"></link-item>
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
                    <link-item v-if="n % 2" :to="node" :key="n"></link-item>
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
    Vue.component('link-item', {
        props:['to'],
        template:`<router-link v-if="typeLink.length == 1" :to="'/ticket/'+to">##{{to}}</router-link><a v-else href="javascript:void(0)" @click="showImage($event)">##i{{typeLink[1]}}</a>`,
        methods:{
            showImage(){
                if (event) event.preventDefault()
                let img = '/img/attach/'+this.typeLink[1]+'.'+this.typeLink[2];
                Fire.$emit('showImage', img);
            }
        },
        computed: {
            typeLink() {
                return this.to.split('i');
            }
        }
    })
        
    export default {
        props: {
            message: {
                user: {},
                users: {}
            }
        },
        methods: {
            
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
                return this.message.comments.split(/##vuelink([\w]+)/)
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
