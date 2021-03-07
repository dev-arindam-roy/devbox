<template>
    <div class="modal fade" id="addCategoryModal">
        <form id="addCategoryFrm" @submit.prevent="createCategory">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title box-title" id="exampleModalLabel">Add Category</h5>
                        <button type="button" class="close" @click="closeAddCategoryModal">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <validationErrorComponent :validationErrorListArr="validationErrors"></validationErrorComponent>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-lebel">Visibility: </label>
                                    <select v-model.trim="categoryVisibility" class="form-control">
                                        <option v-for="item in visibilityList"  v-bind:key="item.id" v-bind:value="item.id">{{item.name}}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-lebel">Category Name: <em>*</em></label>
                                    <input type="text" v-model.trim="categoryName" name="name" maxlength="60" class="form-control" placeholder="Category name">
                                    <div class="text-danger" v-if="!$v.categoryName.required && $v.categoryName.$error">Please enter category name</div>
                                    <div class="text-danger" v-if="!$v.categoryName.minLength && $v.categoryName.required && $v.categoryName.$error">Minimum 3 chars required</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-lebel">Category Description: </label>
                                    <textarea v-model.trim="categoryDescription" name="description" class="form-control" placeholder="Category description"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" :disabled="isDisabled" class="btn btn-success">Create</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { required, minLength } from 'vuelidate/lib/validators'
import validationErrorComponent from './validationErrorsComponent.vue'
export default {
    components: {
        validationErrorComponent
    },
    data() {
        return {
            categoryName: '',
            categoryDescription: '',
            categoryVisibility: '1',
            visibilityList: this.$visibilityList,
            validationErrors: []
        }
    },
    computed: {
        isDisabled: function () {
            return this.categoryName == ''
        }
    },
    validations: {
        categoryName: {
            required,
            minLength: minLength(3)
        }
    },
    mounted() {
        
    },
    methods: {
        defaultInit() {
            this.categoryName = '';
            this.categoryDescription = '';
            this.categoryVisibility = '1';
        },
        createCategory() {
            var _this = this;
            _this.$v.$touch();
            if (!this.$v.$error) {
                _this.createCategorySubmit();
            }
        },
        async createCategorySubmit() {
            var _this = this;
            var url = "/categories/add";
            const addCategoryProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    name: _this.categoryName,
                    description: _this.categoryDescription,
                    visibility: _this.categoryVisibility
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.validationErrors = [];
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.defaultInit();
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
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
        closeAddCategoryModal() {
            this.defaultInit();
            this.validationErrors = [];
            $('#addCategoryModal').modal('hide');
        }
    }
}
</script>
