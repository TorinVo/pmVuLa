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
                                <router-link to="/ticket">
                                    Tickets
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">Widgets</li>
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
                        <chat-box :post-id="ticket.id"></chat-box>
                        <!--/.direct-chat -->
                    </div>
                    <div class="col-md-4">
                        <ChatAttach @showImage="showImage"></ChatAttach>
                    </div>
                </div>
            </div>
        </section>
        <modal-window ref="imagemodal">
           
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
                    user: {}
                }
            }
        },

        methods: {
            loadTicket() {
                axios.get('/api/ticket/' + this.$route.params.ticket)
                .then(response => {
                    this.ticket = response.data;
                })
                .catch(error => {
                    console.log(error)
                })
            },
            openModal() {
                this.$refs.imagemodal.modalOpen = true;
            },
            showImage(image) {
                this.$refs.imagemodal.image = image;
                this.$refs.imagemodal.modalOpen = true;
            }
        },

        created() {
            this.loadTicket();
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
