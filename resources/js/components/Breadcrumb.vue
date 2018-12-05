<template>
    <div>
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Widgets</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/dashboard">
                                    Home
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
        </div>
        </section>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log("Component mounted.");
        }
    };

</script>
