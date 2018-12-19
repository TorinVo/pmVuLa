<template>
    <div class="direct-chat-msg" :class="isMine?'right':'left'">
        <div class="direct-chat-info clearfix">
            <span class="direct-chat-name" :class="isMine?'float-right':'float-left'">{{ name }}</span>
            <span class="direct-chat-timestamp" :class="isMine?'float-left':'float-right'">{{ parseDate }}</span>
        </div>
        <!-- /.direct-chat-info -->
        <img class="direct-chat-img" :src="profileImage" alt="message user image"><!-- /.direct-chat-img -->
        <div class="direct-chat-text" v-html="message"></div>
        <!-- /.direct-chat-text -->
    </div>
</template>
<script>
    export default {
        name: 'DirectChatItem',
        props: {
            name: {
                type: String
            },
            date: {
                type: String
            },
            profileImage: {
                type: String
            },
            message: {
                type: String
            },
            isMine: {
                type: Boolean
            }
        },
        computed: {
            parseDate() {
                return this.date
            }
        },
        created() {}
    }

</script>
