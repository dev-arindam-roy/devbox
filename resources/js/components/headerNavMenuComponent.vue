<template>
    <nav class="navbar navbar-expand-lg navbar-dark navbg">
        <router-link class="navbar-brand" :to="{name: 'rootRoute'}">
            <img src="/images/devbox_64.png" style="width:32px;">
            <span><strong>DevBox</strong></span>
        </router-link>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto" v-show="$root.isUserLoggedIn">
                <li class="nav-item">
                    <router-link class="nav-link" :to="{name: 'homeRoute'}">Home</router-link>
                </li>
                <li class="nav-item">
                    <router-link class="nav-link" :to="{name: 'myAccountRoute'}">My Account</router-link>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);" @click="logout">Logout</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto" v-show="!$root.isUserLoggedIn">
                <li class="nav-item" v-show="$route.name == 'registrationRoute' || $route.name == 'rootRoute'">
                    <router-link class="nav-link" :to="{name: 'loginRoute'}">Login</router-link>
                </li>
                <li class="nav-item" v-show="$route.name == 'loginRoute'  || $route.name == 'rootRoute'">
                    <router-link class="nav-link" :to="{name: 'registrationRoute'}">Create Account</router-link>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
export default {
    props:{
        
    },
    data() {
        return {
            
        }
    },
    methods: {
        async logout() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.post('/auth/signout').then(response => {
                if (response.data.status == 200) {
                    //_this.$root.isUserLoggedIn = false;
                    //_this.$root.appUserInfo = [];
                    window.location.href = "/";
                }
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        }
    },
    mounted() {
    }
}
</script>
