<template>
    <router-link v-if="typeLink.length == 1" :to="'/ticket/'+to">##{{to}}</router-link>
    <a v-else href="javascript:void(0)" @click="showImage($event)">##i{{typeLink[1]}}</a>
</template>

<script>
    export default {
        props:['to'],
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
    }
</script>