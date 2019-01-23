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
                    <div class="col">
                        <div class="card card-success card-outline direct-chat direct-chat-success">
                            <div class="card-header d-flex p-0">
                                <h3 class="card-title p-2 mb-0">Description</h3>
                                <div class="card-tools"><button type="button" data-widget="collapse" class="btn btn-tool"><i class="fa fa-minus"></i></button></div>
                            </div>
                             <div class="card-body v-scroll" style="padding: 10px;max-height: 100px;">
                                <template v-for="(node, n) in description">
                                    <LinkItem v-if="n % 2" :to="node" :key="n"></LinkItem>
                                    <span v-html="node" v-else :key="n"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
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
    import { nl2br } from '../helpers';
    import ChatAttach from './Attach.vue';
    import ImageCarousel from './ImageCarousel.vue';
    import ModalWindow from './ModalWindow.vue';
    import LinkItem from './LinkItem.vue'
    export default {
        components: {
            ChatAttach,
            ImageCarousel,
            ModalWindow,
            LinkItem
        },

        data() {
            return {
                ticket: {
                    user: {},
                    project: {},
                    description: ''
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

        computed: {
            description() {
                var description = nl2br(this.ticket.description);
                return (description)?description.split(/##vuelink([\w]+)/):''
            },
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
