<template>
    <form id="regFrm" @submit.prevent="signupSubmit" autocomplete="off">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="box-title"><i class="fas fa-user-plus"></i> Create Your Account</span>
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
                                            <label class="form-lebel">Name: <em>*</em></label>
                                            <input type="text" v-model.trim="user.name" class="form-control" placeholder="Name" autocomplete="off">
                                            <div class="text-danger" v-if="!$v.user.name.required && $v.user.name.$error">Please enter name</div>
                                            <div class="text-danger" v-if="!$v.user.name.minLength && $v.user.name.required && $v.user.name.$error">Minimum characters 3</div>
                                            <div class="text-danger" v-if="!$v.user.name.regxAlfaWithSpace && $v.user.name.minLength && $v.user.name.required && $v.user.name.$error">Please enter valid name</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-lebel">Email: <em>*</em></label>
                                            <input type="email" v-model.trim="user.email" class="form-control" placeholder="Email" autocomplete="off">
                                            <div class="text-danger" v-if="!$v.user.email.required && $v.user.email.$error">Please enter email</div>
                                            <div class="text-danger" v-if="!$v.user.email.regxEmailAddress && $v.user.email.required && $v.user.email.$error">Please enter an valid email</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-lebel">Username: <em>*</em></label>
                                            <input type="text" v-model.trim="user.username" class="form-control" placeholder="Username" autocomplete="off">
                                            <div class="text-danger" v-if="!$v.user.username.required && $v.user.username.$error">Please enter username</div>
                                            <div class="text-danger" v-if="!$v.user.username.minLength && $v.user.username.required && $v.user.username.$error">Minimum characters 6</div>
                                            <div class="text-danger" v-if="!$v.user.username.regxAlfaNumericWithOutSpace && $v.user.username.minLength && $v.user.username.required && $v.user.username.$error">Please enter valid username</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-lebel">Password: <em>*</em></label>
                                            <input type="password" v-model.trim="user.password" class="form-control" placeholder="Password" autocomplete="off">
                                            <div class="text-danger" v-if="!$v.user.password.required && $v.user.password.$error">Please enter password</div>
                                            <div class="text-danger" v-if="!$v.user.password.minLength && $v.user.password.required && $v.user.password.$error">Minimum length 8</div>
                                            <div class="text-danger" v-if="!$v.user.password.regxPassword && $v.user.password.minLength && $v.user.password.required && $v.user.password.$error">Please enter a strong password</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-lebel">Confirm Password: <em>*</em></label>
                                            <input type="password" v-model.trim="user.confirmPassword" class="form-control" placeholder="Confirm Password" autocomplete="off">
                                            <div class="text-danger" v-if="!$v.user.confirmPassword.required && $v.user.confirmPassword.$error">Please enter confirm password</div>
                                            <div class="text-danger" v-if="!$v.user.confirmPassword.sameAs && $v.user.confirmPassword.required && $v.user.confirmPassword.$error">Confirm password not match</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success">Create Account</button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <router-link :to="{name: 'loginRoute'}" class="btn btn-primary navbg">Login</router-link>
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
import { required, minLength, sameAs } from 'vuelidate/lib/validators'
import validationErrorComponent from '../validationErrorsComponent.vue'
const regxAlfaWithSpace = (value, vm) => {
  if (value == '') return true
  return /^[A-Z a-z]+$/.test(value)
}

const regxAlfaNumericWithOutSpace = (value, vm) => {
  if (value == '') return true
  return /^[A-Za-z0-9_-]+$/.test(value)
}

const regxEmailAddress = (value, vm) => {
  if (value == '') return true
  return /^([a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)
}

const regxNumeric = (value, vm) => {
  if (value == '') return true
  return /^[0-9]+$/.test(value)
}

const regxPassword = (value, vm) => {
  if (value == '') return true
  return /^[a-zA-Z0-9!#\?\^\[\]{}+=._-]{8,32}$/.test(value)
}
export default {
    components: {
        validationErrorComponent
    },
    data() {
        return {
            validationErrors: [],
            user: {
                name: '',
                email: '',
                username: '',
                password: '',
                confirmPassword: ''
            }
        }
    },
    watch: {

    },
    computed: {

    },
    validations: {
        user: {
            name: {
                required,
                minLength: minLength(3),
                regxAlfaWithSpace
            },
            email: {
                required,
                regxEmailAddress
            },
            username: {
                required,
                minLength: minLength(6),
                regxAlfaNumericWithOutSpace
            },
            password: {
                required,
                minLength: minLength(8),
                regxPassword
            },
            confirmPassword: {
                required,
                sameAs: sameAs('password')
            }
        }
    },
    methods: {
        signupSubmit() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                _this.registrationProcess();
            }
        },
        async registrationProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/auth/signup";
            const apiProcess = await axios({
                method: 'post',
                url: url,
                data: _this.user,
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.validationErrors = [];
                    _this.$root.isPageLoadingActive = false;
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
                        setTimeout( function () { 
                            _this.$router.push({name: 'loginRoute'})
                        }, 1000);
                    }
                    if (response.data.type == 'error') {
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
    }
}
</script>
