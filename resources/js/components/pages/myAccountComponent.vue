<template>
    <div>
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
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img :src="profileImage" class="avatar">
                                    </div>
                                    <div class="col-md-8">
                                        <h3><strong>{{name}}</strong></h3>
                                        <hr/>
                                        <p><strong>Email:</strong> {{email}}</p>
                                        <p><strong>Username:</strong> {{username}}</p>
                                        <p><strong>Mobile:</strong> {{mobileNumber}}</p>
                                        <p><strong>Sex:</strong> {{sex}}</p>
                                        <p><strong>address:</strong> {{address}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <accountOption></accountOption>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import accountOption from './myAccountOptionsComponent'
export default {
    components: {
        accountOption
    },
    data() {
        return {
            profileImage: '/images/user.png',
            name: '',
            email: '',
            username: '',
            sex: '',
            address: '',
            mobileNumber: '',
        }
    },
    methods: {
        async getUserInfo() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.post('/auth/checkAuth').then(response => {
                if (response.data.status == 200) {
                    _this.name = response.data.content.user.name;
                    _this.mobileNumber = response.data.content.user.mobile_number;
                    _this.email = response.data.content.user.email;
                    _this.username = response.data.content.user.username;
                    _this.address = response.data.content.user.address;
                    _this.sex = response.data.content.user.sex;
                    if (response.data.content.user.profile_image && response.data.content.user.profile_image != '') {
                        _this.profileImage = '/uploads/thumb/' + response.data.content.user.profile_image;
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
        }
    },
    mounted() {
        this.getUserInfo();
    }
}
</script>

<style scoped>
.avatar {
    width: 100%;
    border-radius: 6px;
}
</style>
