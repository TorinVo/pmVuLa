<template>
    <div>
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Profile</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard' }">
                                    Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">User Profile</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle"  :src="getProfilePhoto() || 'img/avatar.png'">
                                </div>
                                <h3 class="profile-username text-center">{{ this.form.name }}</h3>
                                <p class="text-muted text-center">{{ this.form.type | upText }}</p>
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Open Projects</b> <a class="float-right">{{ this.form.projects }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Total Posts</b> <a class="float-right">0</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="card card-primary card-outline">
                            <div class="card-header p-0">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-tab active" href="#settings" data-toggle="tab">Account Settings</a>
                                    </li>
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="settings">
                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input v-model="form.name" type="text" name="name" id="name"
                                                    class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('name') }">
                                                <has-error :form="form" field="name"></has-error>
                                            </div>

                                            <div class="form-group">
                                                <label for="email">Email Address</label>
                                                <input v-model="form.email" :readonly="editmode ? true : false" type="email" name="email" id="email"
                                                    class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('email') }">
                                                <has-error :form="form" field="email"></has-error>
                                            </div>

                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input v-model="form.password" type="password" name="password" id="password"
                                                    class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('password') }">
                                                <has-error :form="form" field="password"></has-error>
                                            </div>

                                            <div class="form-group">
                                                <label for="photo" class="control-label">Profile Photo</label>
                                                <input type="file" @change="updateProfile" name="photo" class="form-control form-control-sm">
                                                
                                            </div>

                                            <div class="form-group">
                                                <label for="bio">Short bio for user</label>
                                                <textarea v-model="form.bio" type="bio" name="bio" id="bio"
                                                    class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                                                <has-error :form="form" field="email"></has-error>
                                            </div>
                                            <div class="form-group m-0">
                                                <button @click.prevent="updateInfo" type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
        </section>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                editmode: true,
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: 'avatar.png',
                    projects: 0
                })
            }
        },

        methods: {
            getProfilePhoto(){
                let photo = (this.form.photo.length > 200) ? this.form.photo : "img/profile/"+ this.form.photo;
                return photo;
            },

            updateInfo(){
                this.$Progress.start();
                if(this.form.password == ''){
                    this.form.password = undefined;
                }
                this.form.put('api/profile')
                .then(()=>{
                    Fire.$emit('AfterCreate');
                    this.$Progress.finish();
                    toast({
                        type: 'success',
                        title: 'Information has been updated.'
                    });
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },

            updateProfile(e){
                let file = e.target.files[0];
                let reader = new FileReader();
                let limit = 1024 * 1024 * 2;
                if(file['size'] > limit){
                    swal({
                        type: 'error',
                        title: 'Oops...',
                        text: 'You are uploading a large file',
                    })
                    return false;
                }
                reader.onloadend = (file) => {
                    this.form.photo = reader.result;
                }
                reader.readAsDataURL(file);
            }
        },

        mounted() {
            console.log("Component mounted.");
        },

        created() {
            this.$Progress.start();
            axios.get("api/profile")
            .then( response => {
                this.form.fill(response.data);
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
            });
        }
    };

</script>
