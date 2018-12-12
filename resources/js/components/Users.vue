<template>
    <div v-if="$gate.isAdminOrDesigner()">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"> <router-link to="/Dashboard">Dashboard</router-link></li>
                            <li class="breadcrumb-item active">Users</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title m-0">&nbsp;</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" @click="newUser()"><i class="fas fa-user-plus"></i>
                                        New</button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Type</th>
                                            <th>Register At</th>
                                            <th style="width: 60px">Modify</th>
                                        </tr>
                                        <tr v-for="(user, index) in users.data" :key="user.id">
                                            <td>{{index + 1}}.</td>
                                            <td>{{ user.name }}</td>
                                            <td>{{ user.email }}</td>
                                            <td>{{ user.type | upText }}</td>
                                            <td>{{ user.created_at | myDate }}</td>
                                            <td>
                                                <a href="javascript:void(0)" @click="editUser(user)">
                                                    <i class="fa fa-edit blue"></i>
                                                </a>
                                                /
                                                <a href="javascript:void(0)" @click="deleteUser(user.id)">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer clearfix">
                                <pagination :data="users" @pagination-change-page="getResults"></pagination>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form @submit.prevent="editmode ? updateUser() : createUser()">
                                    <div class="modal-header">
                                        <h5 v-if="!editmode" class="modal-title" id="userModalLabel">Add User</h5>
                                        <h5 v-else class="modal-title" id="userModalLabel">Update User's Info</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input v-model="form.name" type="text" name="name" id="name" class="form-control form-control-sm"
                                                :class="{ 'is-invalid': form.errors.has('name') }">
                                            <has-error :form="form" field="name"></has-error>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input v-model="form.email" :readonly="editmode ? true : false" type="email"
                                                name="email" id="email" class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('email') }">
                                            <has-error :form="form" field="email"></has-error>
                                        </div>

                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input v-model="form.password" type="password" name="password" id="password"
                                                class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('password') }">
                                            <has-error :form="form" field="password"></has-error>
                                        </div>

                                        <div class="form-group">
                                            <label for="type">Role</label>
                                            <select name="type" v-model="form.type" id="type" class="form-control form-control-sm"
                                                :class="{ 'is-invalid': form.errors.has('type') }">
                                                <option value="">Select User Role</option>
                                                <option value="Admin">Admin</option>
                                                <option value="Designer">Designer</option>
                                                <option value="Developer">Developer</option>
                                            </select>
                                            <has-error :form="form" field="type"></has-error>
                                        </div>

                                        <div class="form-group">
                                            <label for="bio">Short bio for user</label>
                                            <textarea v-model="form.bio" type="bio" name="bio" id="bio" class="form-control form-control-sm"
                                                :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                                            <has-error :form="form" field="email"></has-error>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                        <button v-if="editmode" type="submit" class="btn btn-success btn-sm">Update</button>
                                        <button v-else type="submit" class="btn btn-primary btn-sm">Create</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <div v-else>
        <not-found></not-found>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                editmode: false,
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
        methods: {
            getResults(page = 1) {
                axios.get('api/user?page=' + page)
                    .then(response => {
                        this.users = response.data;
                    });
            },
            newUser() {
                this.editmode = false;
                this.form.reset();
                $('#userModal').modal({
                    backdrop: 'static'
                });
            },

            editUser(user) {
                this.editmode = true;
                this.form.reset();
                $('#userModal').modal({
                    backdrop: 'static'
                });
                this.form.fill(user);
            },

            loadUsers() {
                if (this.$gate.isAdminOrDesigner()) {
                    this.$Progress.start();
                    axios.get('api/user').then(({
                        data
                    }) => (this.users = data));
                    this.$Progress.finish();
                }
            },

            createUser() {
                this.$Progress.start();
                this.form.post('api/user')
                    .then(() => {
                        toast({
                            type: 'success',
                            title: 'User Created successfully'
                        });
                        $('#userModal').modal('hide');
                        Fire.$emit('AfterCreate');
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            },

            updateUser() {
                this.form.put('api/user/' + this.form.id)
                    .then(() => {
                        swal('Updated!', 'Information has been updated.', 'success');
                        $('#userModal').modal('hide');
                        this.$Progress.finish();
                        Fire.$emit('AfterCreate');
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            },

            deleteUser(id) {
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    // Send request to the server
                    if (result.value) {
                        this.form.delete('api/user/' + id).then(() => {
                                swal('Deleted!', 'Your file has been deleted.', 'success');
                                Fire.$emit('AfterCreate');
                        }).catch(() => {
                            swal('Failed!', 'There was something wronge.', 'warning');
                        });
                    }
                });
            }
        },
        created() {
            this.loadUsers();
            Fire.$on('AfterCreate', () => {
                this.loadUsers();
            });
        },
        mounted() {
           
        }
    };

</script>
