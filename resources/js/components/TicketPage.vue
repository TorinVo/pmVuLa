<template>
    <div>
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-9">
                        <h4>##{{ ticket.id }} {{ ticket.title }} <small>Posted {{ ticket.created_at | myDate('MMM Do YYYY') }} by {{ ticket.user.name }}</small></h4>
                    </div>
                    <div class="col-sm-3">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'tickets' }">
                                    Tickets
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ticket.project.name}}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <!-- DIRECT CHAT WARNING -->
                        <chat-box @actionsTicket="actionsTicket" :post-id="ticket.id" :ticket="ticket"></chat-box>
                        <!--/.direct-chat -->
                    </div>
                    <div class="col-md-4">
                        <ChatAttach></ChatAttach>
                    </div>
                </div>
            </div>
        </section>
        <modal-window ref="imagemodal">
            <img :src="imageView" class="img-fluid">
        </modal-window>
    </div>
</template>

<script>
    import ChatAttach from './Attach.vue';
    import ImageCarousel from './ImageCarousel.vue';
    import ModalWindow from './ModalWindow.vue';
    export default {
        components: {
            ChatAttach,
            ImageCarousel,
            ModalWindow
        },

        data() {
            return {
                ticket: {
                    user: {},
                    project: {}
                },
                imageView: ''
            }
        },

        methods: {
            loadTicket() {
                axios.get('/api/ticket/' + this.$route.params.ticket)
                .then(response => {
                    this.ticket = response.data;
                })
                .catch(error => { })
            },
            actionsTicket(type, id){
                axios.post('/api/ticketactions', {
                    'id': id,
                    'type': type
                })
                .then(response => {
                    if(type == 'reviewed') this.ticket.reviewed = response.data.reviewed
                    else this.ticket.status = response.data.status
                    swal('Updated!', 'Information has been updated.', 'success');
                })
                .catch(error => {});
            },
            checkImage(imageSrc, succses, error) {
                var img = new Image();
                img.onload = succses; 
                img.onerror = error;
                img.src = imageSrc;
            }
        },

        created() {
            let vm = this;
            this.loadTicket();
            Fire.$on('showImage', (img) => {
                vm.checkImage(img, function() {
                    vm.imageView = img;
                    vm.$refs.imagemodal.modalOpen = true;
                },
                function() {
                    vm.imageView = '/img/attach/0.png';
                    vm.$refs.imagemodal.modalOpen = true;
                });
            });
        },

        mounted() {
            // Fire.$on('added_message', (message) => {
            //     this.comments.push(message.data);
            //     //this.$forceUpdate();
            //     //console.log(this.comments);
            // });
        },
        watch: {
            '$route': 'loadTicket'
        },
    };

</script>
