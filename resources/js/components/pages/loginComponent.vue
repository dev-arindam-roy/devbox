<template>
    <form @submit.prevent="loginSubmit" autocomplete="off">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="box-title"><i class="fas fa-sign-in-alt"></i> Login</span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <validationErrorComponent :validationErrorListArr="validationErrors"></validationErrorComponent>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-lebel">Username / Email: <em>*</em></label>
                                            <input type="text" v-model.trim="userNameEmail" class="form-control" placeholder="Username or Email" autocomplete="off">
                                            <div class="text-danger" v-if="!$v.userNameEmail.required && $v.userNameEmail.$error">Please enter Username or Email</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-lebel">Password: <em>*</em></label>
                                            <input type="password" v-model.trim="password" class="form-control" placeholder="Password" autocomplete="off">
                                            <div class="text-danger" v-if="!$v.password.required && $v.password.$error">Please enter password</div>
                                        </div>
                                        <div class="form-group">
                                            <input type="checkbox" v-model.trim="rememberMe">
                                            <label class="form-lebel">Remember Me</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success">Login</button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <router-link :to="{name: 'registrationRoute'}" class="btn btn-primary navbg">Create Account</router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>
    </form>
</template>

<script>
import { required } from 'vuelidate/lib/validators'
import validationErrorComponent from '../validationErrorsComponent.vue'
export default {
    components: {
        validationErrorComponent
    },
    data() {
        return {
            validationErrors: [],
            userNameEmail: '',
            password: '',
            rememberMe: false,
        }
    },
    watch: {

    },
    computed: {

    },
    validations: {
        userNameEmail: {
            required
        },
        password: {
            required
        }
    },
    methods: {
        loginSubmit() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                _this.loginProcess();
            }
        },
        async loginProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/auth/signin";
            const apiProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    userNameEmail: _this.userNameEmail,
                    password: _this.password,
                    rememberMe: _this.rememberMe
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.validationErrors = [];
                    _this.$v.$reset();
                    _this.$cookies.remove('__loginUserEmail__');
                    _this.$cookies.remove('__loginPassword__');
                    if (_this.rememberMe) {
                        _this.$cookies.set('__loginUserEmail__', _this.userNameEmail);
                        _this.$cookies.set('__loginPassword__', _this.password);
                    }
                    if (response.data.type == 'success') {
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
                        //_this.$root.isUserLoggedIn = true;
                        //_this.$root.appUserInfo = response.data.content.user;
                        //Vue.prototype.$isLoggedInUser = true;
                        setTimeout( function () { 
                            //_this.$router.push({name: 'homeRoute'});
                            window.location.href = '/authentication-inprogress';
                        }, 1000);
                    }
                    if (response.data.type == 'error') {
                        _this.$root.isPageLoadingActive = false;
                        _this.$toast.error({
                            title:'Error',
                            message:response.data.msg 
                        });
                    }
                }
            })
            .catch(function (error) {
                if (error.response.status == 422) {
                    _this.$root.isPageLoadingActive = false;
                    _this.validationErrors = error.response.data.content.validationErrors
                    _this.$toast.error({
                        title:'Validation Error',
                        message:'Please process with valid data'
                    });
                }
                if (error.response.status == 500) {
                    _this.$toast.error({
                        title:'System Error',
                        message:'Something went wrong!'
                    });
                }
            });
        }
    },
    mounted() {
        var _this = this;
        if (_this.$cookies.isKey('__loginUserEmail__') && _this.$cookies.isKey('__loginPassword__')) {
            if ((_this.$cookies.get('__loginUserEmail__') != null && _this.$cookies.get('__loginUserEmail__') != undefined) && (_this.$cookies.get('__loginPassword__') != null && _this.$cookies.get('__loginPassword__') != undefined)) {
                _this.rememberMe = true;
                _this.userNameEmail = _this.$cookies.get('__loginUserEmail__');
                _this.password = _this.$cookies.get('__loginPassword__');
            }
        }
    }
}
</script>
