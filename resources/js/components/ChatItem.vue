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
            <!-- <div class="direct-chat-info clearfix">
                <span class="direct-chat-timestamp float-right">Seen by Alger</span>
            </div> -->
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
            <!-- <div class="direct-chat-info clearfix">
                <span class="direct-chat-timestamp">Seen by Alger, Torin</span>
            </div> -->
            <!-- /.direct-chat-text -->
        </div>
        <!-- /.direct-chat-msg -->
    </div>
</template>

<script>
import router from '../router';
    Vue.component('link-item', {
        props:['to'],
        template:`<router-link v-if="typeLink.length == 1" :to="'/ticket/'+to">##{{to}}</router-link><a href="javascript:void(0)" @click="openImg($event)" v-else>##{{to}}</a>`,
        methods:{
            openImg(){
                if (event) event.preventDefault()
                alert(this.typeLink[1]);
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
                user: {}
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
            }
        },
        mounted() {
            
        }
    }
</script>
