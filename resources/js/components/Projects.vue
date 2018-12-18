<template>
    <div>
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Projects</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard' }">
                                    Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">Projects</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title m-0">&nbsp;</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" @click="newProject()"><i class="fas fa-plus-square"></i>
                                        New</button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>Title</th>
                                            <th>Register At</th>
                                            <th>Progress</th>
                                            <th style="width: 40px">Status</th>
                                            <th style="width: 60px">Modify</th>
                                        </tr>
                                        <tr v-for="(project, index) in projects.data" :key="project.id">
                                            <td>{{index + 1}}.</td>
                                            <td>
                                                <div v-if="project.url">
                                                    <a :href="project.url" target="_black">{{ project.name }}</a>
                                                </div>
                                                <div v-else>
                                                    {{ project.name }}
                                                </div>
                                            </td>
                                            <td>{{ project.created_at | myDate }}</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" :style="{width: project.percent+'%'}" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">{{project.percent}}%</div>
                                                </div>
                                            </td>
                                            <td class="center">
                                                <span class="badge" :class="{ 'badge-primary':project.status == 'open', 'badge-danger':project.status == 'close' }" style="padding: .5em;min-width: 40px;">{{project.status | upText}}</span>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" @click="editProject(project)">
                                                    <i class="fa fa-edit blue"></i>
                                                </a>
                                                /
                                                <a href="javascript:void(0)" @click="deleteProject(project.id)">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer clearfix">
                                <pagination :data="projects" @pagination-change-page="getResults"></pagination>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="projectModal" tabindex="-1" role="dialog" aria-labelledby="projectModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form @submit.prevent="editmode ? updateProject() : createProject()">
                            <div class="modal-header">
                                <h5 v-if="!editmode" class="modal-title" id="projectModalLabel">Add Project</h5>
                                <h5 v-else class="modal-title" id="projectModalLabel">Update Project's Info</h5>
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
                                    <label for="url">Url</label>
                                    <input v-model="form.url" type="url" name="url" id="url" class="form-control form-control-sm"
                                        :class="{ 'is-invalid': form.errors.has('url') }">
                                    <has-error :form="form" field="url"></has-error>
                                </div>

                                <div class="form-group" v-if="editmode">
                                    <label for="status">Status</label>
                                    <select name="status" v-model="form.status" id="status" class="form-control form-control-sm"
                                        :class="{ 'is-invalid': form.errors.has('status') }">
                                        <option value="open">Open</option>
                                        <option value="close">Close</option>
                                    </select>
                                    <has-error :form="form" field="status"></has-error>
                                </div>

                                <div class="form-group">
                                    <label for="describe">Describe</label>
                                    <textarea v-model="form.describe" type="describe" name="describe" id="describe" class="form-control form-control-sm"
                                        :class="{ 'is-invalid': form.errors.has('describe') }"></textarea>
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
        </section>
    </div>
</template>

<script>
    export default {
        beforeMount() {
            if (this.$gate.isAdminOrDesigner()) {
                this.$store.dispatch('actionProjectFetch')
            }
        },
        data() {
            return{
                editmode: false,
                page: '',
                //projects: {},
                form: new Form({
                    id: '',
                    name: '',
                    url: '',
                    discribe: '',
                    status: 'open'
                })
            }
        },

        methods: {
            getResults(page = 1) {
                this.page = page;
                this.$store.dispatch('actionProjectFetch', this.page)
                // this.$Progress.start();
                // axios.get('api/project?page=' + page)
                // .then(response => {
                //     this.projects = response.data;
                //     this.$Progress.finish();
                // })
                // .catch(() => {
                //     this.$Progress.fail();
                // });
            },

            loadProjects() {
                if (this.$gate.isAdminOrDesigner()) {
                    this.$Progress.start();
                    axios.get('api/project').then(({
                        data
                    }) => (this.projects = data));
                    this.$Progress.finish();
                }
            },

            newProject() {
                this.editmode = false;
                this.form.reset();
                $('#projectModal').modal({
                    backdrop: 'static'
                });
            },

            createProject() {
                this.$Progress.start();
                this.form.post('api/project')
                    .then(() => {
                        toast({
                            type: 'success',
                            title: 'Project Created successfully'
                        });
                        $('#projectModal').modal('hide');
                        Fire.$emit('AfterCreate');
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            },

            editProject(project) {
                this.editmode = true;
                this.form.reset();
                $('#projectModal').modal({
                    backdrop: 'static'
                });
                this.form.fill(project);
            },

            updateProject() {
                this.form.put('api/project/' + this.form.id)
                .then(() => {
                    swal('Updated!', 'Information has been updated.', 'success');
                    $('#projectModal').modal('hide');
                    this.$Progress.finish();
                    Fire.$emit('AfterCreate');
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },

            deleteProject(id) {
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
                        this.form.delete('api/project/' + id).then(() => {
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
            //this.loadProjects();
            Fire.$on('AfterCreate', () => {
                //this.loadProjects();
                this.$store.dispatch('actionProjectFetch')
            });
        },
        computed: {
            projects() {
                return this.$store.state.storeProject.projects
            },
        },
        mounted() {
        }
    }

</script>
