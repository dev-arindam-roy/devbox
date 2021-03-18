<template>
    <form @submit.prevent="editProfileSubmit">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">Edit Profile</span>
                            </div>
                            <div class="col-md-6 text-right">
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <validationErrorComponent :validationErrorListArr="validationErrors"></validationErrorComponent>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-lebel">Name: <em>*</em></label>
                                    <input type="text" v-model.trim="name" class="form-control" placeholder="Name">
                                    <div class="text-danger" v-if="!$v.name.required && $v.name.$error">Please enter name</div>
                                    <div class="text-danger" v-if="!$v.name.minLength && $v.name.required && $v.name.$error">Minimum characters required 3</div>
                                    <div class="text-danger" v-if="!$v.name.regxAlfaWithSpace && $v.name.minLength && $v.name.required && $v.name.$error">Please enter valid name</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-lebel">Mobile Number: </label>
                                    <input type="text" v-model.trim="mobileNumber" class="form-control" placeholder="Mobile Number">
                                    <div class="text-danger" v-if="!$v.mobileNumber.regxNumeric && $v.mobileNumber.$error">Please enter valid mobile number</div>
                                    <div class="text-danger" v-if="!$v.mobileNumber.minLength && $v.mobileNumber.regxNumeric && $v.mobileNumber.$error">Please enter 10 digits valid number</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-lebel">Profile Image: </label> <br/>
                                    <input type="file" ref="profileImage" v-on:change="handleImageUploads($event)" accept="image/*">
                                    <div class="text-danger" v-if="!isImgExtValid">Image extension is wrong.</div>
                                    <div class="text-danger" v-if="!isImgSizeValid && isImgExtValid">Image size is should less than 1mb.</div>
                                </div>
                                <div class="form-group">
                                    <img :src="profileImagePath" class="avatar">
                                    <button type="button" v-show="currentProfileImage != ''" class="btn btn-sm btn-danger float-right" v-on:click="removeProfileImage">Remove Profile Image</button>
                                </div>
                                <div class="form-group">
                                    <label class="form-lebel">Address: </label>
                                    <textarea v-model.trim="address" class="form-control" placeholder="Address"></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-lebel">Sex: </label> <br/>
                                    <input type="radio" name="sex" v-model.trim="sex" value="Male"> Male
                                    <input type="radio" name="sex" v-model.trim="sex" value="Female"> Female
                                </div>
                            </div>
                            <div class="col-md-4">
                                <accountOption></accountOption>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" :disabled="isSubmitDisabled" class="btn btn-success">Save Changes</button>
                        <button type="button" v-show="isImageHasError" class="btn btn-danger" v-on:click="resetUpload">Reset Upload</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import { required, minLength } from 'vuelidate/lib/validators'
import validationErrorComponent from '../validationErrorsComponent.vue'
import accountOption from './myAccountOptionsComponent'
const regxAlfaWithSpace = (value, vm) => {
  if (value == '') return true
  return /^[A-Z a-z]+$/.test(value)
}
const regxNumeric = (value, vm) => {
  if (value == '') return true
  if (value != '') {
    return /^[0-9]+$/.test(value)
  }
}
export default {
    components: {
        accountOption,
        validationErrorComponent
    },
    data() {
        return {
            name: '',
            mobileNumber: '',
            address: '',
            sex: '',

            currentProfileImage: '',
            profileImage: '',
            defaultProfileImagePath: '/images/user.png',
            profileImagePath: '',
            
            validationErrors: [],

            isImgExtValid: true,
            isImgSizeValid: true,
            isImageHasError: false,
        }
    },
    watch: {

    },
    computed: {
        isSubmitDisabled: function () {
            return this.name == '' || this.isImageHasError;
        }
    },
    validations: {
        name: {
            required,
            minLength: minLength(3),
            regxAlfaWithSpace
        },
        mobileNumber: {
            minLength: minLength(10),
            regxNumeric
        }
    },
    methods: {
        async getUserInfo() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.post('/auth/checkAuth').then(response => {
                if (response.data.status == 200) {
                    _this.name = response.data.content.user.name;
                    if (response.data.content.user.mobile_number) {
                        _this.mobileNumber = response.data.content.user.mobile_number;
                    }
                    if (response.data.content.user.address) {
                        _this.address = response.data.content.user.address;
                    }
                    _this.sex = response.data.content.user.sex;
                    if (response.data.content.user.profile_image) {
                        _this.profileImagePath = '/uploads/thumb/' + response.data.content.user.profile_image;
                        _this.currentProfileImage = '/uploads/thumb/' + response.data.content.user.profile_image;
                        _this.defaultProfileImagePath = '/uploads/thumb/' + response.data.content.user.profile_image;
                    }
                    _this.$root.isPageLoadingActive = false;
                }
            }).catch(function (error) {
                _this.$root.isPageLoadingActive = false;
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        uploadInit() {
            var _this = this;
            _this.profileImage = '';
            _this.$refs.profileImage.value = null;
        },
        resetUpload() {
            this.isImgExtValid = true;
            this.isImgSizeValid = true;
            this.isImageHasError = false;
        },
        handleImageUploads(event) {
            var _this = this;
            _this.isImgExtValid = true;
            _this.isImgSizeValid = true;
            _this.isImageHasError = false;
            let imgExtArr = ['jpg', 'png', 'jpeg', 'gif'];
            let files = event.target.files;
            let fileLength = files.length;
            let fileName = files[0].name;
            let fileSize = files[0].size;
            let fileNameArr = fileName.split('.');
            let fileExt = fileNameArr[fileNameArr.length - 1].toLowerCase();

            if(!imgExtArr.includes(fileExt)) {
                _this.isImgExtValid = false;
                _this.profileImagePath =_this.defaultProfileImagePath;
                _this.isImageHasError = true;
                _this.profileImage = '';
                _this.$refs.profileImage.value = null;
            }
            
            if(fileSize >= 1000000) {
                _this.isImgSizeValid = false;
                _this.profileImagePath =_this.defaultProfileImagePath;
                _this.isImageHasError = true;
                _this.profileImage = '';
                _this.$refs.profileImage.value = null;
            }

            if(_this.isImgExtValid && _this.isImgSizeValid) {
                _this.isImageHasError = false;
                var reader = new FileReader();
                reader.onload = function(e) {
                    _this.profileImagePath = e.target.result;
                }
                reader.readAsDataURL(files[0]);
                _this.profileImage = _this.$refs.profileImage.files;
            }
        },
        editProfileSubmit() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                _this.editProfileProcess();
            }
        },
        async editProfileProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;

            var url = "/myAccount/updateProfile";
            
            let formData = new FormData();
            if (_this.profileImage && _this.profileImage.length) {
                formData.append('avatar', _this.profileImage[0]);
            }
            formData.append('name', _this.name);
            if (_this.mobileNumber != '') {
                formData.append('mobile_number', _this.mobileNumber);
            }
            if (_this.address != '') {
                formData.append('address', _this.address);
            }
            if (_this.sex != '') {
                formData.append('sex', _this.sex);
            }
            
            const apiResponse = await axios({
                method: 'post',
                url: url,
                data: formData,
                headers: {'Content-Type': 'multipart/form-data'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.validationErrors = [];
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.uploadInit();
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
                    _this.$root.isPageLoadingActive = false;
                    _this.getUserInfo();
                }
            })
            .catch(function (error) {
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
                 _this.$root.isPageLoadingActive = false;
            });
        },
        removeProfileImage() {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to delete",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!'
            }).then((actionResult) => {
                if (actionResult.value) {
                    _this.$swal({
                        title: 'Please Wait ...',
                        willOpen () {
                            _this.$swal.showLoading()
                        },
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        showCancelButton: false,
                        showConfirmButton: false
                    })
                    this.deleteProfileImageProcess(); 
                }
            })
        },
        async deleteProfileImageProcess() {
            var _this = this;
            axios.post('/myAccount/deleteProfileImage').then(response => {
                _this.$root.isPageLoadingActive = true;
                if (response.data.status == 200) {
                    _this.$swal.close();
                    _this.$toast.success({
                        title:'Success',
                        message:response.data.msg
                    });
                    _this.currentProfileImage = '';
                    _this.profileImagePath = '/images/user.png';
                    _this.uploadInit();
                    _this.resetUpload();
                    _this.$root.isPageLoadingActive = false;
                }
            }).catch(function (error) {
                _this.$root.isPageLoadingActive = false;
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        }
    },
    mounted() {
        var _this = this;
        _this.profileImagePath = _this.defaultProfileImagePath;
        _this.getUserInfo();
    }
}
</script>

<style scoped>
.avatar {
    width: 100px;
    max-height: 90px;
    border-radius: 5px;
}
</style>
