<template>
    <div>
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'dashboard' }">
                                    Home
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">Events</li>
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
                                    <button type="button" class="btn btn-primary btn-sm" @click="newEvent()"><i class="fas fa-plus-square"></i>
                                        New</button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <VACalendar ref="calendar"
                                    :event-sources="eventSources"
                                    @event-selected="eventSelected"
                                    @day-click="dayClick"
                                    :editable="false">
                                </VACalendar>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Modal -->
        <div class="modal fade v-scroll" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form @submit.prevent="editmode ? updateEvent() : createEvent()">
                        <div class="modal-header">
                            <h5 v-if="!editmode" class="modal-title" id="eventModalLabel">Add Event</h5>
                            <h5 v-else class="modal-title" id="eventModalLabel">Update Event's Info</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="title" class=" col-md-3">Title</label>
                                <div class=" col-md-9">
                                    <input v-model="form.title" type="text" name="title" id="title" class="form-control form-control-sm"
                                    :class="{ 'is-invalid': form.errors.has('title') }">
                                    <has-error :form="form" field="title"></has-error>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="description" class=" col-md-3">Description</label>
                                <div class=" col-md-9">
                                    <textarea v-model="form.description" type="description" name="description" id="description" class="form-control form-control-sm"
                                    :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                                    <has-error :form="form" field="description"></has-error>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="start" class=" col-md-3">Start date</label>
                                <div class=" col-md-9">
                                    <date-picker name="start" id="start" v-model="form.start" :config="options" class="form-control-sm" :class="{ 'is-invalid': form.errors.has('start') }"></date-picker>
                                    <has-error :form="form" field="start"></has-error>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="end-date" class=" col-md-3">End date</label>
                                <div class=" col-md-9">
                                    <date-picker name="end" id="end" v-model="form.end" :config="options" class="form-control-sm" :class="{ 'is-invalid': form.errors.has('end') }"></date-picker>
                                    <has-error :form="form" field="end"></has-error>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="event_recurring" class="col-xs-3 col-3">Repeat</label>
                                <div class="col-xs-9 col-9">
                                    <div class="custom-control custom-checkbox">
                                        <input v-model="form.repeat" type="checkbox" class="custom-control-input" id="customCheck1">
                                        <label class="custom-control-label" for="customCheck1">&nbsp</label>
                                    </div>
                                </div>
                            </div>
                            <transition name="fade" mode="out-in">
                                <div v-show="form.repeat">
                                    <div class="form-group row">
                                        <label for="repeat_interval" class=" col-md-3">Repeat every</label>
                                        <div class="col-md-4">
                                            <input v-model="form.repeat_interval" type="number" name="repeat_interval" id="repeat_interval" min="1" class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('repeat_interval') }">
                                        </div>
                                        <div class="col-md-5">
                                            <select v-model="form.repeat_type" name="repeat_type" class="form-control form-control-sm" id="repeat_type" tabindex="-1">
                                                <option value="daily">Day(s)</option>
                                                <option value="weekly">Week(s)</option>
                                                <option value="monthly">Month(s)</option>
                                                <option value="yearly">Year(s)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="no_of_cycles" class=" col-md-3">Ends</label>
                                            <div class="col-md-9">
                                                <div class="custom-control custom-radio">
                                                    <input v-model="form.repeat_end" type="radio" id="customRadio1" value="0" name="customRadio" class="custom-control-input">
                                                    <label class="custom-control-label" for="customRadio1">Never</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 offset-md-3">
                                                <div class="custom-control custom-radio">
                                                    <input v-model="form.repeat_end" type="radio" id="customRadio2" value="1" name="customRadio" class="custom-control-input">
                                                    <label class="custom-control-label" for="customRadio2">After</label>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group mb-3 input-group-sm">
                                                    <input v-model="form.repeat_limit" :readonly="(form.repeat_end  == '0')" type="number" name="no_of_cycles" id="no_of_cycles" min="1" class="form-control form-control-sm" :class="{ 'is-invalid': form.errors.has('repeat_limit') }">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">occurrences</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                            <div class="form-group row">
                                <label for="end-date" class=" col-md-3"></label>
                                <div class="col-md-9">
                                    <div class="color-palet">
                                        <span v-for="(color, index) in colors" :key="index" :style="{ backgroundColor: color}" :class="{active:(color == form.backgroundColor)}" @click="form.backgroundColor = color" class="color-tag mr15"></span>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="modal-footer">
                            <button v-if="editmode" @click="deleteEvent(form.id)" type="button" class="btn btn-danger mr-auto btn-sm">Delete</button>
                        
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
    import VACalendar from './widgets/VACalendar.vue'
    import moment from 'moment';
    export default {
        components: {
            VACalendar
        },
        data () {
            return {
                editmode: false,
                form: new Form({
                    id: '',
                    title: '',
                    description: '',
                    start: '',
                    end: '',
                    backgroundColor: '#83c340',
                    event_type: '',
                    repeat_type: '',
                    repeat_interval: 1,
                    repeat_days: '',
                    repeat_limit: '',
                    repeat: '0',
                    repeat_end: 0
                }),
                colors:[
                    '#83c340',
                    '#29c2c2',
                    '#2d9cdb',
                    '#aab7b7',
                    '#f1c40f',
                    '#e18a00',
                    '#e74c3c',
                    '#d43480',
                    '#ad159e',
                    '#37b4e1',
                    '#34495e',
                    '#dbadff'
                ],
                options: {
                    format: 'YYYY-MM-DD HH:mm',
                    useCurrent: false,
                    showClose: true,
                },
                eventSources: [
                    {
                        events(start, end, timezone, callback) {
                            self.axios.get(`/api/event`, {
                                params:{
                                    timezone: timezone,
                                    start: moment(start).format('YYYY-MM-DD'),
                                    end: moment(end).format('YYYY-MM-DD')
                                }
                            }).then(response => {
                                callback(response.data.data)
                            })
                        },
                        color: 'yellow',
                        textColor: '#fff',
                    }
                ]
            }
        },
         methods: {
            eventSelected(event, jsEvent, view){
                var data = {
                    id: event.id,
                    title: event.title,
                    description: event.description,
                    start: moment(event.start).format('YYYY-MM-DD HH:mm'),
                    end: moment(event.end).format('YYYY-MM-DD HH:mm'),
                    backgroundColor: event.backgroundColor,
                    event_type: event.event_type,
                    repeat_type: (event.repeat_type) ? event.repeat_type : 'daily',
                    repeat_interval: (event.repeat_interval) ? event.repeat_interval : 1,
                    repeat_days: event.repeat_days,
                    repeat_limit: event.repeat_limit,
                    repeat: (event.repeat_type) ? 1 : 0,
                    repeat_end: (event.repeat_limit) ? 1 : 0
                };
                this.editmode = true;
                this.form.reset();
                $('#eventModal').modal({
                    backdrop: 'static'
                });
                 this.form.fill(data);
            },
            dayClick(date, jsEvent, view){
                this.editmode = false;
                this.form.reset();

                var start = moment(date).format('YYYY-MM-DD HH:mm')
                var data = {
                    start: start,
                    end: moment(start).add(30, 'minutes').format('YYYY-MM-DD HH:mm'),
                    backgroundColor: '#83c340',
                    repeat_type: 'daily',
                    repeat_interval: 1,
                    repeat_end: 0
                };

                $('#eventModal').modal({
                    backdrop: 'static'
                });

                this.form.fill(data);
            },
            newEvent(){
                this.editmode = false;
                this.form.reset();

                var start = moment().format('YYYY-MM-DD HH:mm')
                var data = {
                    start: start,
                    end: moment(start).add(30, 'minutes').format('YYYY-MM-DD HH:mm'),
                    backgroundColor: '#83c340',
                    repeat_type: 'daily',
                    repeat_interval: 1,
                    repeat_end: 0
                };

                $('#eventModal').modal({
                    backdrop: 'static'
                });

                this.form.fill(data);
            },
            createEvent(){
                this.$Progress.start();
                this.form.post('api/event')
                .then(() => {
                    toast({
                        type: 'success',
                        title: 'Event Created successfully'
                    });
                    $('#eventModal').modal('hide')
                    this.$refs.calendar.$emit('refetch-events')
                    this.$Progress.finish();
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            updateEvent(){
                this.$Progress.start();
                this.form.put('api/event/' + this.form.id)
                .then(() => {
                    swal('Updated!', 'Information has been updated.', 'success')
                    $('#eventModal').modal('hide')
                    this.$refs.calendar.$emit('refetch-events')
                    this.$Progress.finish();
                    //Fire.$emit('AfterCreate');
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            deleteEvent(id){
                $('#eventModal').modal('hide')
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
                        this.form.delete('api/event/' + id).then(() => {
                            swal('Deleted!', 'Your file has been deleted.', 'success')
                            this.$refs.calendar.$emit('refetch-events')
                        }).catch(() => {
                            swal('Failed!', 'There was something wronge.', 'warning');
                        });
                    }
                });
            }
         },
         watch: {
            
         }
    }
</script>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .3s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
    .color-tag {
        display: inline-block;
        width: 15px;
        height: 15px;
        margin: 2px 10px 0 0;
        transition: all 300ms ease;
        -moz-transition: all 0.1s;
        -webkit-transition: all 0.1s;
        transition: all 0.1s;
        cursor: pointer;
    }
    .color-tag:hover {
        -moz-transform: scale(1.5);
        -webkit-transform: scale(1.5);
        transform: scale(1.5);
    }
    .color-tag.active {
        border-radius: 50%;
    }
</style>
