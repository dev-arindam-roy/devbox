<template>
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">My Account</span>
                            </div>
                            <div class="col-md-6 text-right">
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'myAccountRoute'}">My Profile</router-link></li>
                                    <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'myAccountRoute'}">Change Password</router-link></li>
                                    <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'myAccountRoute'}">Change Email-id</router-link></li>
                                    <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'myAccountRoute'}">Change Username</router-link></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    mounted() {
        this.$root.isPageLoadingActive = false;
    }
}
</script>
