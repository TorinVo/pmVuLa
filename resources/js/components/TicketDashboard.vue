<template>
    <div>
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Tickets</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/dashboard">
                                    Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">Tickets</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title m-0">Filters</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-2">
                                <form action="" method="get">
                                    <div class="row row-eq-heigh">
                                        <div class="col-md-6">
                                            <div class="card mb-2">
                                                <div class="card-header">
                                                    <h6 class="m-0">By Date</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-calendar"></i>
                                                                </span>
                                                            </div>
                                                            <input type="text" :value="getFilterDate" class="form-control float-right active form-control-sm"
                                                                readonly id="reservation">
                                                        </div>
                                                        <!-- /.input group -->
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <button type="button" style="min-width:120px;" @click="changeDate(-30)"
                                                            class="btn btn-primary btn-sm mb-1" :class="{ disabled: (form.btnActive == 30) ? true : false }">Last
                                                            30 days</button>
                                                        <button type="button" style="min-width:120px;" @click="changeDate(-60)"
                                                            class="btn btn-primary btn-sm mb-1" :class="{ disabled: (form.btnActive == 60) ? true : false }">Last
                                                            60 days</button>
                                                        <button type="button" style="min-width:120px;" @click="changeDate(-90)"
                                                            class="btn btn-primary btn-sm mb-1" :class="{ disabled: (form.btnActive == 90) ? true : false }">Last
                                                            90 days</button>
                                                        <button type="button" style="min-width:120px;" @click="changeDate(-365)"
                                                            class="btn btn-primary btn-sm mb-1" :class="{ disabled: (form.btnActive == 365) ? true : false }">Last
                                                            365 days</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card mb-2">
                                                <div class="card-header">
                                                    <h6 class=" m-0">By Project Type</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <select class="form-control form-control-sm"
                                                            v-model="form.projectSelect">
                                                            <option value="0">All</option>
                                                            <option v-for="project in projects" :value="project.id"
                                                                :key="project.id">{{ project.name }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" v-model="form.hiddenClose"
                                                            class="form-check-input" id="txt_hd_close">
                                                        <label class="form-check-label" for="exampleCheck1">Hide closed
                                                            issues</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title m-0">&nbsp;</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" @click="newTicket()"><i class="fas fa-plus-square"></i>
                                        Add Entry
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0">

                                <div class="overlay" v-if="this.loading">
                                    <i class="fas fa-sync-alt fa-spin"></i>
                                </div>

                                <table class="table table-striped table-ticket">
                                    <tbody>
                                        <tr>
                                            <th>Project Type</th>
                                            <th>Issue # </th>
                                            <th>Issue Title</th>
                                            <th>Date Posted</th>
                                            <th style="width: 40px">Reviewed</th>
                                            <th style="width: 125px">New Comment</th>
                                            <th style="width: 40px">Status</th>
                                            <th style="width: 60px">Modify</th>
                                        </tr>
                                        <tr v-for="ticket in tickets.data" :key="ticket.id">
                                            <td>
                                                <a href="javascript:void(0)" @click="form.projectSelect = ticket.project.id">
                                                    {{ ticket.project.name }}
                                                </a>
                                            </td>
                                            <td>
                                                <router-link :to="{ name: 'ticket', params: { ticket: ticket.id } }">
                                                    ##{{ ticket.id }}
                                                 </router-link>
                                            </td>
                                            <td>{{ ticket.title }}</td>
                                            <td>{{ ticket.created_at | myDate('MM/DD/YYYY') }}</td>
                                            <td class="center">
                                                <i class="tb-icon fas fa-user-check" :class="{ 'text-info': ticket.reviewed }"></i>
                                            </td>
                                            <td class="center">
                                                <i class="tb-icon fas fa-comments text-info"></i>
                                            </td>
                                            <td class="center">
                                                <i class="tb-icon fas fa-dot-circle text-info"></i>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" @click="editTicket(ticket)">
                                                    <i class="fa fa-edit blue"></i>
                                                </a>
                                                /
                                                <a href="javascript:void(0)" @click="deleteTicket(ticket.id)">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                <pagination :data="tickets" @pagination-change-page="getResults"></pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Modal -->
        <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateTicket() : createTicket()">
                        <div class="modal-header">
                            <h5 v-if="!editmode" class="modal-title" id="ticketModalLabel">Add Ticket</h5>
                            <h5 v-else class="modal-title" id="ticketModalLabel">Update Ticket's Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="title">Tilte</label>
                                <input v-model="frmData.title" type="text" name="title" id="title" class="form-control form-control-sm"
                                    :class="{ 'is-invalid': frmData.errors.has('title') }">
                                <has-error :form="frmData" field="title"></has-error>
                            </div>

                            <div class="form-group">
                                <label for="project_id">Project</label>
                                <select class="form-control form-control-sm" name="project_id" v-model="frmData.project_id"
                                    :class="{ 'is-invalid': frmData.errors.has('project_id') }">
                                    <option value="">-</option>
                                    <option v-for="project in projects" :value="project.id" :key="project.id">{{
                                        project.name }}</option>
                                </select>
                                <has-error :form="frmData" field="project_id"></has-error>
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
</template>

<script>
    import moment from 'moment';
    export default {
        data() {
            return {
                loading: false,
                projects: {},
                tickets: {},
                filter: {},
                editmode: false,
                form: new Form({
                    dateto: moment(new Date()).format('MM/DD/YYYY'),
                    datefrom: moment(new Date()).format('MM/DD/YYYY'),
                    hiddenClose: true,
                    projectSelect: 1,
                    btnActive: 0
                }),
                frmData: new Form({
                    id: '',
                    title: '',
                    project_id: ''
                })
            }
        },

        methods: {
            loadProjects() {
                axios.get('api/getproject')
                    .then(response => {
                        this.projects = response.data.projects;
                        this.form.fill(response.data.filter);
                    });
            },

            changeDate(num) {
                let today = moment(new Date());

                this.form.dateto = today.format('MM/DD/YYYY');
                this.form.datefrom = today.add(num, 'days').format('MM/DD/YYYY');
                this.form.btnActive = Math.abs(num);

                $('#reservation').data('daterangepicker').setStartDate(this.form.datefrom);
                $('#reservation').data('daterangepicker').setEndDate(this.form.dateto);
            },

            newTicket() {
                this.editmode = false;
                this.frmData.reset();
                $('#ticketModal').modal({
                    backdrop: 'static'
                });
            },

            createTicket() {
                this.$Progress.start();
                this.frmData.post('api/ticket')
                    .then(() => {
                        toast({
                            type: 'success',
                            title: 'Ticket Created successfully'
                        });
                        $('#ticketModal').modal('hide');
                        Fire.$emit('AfterCreate');
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            },

            loadTickets() {
                this.$Progress.start();
                this.loading = true;
                axios.get('api/ticket', {
                    params: this.getFilterData
                }).then(response => {
                    this.tickets = response.data;
                    this.loading = false;
                });
                this.$Progress.finish();
            },

            getResults(page = 1) {
                this.$Progress.start();
                this.loading = true;
                axios.get('api/ticket?page=' + page, {
                        params: this.getFilterData
                    })
                    .then(response => {
                        this.tickets = response.data;
                        this.loading = false;
                    });
                this.$Progress.finish();
            },

            editTicket(ticket) {
                this.editmode = true;
                this.frmData.reset();
                $('#ticketModal').modal({
                    backdrop: 'static'
                });
                this.frmData.fill(ticket);
            },

            updateTicket() {
                this.frmData.put('api/ticket/' + this.frmData.id)
                    .then(() => {
                        swal('Updated!', 'Information has been updated.', 'success');
                        $('#ticketModal').modal('hide');
                        this.$Progress.finish();
                        Fire.$emit('AfterCreate');
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            },

            deleteTicket(id) {
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
                    this.form.delete('api/ticket/' + id).then(() => {
                        if (result.value) {
                            swal('Deleted!', 'Your file has been deleted.', 'success');
                            Fire.$emit('AfterCreate');
                        }
                    }).catch(() => {
                        swal('Failed!', 'There was something wronge.', 'warning');
                    });
                });
            }
        },

        created() {
            this.loadProjects();
            //this.loadTickets();

            Fire.$on('AfterCreate', () => {
                this.loadTickets();
            });
        },
        computed: {
            getFilterDate() {
                return this.form.datefrom + ' - ' + this.form.dateto;
            },

            getFilterData() {
                let data = {
                    'datefrom': this.form.datefrom,
                    'dateto': this.form.dateto,
                    'hiddenClose': this.form.hiddenClose,
                    'projectSelect': this.form.projectSelect,
                    'btnActive': this.form.btnActive
                };
                return data;
            }
        },
        watch: {
            getFilterData: function () {
                $('#reservation').data('daterangepicker').setStartDate(this.form.datefrom);
                $('#reservation').data('daterangepicker').setEndDate(this.form.dateto);
                this.loadTickets();
            }
        },

        mounted() {
            this.$nextTick(() => {
                let vm = this;
                if ($('#reservation')) {
                    $('#reservation').daterangepicker({
                        autoUpdateInput: false,
                        autoApply: true
                    });

                    $('#reservation').on('apply.daterangepicker', function (ev, picker) {
                        vm.form.datefrom = picker.startDate.format('MM/DD/YYYY');
                        vm.form.dateto = picker.endDate.format('MM/DD/YYYY');
                        vm.form.btnActive = 0;
                    });
                }
            })
        }
    };

</script>

<style lang="scss" scoped>
    .table-ticket {
        .tb-icon {
            font-size: 1.2rem;
        }

        ;

        .center {
            text-align: center;
        }
    }

    ;

</style>
