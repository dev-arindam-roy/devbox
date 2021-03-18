<template>
    <form @submit.prevent="changeUsernameSubmit">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">Change Username</span>
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
                                            <label class="form-lebel">New Username: <em>*</em></label>
                                            <input type="text" class="form-control" v-model.trim="username" placeholder="New username">
                                            <div class="text-danger" v-if="!$v.username.required && $v.username.$error">Please enter username</div>
                                            <div class="text-danger" v-if="!$v.username.minLength && $v.username.required && $v.username.$error">Minimum characters required 6</div>
                                            <div class="text-danger" v-if="!$v.username.regxAlfaNumericWithOutSpace && $v.username.minLength && $v.username.required && $v.username.$error">Please enter a valid username</div>
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
                        <button type="submit" :disabled="isButtonDisabled" class="btn btn-success">Change Username</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import accountOption from './myAccountOptionsComponent'
import validationErrorComponent from '../validationErrorsComponent.vue'
import { required, minLength } from 'vuelidate/lib/validators'
const regxAlfaNumericWithOutSpace = (value, vm) => {
  if (value == '') return true
  return /^[A-Za-z0-9_-]+$/.test(value)
}
export default {
    components: {
        accountOption,
        validationErrorComponent,
    },
    data() {
        return {
            username: '',
            validationErrors: [],
        }
    },
    watch: {

    },
    computed: {
        isButtonDisabled: function () {
            return this.username == '';
        }
    },
    validations: {
        username: {
            required,
            minLength: minLength(6),
            regxAlfaNumericWithOutSpace
        }
    },
    methods: {
        defaultInit() {
            var _this = this;
            _this.username = '';
        },
        changeUsernameSubmit() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                _this.changeUsernameProcess();
            }
        },
        async changeUsernameProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/myAccount/changeUsername";
            const apiProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    username: _this.username
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
