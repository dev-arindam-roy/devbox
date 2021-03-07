<template>
    <form id="addEditPostBoxFrm" @submit.prevent="addEditPostBox">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <span class="box-title">
                            <span v-if="postBox.id==0">Create New Box</span>
                            <span v-else>Edit Box</span>
                        </span>
                    </div>
                    <div class="card-body">
                        <validationErrorComponent :validationErrorListArr="validationErrors" class="mb-1"></validationErrorComponent>
                        <div class="form-group">
                            <label class="form-lebel">Title: <em>*</em></label>
                            <input type="text" v-model.trim="postBox.title" class="form-control" placeholder="Enter title">
                            <div class="text-danger" v-if="!$v.postBox.title.required && $v.postBox.title.$error">Please enter post title</div>
                            <div class="text-danger" v-if="!$v.postBox.title.minLength && $v.postBox.title.required && $v.postBox.title.$error">Minimum 3 chars required</div>
                        </div>
                        <div class="form-group">
                            <label class="form-lebel">Keywords:</label>
                            <textarea name="keywords" v-model.trim="postBox.keywords" class="form-control" placeholder="Enter keywords"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="form-lebel">Category: </label>
                                <select v-model.trim="postBox.category_id" class="form-control">
                                    <option value="0">Default</option>
                                    <option v-for="item in categoryList"  v-bind:key="item.id" v-bind:value="item.id">{{item.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-lebel">Visibility: </label>
                                <select v-model.trim="postBox.visibility" class="form-control">
                                    <option v-for="item in visibilityList"  v-bind:key="item.id" v-bind:value="item.id">{{item.name}}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-lebel">Status: </label>
                                <select v-model.trim="postBox.status" class="form-control">
                                    <option v-for="item in statusList"  v-bind:key="item.id" v-bind:value="item.id">{{item.name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-lebel">Content: <em>*</em></label>
                            <vue-editor v-model="postBox.box_content" :editorToolbar="customToolbar"></vue-editor>
                            <div class="text-danger" v-if="!$v.postBox.box_content.required && $v.postBox.box_content.$error">Please enter post content</div>
                            <div class="text-danger" v-if="!$v.postBox.box_content.minLength && $v.postBox.box_content.required && $v.postBox.box_content.$error">Minimum 3 chars required</div>
                            <!-- <div v-html="postBox.box_content"></div> -->
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" :disabled="isDisabled" class="btn btn-success">{{submitButtonText}}</button>
                        <input type="hidden" v-model.trim="postBox.id">
                        <button type="button" class="btn btn-danger float-right" @click="postBox.id == 0 ? resetAll() : backToPostBoxList()">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
//https://www.vue2editor.com/examples/#basic-setup
//https://www.npmjs.com/package/vue2-editor
import { VueEditor } from 'vue2-editor'
import { required, minLength } from 'vuelidate/lib/validators'
import validationErrorComponent from '../validationErrorsComponent.vue'
export default {
    components: {
        validationErrorComponent,
        VueEditor
    },
    data() {
        return {
            customToolbar: [
                [{'header': [false, 1, 2, 3, 4, 5, 6]}],
                ["bold", "italic", "underline"], 
                [
                    { list: "ordered" }, 
                    { list: "bullet" }
                ],
                [{ 'color': [] }, { 'background': [] }], 
                ['link', 'image', 'video'],
                ["code-block"]
            ],
            submitButtonText: 'Create Box',
            visibilityList: this.$visibilityList,
            statusList: this.$statusList,
            categoryList: [],
            validationErrors: [],
            postBox: {
                id: 0,
                title: '',
                keywords: '',
                category_id: 0,
                visibility: 1,
                status: 1,
                box_content: '',
            }
        }
    },
    computed: {
        isDisabled: function () {
            return this.postBox.title == '' || this.postBox.box_content == ''
        }
    },
    validations: {
        postBox: {
            title: {
                required,
                minLength: minLength(3)
            },
            box_content: {
                required,
                minLength: minLength(3)
            }
        }
    },
    methods: {
        defaultInit() {
            var _this = this;
            _this.postBox.title = '';
            _this.postBox.keywords = '';
            _this.postBox.category_id = 0;
            _this.postBox.visibility = 1;
            _this.postBox.status = 1;
            _this.postBox.box_content = '';
        },
        resetAll() {
            var _this = this;
            _this.defaultInit();
        },
        async getActiveCategories() {
            var _this = this;
            axios.get('/categories', {
                params: {
                    categoryStatus: 1
                }
            }).then(response => {
                _this.categoryList = response.data.content.myCategories;
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        addEditPostBox() {
            var _this = this;
             _this.$v.$touch();
            if (!this.$v.$error) {
                _this.$root.isPageLoadingActive = true;
                if (_this.postBox.id == 0) {
                    _this.createPostBoxSubmit();
                } else {
                    _this.editPostBoxSubmit();
                }
            }
        },
        async createPostBoxSubmit() {
            var _this = this;
            var url = "/postboxes/add";
            const addPostBoxProcess = await axios({
                method: 'post',
                url: url,
                data: _this.postBox,
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.$root.isPageLoadingActive = false;
                    _this.validationErrors = [];
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.defaultInit();
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
                        // Access root method
                        _this.$root.getCounts();
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
        },
        async getPostBoxEditInfo(postBoxID) {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get(`/postboxes/edit/${postBoxID}`).then(response => {
                if (response.data.status == 200) {
                    _this.submitButtonText = 'Save Changes';
                    _this.postBox.title = response.data.content.postBoxInfo.title;
                    _this.postBox.keywords = response.data.content.postBoxInfo.keywords;
                    _this.postBox.category_id = response.data.content.postBoxInfo.category_id;
                    _this.postBox.status = response.data.content.postBoxInfo.status;
                    _this.postBox.visibility = response.data.content.postBoxInfo.visibility;
                    _this.postBox.box_content = response.data.content.postBoxInfo.box_content;
                    _this.$root.isPageLoadingActive = false;
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
        backToPostBoxList() {
            window.location.href = window.location.origin + "/my-post-boxes";
        },
        async editPostBoxSubmit() {
            var _this = this;
            var url = `/postboxes/update/${_this.postBox.id}`;
            const editPostBoxProcess = await axios({
                method: 'post',
                url: url,
                data: _this.postBox,
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.$root.isPageLoadingActive = false;
                    _this.validationErrors = [];
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
                        // Access root method
                        _this.$root.getCounts();
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
        this.getActiveCategories();
        if (this.$route.params.id != undefined) {
            this.postBox.id = this.$route.params.id;
            this.getPostBoxEditInfo(this.postBox.id);
        }
    }
}
</script>
