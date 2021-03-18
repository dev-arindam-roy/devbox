<template>
    <ul class="list-group list-group-flush">
        <li class="list-group-item" v-show="$route.name != 'myAccountRoute'"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'myAccountRoute'}">My Account</router-link></li>
        <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'editProfileRoute'}">Edit Profile</router-link></li>
        <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'changePasswordRoute'}">Change Password</router-link></li>
        <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'changeEmailRoute'}">Change Email-id</router-link></li>
        <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> <router-link :to="{name: 'changeUsernameRoute'}">Change Username</router-link></li>
    </ul>
</template>

<script>
export default {
    mounted() {
        
    }
}
</script>
