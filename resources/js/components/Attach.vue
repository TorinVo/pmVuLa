<template>
    <div class="card card-success card-outline">
        <div class="card-header d-flex p-0">
            <h3 class="card-title p-2 mb-0">Attach</h3>
            <ul class="nav nav-pills ml-auto" style="padding-right: 0px !important;">
                <li class="nav-item" @click="newImage = 0"><a class="nav-tab active" href="#tab_list" data-toggle="tab">List <span v-if="newImage > 0" class="right badge badge-danger">New {{newImage}}</span></a></li>
                <li class="nav-item"><a class="nav-tab" href="#tab_upload" data-toggle="tab">Upload</a></li>
            </ul>
        </div>
        <modal-window ref="videomodal">

        </modal-window>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab_list" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" @keydown="typing" v-model="videolink" placeholder="Video Link (YouTube)">
                                <span class="input-group-append">
                                <button type="button" @click="openVideo()" class="btn btn-success">Open</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row wap-at-img">
                        <div v-for="(image, index) in images" :key="index" class="col-6 col-md-12 col-xl-6 mb-3">
                            <div class="at-img" :style="{ backgroundImage: 'url(' + image.name + ')' }" @click="showImage(image.name)">
                                <div class="title">
                                    <a href="javascript:void(0)">##i{{image.id}}</a>
                                    <i class="fa fa-times" @click.stop="deleteImage(image.id, index)"></i>
                                </div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab_upload" role="tabpanel">
                    <div class="row wap-at-img">
                        <div class="col-6 col-md-12 col-xl-6 mb-3">
                            <div class="at-img" :class="{active: selected}"  @click="selected = true"></div>
                        </div>
                        <div class="col-6 col-md-12 col-xl-6 mb-3">
                            <strong>Instructions</strong>: Copy an image to clipboard (For instance: <strong>Mac</strong>: <code>cmd+shift+ctrl+4</code> and  <strong>Win</strong>: <code>Alt+PtrScr</code>), click on the <i>target div</i> to paste the image into, and paste <code>cmd+V</code> or <code>ctrl+v</code>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal-window ref="videoModal">
            <iframe width="100%" height="75%" :src="viedeoEmbed" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </modal-window>
    </div>
    
</template>

<script>
    import ModalWindow from './ModalWindow.vue';
    export default {
        components: {
            ModalWindow
        },
        data() {
            return {
                selected: '',
                videolink: '',
                viedeoEmbed: '',
                newImage: 0,
                images: []
            }
        },
        methods: {
            loadImage() {
                var vm = this;
                axios.get('/api/image/' + this.$route.params.ticket)
                .then(response => {
                    this.images = response.data
                    
                })
                .catch(error => {
                    console.log(error)
                })
            },
            showImage(img){
                Fire.$emit('showImage', img);
            },
            deleteImage(id, index) {
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios.delete('/api/image/' + id).then(() => {
                            swal('Deleted!', 'Your file has been deleted.', 'success');
                            this.images.splice(index, 1);
                        }).catch(() => {
                            swal('Failed!', 'There was something wronge.', 'warning');
                        });
                    }
                });
            },
            typing(e) {
                if(e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.openVideo();
                }        
            },
            openVideo(){
                if(!this.videolink || this.videolink.trim() === '') {
                    return
                }
                let pattern = '/%^(?:https?://)?(?:www\.)?(?:youtu\.be/ | youtube\.com(?:/embed/ | /v/ | /watch\?v=))([\w-]{10,12})$%x/';
                let match = getVideoId(this.videolink);
                if(match && match.service == 'youtube'){
                    this.viedeoEmbed = 'https://www.youtube.com/embed/'+match.id;
                    this.$refs.videoModal.modalOpen = true;
                }
            }
        },
        created() {
            this.loadImage()
            Fire.$on('vModalClose', () => {
                this.viedeoEmbed = ''
            });
        },
        watch: {
            '$route': 'loadImage'
        },
        mounted() {
            let vm = this;
            this.$nextTick(function () {
                $("html").pasteImageReader(function(results) {
                    var dataURL, filename;
                    filename = results.filename, dataURL = results.dataURL;
                    // $data.text(dataURL);
                    // $size.val(results.file.size);
                    // $type.val(results.file.type);
                    // $test.attr('href', dataURL);
                    var img = document.createElement('img');
                    img.src= dataURL;
                    var w = img.width;
                    var h = img.height;
                    axios.post('/api/image', {
                        post_id: vm.$route.params.ticket,
                        photo: dataURL
                    })
                    .then(function (response) {
                        //console.log(response);
                        Fire.$emit('added_image', response.data);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                    return $(".wap-at-img .active").css({
                        backgroundImage: "url(" + dataURL + ")"
                    });
                });
            });

            Fire.$on('added_image', (image) => {
                this.images.push(image)
                this.newImage++
                toast({type: 'success', title: '##i'+image.id+' Successfully'})
                setTimeout(function(){
                    $(".wap-at-img .active").css({
                        backgroundImage: ""
                    })
                    vm.selected = false
                }, 1000)
            });

            Echo.private('images.' + this.$route.params.ticket)
            .listen('MessageImageCreate', ({image}) => {
                this.images.push(image)
                this.newImage++
                console.log(image);
            });
        }
    }
</script>

