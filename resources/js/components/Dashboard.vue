<template>
    <div>
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title m-0">Projects Progress</h3> 
                            </div>
                            <div class="card-body">
                                <ul class="projects-progress">
                                    <li v-for="project in projects" :key="project.id">
                                        <h5>{{ project.name }}</h5>
                                        <div class="progress-group">
                                            <b>{{project.close}}</b>/{{project.open+project.close}}
                                            <span class="float-right">{{project.percent}}% </span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar progress-bar-striped bg-success" :class="{ 'bg-danger': project.status == 'close' }" role="progressbar" :style="{width: project.percent+'%'}" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">{{project.status | upText}}</div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-lg-6 col-6">
                                <!-- small card -->
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{myPosts}}</h3>
                                        <p>Total My Posts</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6">
                                <!-- small card -->
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{this.$root.ticketOpen}}</h3>
                                        <p>Total Posts Open</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    export default {
        beforeMount() {
            this.$store.dispatch('actionProjectAllFetch')
            this.$store.dispatch('actionMyPostFetch')
        },
        created () {
        },
        computed: {
            projects() {
                return this.$store.state.storeProject.projects
            },
            myPosts(){
                return this.$store.state.storePost.myPost
            }
        },
    }
</script>