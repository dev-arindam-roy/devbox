<template>
    <div id="homePage">
        <div class="row mb-3">
            <div class="col-md-3">
                <select v-model.trim="postCategory" class="form-control">
                    <option value="0">Categories</option>
                    <option v-for="item in categoryList"  v-bind:key="item.id" v-bind:value="item.id">{{item.name}}</option>
                </select>
            </div>
            <div class="col-md-8">
                <input type="text" v-model.trim="postSearch" class="form-control" placeholder="Search..">
            </div>
            <div class="col-md-1">
                <button :disabled="isDisabled" class="btn btn-success" @click="searchDevPost"><i class="fas fa-search"></i></button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label><strong>Total DevBox: {{postBoxList.total}}</strong></label>
            </div>
            <div class="col-md-6 text-right" v-if="isLoadAllShow">
                <a href="/" class="btn btn-sm btn-primary navbg">All PostBox</a>
            </div>
        </div>
        <div class="row" v-if="postBoxList.total > 0">
            <div class="col-md-12 mb-3" v-for="(post, index) in postBoxList.data" :key="post.id">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12"><span class="box-post-title">{{index + 1}}. {{post.title}}</span></div>
                        </div>
                        <div class="row" v-if="post.keywords.length > 0 || post.category_name">
                            <div class="col-md-6">
                                <span class="keywords badge badge-secondary mr-1" v-for="keyword in post.keywords" v-bind:key="keyword" @click="searchByKeyword(keyword)">{{keyword}}</span>
                            </div>
                            <div class="col-md-6 text-right" v-if="post.category_name"><span class="box-post-catname" @click="searchByCategory(post.category_id)">{{post.category_name}} <i class="fas fa-angle-double-left"></i></span></div>
                        </div>
                    </div>
                    <div class="card-body" style="background-color: #fdfde6;">
                        <div class="post-box-content" v-html="post.box_content"></div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6"><span class="post-footer-dt"><i class="far fa-clock"></i> {{post.post_at}}</span></div>
                            <div class="col-md-6 text-right">
                                <router-link :to="{name: 'editPostBoxRoute', params: {id: post.id}}" class="btn btn-sm btn-outline-success">Edit</router-link>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" @click="deletePostBox(post)">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <pagination :data="postBoxList" @pagination-change-page="getMyBoxPosts"></pagination>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-md-12">
                <div class="alert alert-info">
                    <strong>No DevBox Found!</strong> 
                    <p><router-link :to="{name: 'addBoxRoute'}">Click here to add your <strong>DevBox</strong></router-link></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            categoryList: [],
            postCategory: 0,
            postSearch: '',

            pagination: this.$defaultPagination,
            postBoxList: {},
        }
    },
    watch: {
        postSearch () {
            if (this.postSearch == '' && this.postCategory == 0) {
                this.getMyBoxPosts();
            }
        },
        postCategory() {
            if (this.postSearch == '' && this.postCategory == 0) {
                this.getMyBoxPosts();
            }
        }
    },
    computed: {
        isDisabled: function () {
            return (this.postCategory == '' || this.postCategory == 0) && this.postSearch == '';
        },
        isLoadAllShow: function () {
            return this.$route.query.keyword != undefined || this.$route.query.category != undefined;
        }
    },
    mounted() {
        var _this = this;
        _this.getActiveCategories();
        _this.getMyBoxPosts();
        if (_this.$route.query.keyword != undefined) {
            _this.searchByKeyword(_this.$route.query.keyword);
        }
        if (_this.$route.query.category != undefined) {
            _this.searchByCategory(_this.$route.query.category);
        }
    },
    methods: {
        async getActiveCategories() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get('/categories', {
                params: {
                    status: [1]
                }
            }).then(response => {
                _this.categoryList = response.data.content.myCategories;
                _this.$root.isPageLoadingActive = false;
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        async getMyBoxPosts(page = 1, categoryId = this.postCategory, searchTxt = this.postSearch) {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get('/postboxes', {
                params: {
                    page: page,
                    status: [1],
                    search: searchTxt,
                    category: categoryId,
                    pagination: _this.pagination
                }
            }).then(response => {
                _this.postBoxList = response.data.content.myPostBoxes;
                _this.$root.isPageLoadingActive = false;
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        searchDevPost() {
            var _this = this;
            _this.getMyBoxPosts(1, _this.postCategory, _this.postSearch);
        },
        deletePostBox(postBoxObj) {
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
                    this.deletePostBoxProcess(postBoxObj); 
                }
            })
        },
        async deletePostBoxProcess(postBoxObj) {
            var _this = this;
            var url = "/postboxes/delete";
            const deleteProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    postBoxId: postBoxObj.id
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.$swal.close();
                    _this.$toast.success({
                        title:'Success',
                        message:response.data.msg
                    });
                    let index = _this.postBoxList.data.indexOf(postBoxObj);
                    _this.postBoxList.data.splice(index, 1);
                    _this.postBoxList.total--;
                    _this.$root.getCounts();
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        searchByKeyword(keyword) {
            var _this = this;
            _this.postSearch = keyword;
            _this.searchDevPost();
        },
        searchByCategory(catId) {
            var _this = this;
            _this.postCategory = catId;
             _this.searchDevPost();
        }
    }
}
</script>
