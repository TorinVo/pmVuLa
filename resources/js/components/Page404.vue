<template>
    <div class="content" style="background: #fff;">
        <div class="container-fluid">
            <div class="row">
                <div id="notfound">
                    <div class="notfound">
                        <div class="notfound-404">
                            <h1>Oops!</h1>
                            <h2>404 - The Page can't be found</h2>
                        </div>
                        <router-link :to="{ name: 'dashboard' }">
                            Go TO Homepage
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
    
<script>
    export default {
        mounted() {
            
        }
    }
</script>