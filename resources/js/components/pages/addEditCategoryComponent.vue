<template>
    <form id="editCategoryFrm" @submit.prevent="submitCategory">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">
                                    <span v-if="categoryId==0">Create Category</span>
                                    <span v-else>Edit Category</span>
                                </span>
                            </div>
                            <div class="col-md-6">
                                <router-link :to="{name : 'myCategoryListRoute'}" class="btn btn-sm btn-primary navbg float-right">All Categories</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <validationErrorComponent :validationErrorListArr="validationErrors"></validationErrorComponent>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-lebel">Visibility:</label>
                                    <select v-model.trim="categoryVisibility" class="form-control">
                                        <option v-for="item in visibilityList"  v-bind:key="item.id" v-bind:value="item.id">{{item.name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-lebel">Status:</label>
                                    <select v-model.trim="categoryStatus" class="form-control">
                                        <option v-for="item in statusList" v-bind:key="item.id" v-bind:value="item.id">{{item.name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-lebel">Category Name: <em>*</em></label>
                                    <input type="text" v-model.trim="categoryName" class="form-control" placeholder="Category name">
                                    <div class="text-danger" v-if="!$v.categoryName.required && $v.categoryName.$error">Please enter category name</div>
                                    <div class="text-danger" v-if="!$v.categoryName.minLength && $v.categoryName.required && $v.categoryName.$error">Minimum 3 chars required</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-lebel">Description:</label>
                                    <textarea v-model.trim="categoryDescription" class="form-control" placeholder="Category description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" :disabled="!isSubmitDisabled" class="btn btn-success">{{submitButtonText}}</button>
                                <input type="hidden" v-model.trim="categoryId">
                            </div>
                            <div class="col-md-6">
                                <div v-if="categoryId == 0">
                                    <a href="javascript:void(0);" class="btn btn-danger float-right" @click="resetAll">Cancel</a>
                                </div>
                                <div v-else>
                                    <router-link :to="{name : 'myCategoryListRoute'}" class="btn btn-danger float-right">Cancel</router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import { required, minLength } from 'vuelidate/lib/validators'
import validationErrorComponent from '../validationErrorsComponent.vue'
export default {
    components: {
        validationErrorComponent
    },
    data() {
        return {
            submitButtonText: 'Add Category',
            visibilityList: this.$visibilityList,
            statusList: this.$statusList,
            categoryId: 0, 
            categoryVisibility: 1,
            categoryStatus: 1,
            categoryName: '',
            categoryDescription: '',
            validationErrors: [],
            loadData: {
                categoryVisibility: '',
                categoryStatus: '',
                categoryName: '',
                categoryDescription: ''
            }
        }
    },
    computed: {
        isSubmitDisabled: function () {
            if (this.categoryId == 0) {
                return this.categoryName != '';
            } else {
                return this.categoryName != this.loadData.categoryName ||
                    this.categoryDescription != this.loadData.categoryDescription ||
                    this.categoryStatus != this.loadData.categoryStatus ||
                    this.categoryVisibility != this.loadData.categoryVisibility
            }
        }
    },
    validations: {
        categoryName: {
            required,
            minLength: minLength(3)
        }
    },
    methods: {
        async getCategory() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get(`/categories/edit/${_this.categoryId}`).then(response => {
                if (response.data.status == 200) {
                    _this.categoryName = response.data.content.categoryInfo.name;
                    _this.categoryDescription = response.data.content.categoryInfo.description;
                    _this.categoryStatus = response.data.content.categoryInfo.status;
                    _this.categoryVisibility = response.data.content.categoryInfo.visibility;
                    _this.$root.isPageLoadingActive = false;

                    _this.loadData.categoryName = response.data.content.categoryInfo.name;
                    _this.loadData.categoryDescription = response.data.content.categoryInfo.description;
                    _this.loadData.categoryStatus = response.data.content.categoryInfo.status;
                    _this.loadData.categoryVisibility = response.data.content.categoryInfo.visibility;
                }
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
                if (error.response.status === 404) {
                    setTimeout(function () { 
                         window.location.href = '/';
                    }, 2000);
                }
            });
        },
        submitCategory() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                if (_this.categoryId == 0) {
                    _this.createCategoryProcess()
                } else {
                    _this.updateCategoryProcess()
                }
            }
        },
        async updateCategoryProcess() {
            var _this = this;
            var url = `/categories/update/${_this.categoryId}`;
            const updateCategoryProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    name: _this.categoryName,
                    description: _this.categoryDescription,
                    visibility: _this.categoryVisibility,
                    status: _this.categoryStatus
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.validationErrors = [];
                    if (response.data.type == 'success') {
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });

                        _this.loadData.categoryName = _this.categoryName;
                        _this.loadData.categoryDescription = _this.categoryDescription;
                        _this.loadData.categoryStatus = _this.categoryStatus;
                        _this.loadData.categoryVisibility = _this.categoryVisibility;
                    }
                    if (response.data.type == 'error') {
                        _this.$toast.error({
                            title:'Error',
                            message:response.data.content.categoryName + ' - ' + response.data.msg 
                        });
                    }
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
            });
        },
        async createCategoryProcess() {
            var _this = this;
            var url = "/categories/add";
            const addCategoryProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    name: _this.categoryName,
                    description: _this.categoryDescription,
                    visibility: _this.categoryVisibility,
                    status: _this.categoryStatus
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.validationErrors = [];
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.resetAll();
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
                        _this.$root.sidebarCount.categoryCount++;
                    }
                    if (response.data.type == 'error') {
                        _this.$toast.error({
                            title:'Error',
                            message:response.data.content.categoryName + ' - ' + response.data.msg 
                        });
                    }
                }
            })
            .catch(function (error) {
                // console.log(error.response.status);
                // console.log(error.response.data);
                // console.log(error.response.headers);
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
        },
        resetAll() {
            var _this = this;
            _this.categoryId = 0;
            _this.categoryVisibility = 1;
            _this.categoryStatus = 1;
            _this.categoryName = '';
            _this.categoryDescription = '';
            _this.validationErrors = [];
            _this.$v.$reset();
        }
    },
    mounted() {
        if (this.$route.params.id != undefined) {
            this.categoryId = this.$route.params.id;
            this.getCategory(this.categoryId);
            this.submitButtonText = 'Save Changes';
        }
    }
}
</script>
