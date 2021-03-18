<template>
    <form @submit.prevent="changePasswordSubmit">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">Change Account Password</span>
                            </div>
                            <div class="col-md-6 text-right">
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <validationErrorComponent :validationErrorListArr="validationErrors" class="mb-1"></validationErrorComponent>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="form-lebel">Current Password: <em>*</em></label>
                                            <input :type="isPasswordOrText" class="form-control" v-model.trim="currentPassword" placeholder="Current Password">
                                            <div class="text-danger" v-if="!$v.currentPassword.required && $v.currentPassword.$error">Please enter current password</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-lebel">New Password: <em>*</em></label>
                                            <input :type="isPasswordOrText" class="form-control" v-model.trim="newPassword" placeholder="New Password">
                                            <div class="text-danger" v-if="!$v.newPassword.required && $v.newPassword.$error">Please enter new password</div>
                                            <div class="text-danger" v-if="!$v.newPassword.minLength && $v.newPassword.required && $v.newPassword.$error">Minimum length required 8</div>
                                            <div class="text-danger" v-if="!$v.newPassword.regxPassword && $v.newPassword.minLength && $v.newPassword.required && $v.newPassword.$error">Please enter a strong password</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-lebel">Confirm Password: <em>*</em></label>
                                            <input :type="isPasswordOrText" class="form-control" v-model.trim="confirmPassword" placeholder="Confirm Password">
                                            <div class="text-danger" v-if="!$v.confirmPassword.required && $v.confirmPassword.$error">Please enter confirm password</div>
                                            <div class="text-danger" v-if="!$v.confirmPassword.sameAs && $v.confirmPassword.required && $v.confirmPassword.$error">Confirm password not match</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <accountOption></accountOption>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" :disabled="isButtonDisabled" class="btn btn-success">Change Password</button>
                        <button type="button" :disabled="isButtonDisabled" class="btn btn-primary navbg" @click="showHidePassword">{{showHideButtonText}}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import accountOption from './myAccountOptionsComponent'
import validationErrorComponent from '../validationErrorsComponent.vue'
import { required, minLength, sameAs } from 'vuelidate/lib/validators'
const regxPassword = (value, vm) => {
  if (value == '') return true
  return /^[a-zA-Z0-9!#\?\^\[\]{}+=._-]{8,32}$/.test(value)
}
export default {
    components: {
        accountOption,
        validationErrorComponent,
    },
    data() {
        return {
            isPasswordOrText: 'password',
            showHideButtonText: 'Show Passwords',
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
            validationErrors: [],
        }
    },
    watch: {

    },
    computed: {
        isButtonDisabled: function () {
            return (this.currentPassword == '' || this.newPassword == '' || this.confirmPassword == '');
        }
    },
    validations: {
        currentPassword: {
            required
        },
        newPassword: {
            required,
            minLength: minLength(8),
            regxPassword
        },
        confirmPassword: {
            required,
            sameAs: sameAs('newPassword')
        }
    },
    methods: {
        defaultInit() {
            var _this = this;
            _this.currentPassword = '';
            _this.newPassword = '';
            _this.confirmPassword = '';
        },
        showHidePassword() {
            var _this = this;
            if (_this.isPasswordOrText == 'password') {
                _this.isPasswordOrText = 'text';
                _this.showHideButtonText = 'Hide Passwords';
            } else {
                _this.isPasswordOrText = 'password';
                _this.showHideButtonText = 'Show Passwords';
            }
        },
        changePasswordSubmit() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                _this.changePasswordProcess();
            }
        },
        async changePasswordProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/myAccount/changePassword";
            const apiProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    currentPassword: _this.currentPassword,
                    newPassword: _this.newPassword,
                    confirmPassword: _this.confirmPassword
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.$root.isPageLoadingActive = false;
                    _this.validationErrors = [];
                    _this.defaultInit();
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
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
                _this.$root.isPageLoadingActive = false;
                if (error.response.status == 422) {
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
