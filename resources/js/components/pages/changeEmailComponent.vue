<template>
    <form @submit.prevent="changeEmailSubmit">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">Change Email</span>
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
                                            <label class="form-lebel">New Email-id: <em>*</em></label>
                                            <input type="email" class="form-control" v-model.trim="email" placeholder="New Email-id">
                                            <div class="text-danger" v-if="!$v.email.required && $v.email.$error">Please enter email</div>
                                            <div class="text-danger" v-if="!$v.email.regxEmailAddress && $v.email.required && $v.email.$error">Please enter an valid email</div>
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
                        <button type="submit" :disabled="isButtonDisabled" class="btn btn-success">Change Email</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import accountOption from './myAccountOptionsComponent'
import validationErrorComponent from '../validationErrorsComponent.vue'
import { required } from 'vuelidate/lib/validators'
const regxEmailAddress = (value, vm) => {
  if (value == '') return true
  return /^([a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)
}
export default {
    components: {
        accountOption,
        validationErrorComponent,
    },
    data() {
        return {
            email: '',
            validationErrors: [],
        }
    },
    watch: {

    },
    computed: {
        isButtonDisabled: function () {
            return this.email == '';
        }
    },
    validations: {
        email: {
            required,
            regxEmailAddress
        }
    },
    methods: {
        defaultInit() {
            var _this = this;
            _this.email = '';
        },
        changeEmailSubmit() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                _this.changeEmailProcess();
            }
        },
        async changeEmailProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/myAccount/changeEmail";
            const apiProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    email: _this.email
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.$root.isPageLoadingActive = false;
                    if (response.data.type == 'success') {
                        _this.validationErrors = [];
                        _this.defaultInit();
                        _this.$v.$reset();
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
