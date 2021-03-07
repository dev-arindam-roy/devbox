<template>
    <div id="myPostBoxList">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">My PostBoxes ({{postBoxList.total}})</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'addBoxRoute'}">Add PostBox</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <button type="button" :disabled="ckbIds.length == 0" class="btn btn-sm btn-danger" @click="deleteAllSelected()">Delete Setected</button>
                            </div>
                            <div class="col-md-3">
                                <select v-model.trim="postCategory" class="form-control" @change="searchPostBoxList">
                                    <option value="0">Categories</option>
                                    <option v-for="item in categoryList"  v-bind:key="item.id" v-bind:value="item.id">{{item.name}}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" v-model.trim="postSearch" class="form-control" placeholder="Search.." v-on:keyup="searchByPostTitle($event)">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">
                                                <input type="checkbox" v-model="isCheckAll" v-on:click="selectAll($event)">
                                            </th>
                                            <th style="width:130px;">Category</th>
                                            <th>Title</th>
                                            <th style="width:75px; text-align:center;">Status</th>
                                            <th style="width:100px;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="postBoxList.total > 0">
                                        <tr v-for="(postbox, index) in postBoxList.data" :key="postbox.id">
                                            <td style="width:60px;">
                                                <input type="checkbox" v-model="ckbIds" :value="postbox.id">
                                                {{index + 1}}
                                            </td>
                                            <td style="width:130px;">
                                                <span v-if="postbox.category_name">{{postbox.category_name}}</span>
                                                <span v-else>Default</span>
                                            </td>
                                            <td>
                                                {{postbox.title}}
                                            </td>
                                            <td style="width:75px; text-align:center;">
                                                <span v-if="postbox.status == 1">
                                                    <a href="javascript:void(0);" title="Active" @click="statusChange(postbox, 0)"><i class="far fa-2x fa-check-circle base-green"></i></a>
                                                </span>
                                                <span v-else>
                                                    <a href="javascript:void(0);" title="Inactive" @click="statusChange(postbox, 1)"><i class="fas fa-2x fa-ban base-red"></i></a>
                                                </span>
                                            </td>
                                            <td style="width:100px;">
                                                <router-link :to="{name: 'editPostBoxRoute', params: {id: postbox.id}}" class="btn btn-success btn-sm"><i class="far fa-edit"></i></router-link>
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="deletePostBox(postbox)"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="5">No PostBox Found!</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <pagination :data="postBoxList" @pagination-change-page="getMyBoxPosts"></pagination>
                    </div>
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

            isCheckAll: false,
            ckbIds: [],

            pagination: this.$defaultPagination,
            postBoxList: {},
        }
    },
    watch: {
        postSearch() {
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
        
    },
    methods: {
        async getAllCategories() {
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
                    search: searchTxt,
                    category: categoryId,
                    pagination: _this.pagination
                }
            }).then(response => {
                _this.postBoxList = response.data.content.myPostBoxes;
                _this.isCheckAll = false;
                _this.ckbIds = [];
                _this.$root.isPageLoadingActive = false;
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        statusChange(postbox, status) {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to change the status",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes'
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
                    this.changeStatusProcess(postbox, status); 
                }
            })
        },
        async changeStatusProcess(postbox, status) {
            var _this = this;
            var url = "/postboxes/changeStatus";
            const statusChangeProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    id: postbox.id,
                    status: status
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

                    let index = _this.postBoxList.data.indexOf(postbox);
                    _this.postBoxList.data[index].status = status;
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        deletePostBox(postbox) {
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
                    this.deletePostBoxProcess(postbox); 
                }
            })
        },
        async deletePostBoxProcess(postbox) {
            var _this = this;
            var url = "/postboxes/delete";
            const deleteProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    postBoxId: postbox.id
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
                    let index = _this.postBoxList.data.indexOf(postbox);
                    _this.postBoxList.data.splice(index, 1);
                    _this.postBoxList.total--;
                    _this.$root.sidebarCount.postBoxCount--;
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        selectAll(event) {
            if (event.target.checked) {
                this.ckbIds = this.postBoxList.data.map((postBoxList) => postBoxList.id); 
            } else {
               this.ckbIds = []; 
            }
        },
        deleteAllSelected() {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to delete selected records",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete Selected!'
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
                    this.deleteSelectedProcess(); 
                }
            })
        },
        async deleteSelectedProcess() {
            var _this = this;
            var url = "/postboxes/bulkDelete";
            const bulkDeleteProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    ids: _this.ckbIds
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.$toast.success({
                        title:'Success',
                        message:response.data.msg
                    });

                    let jsonObj = _this.postBoxList;
                    _this.ckbIds.forEach(element => {
                        var indexOf = jsonObj.data.findIndex(data=> data.id === element);
                        jsonObj.data.splice(indexOf, 1);
                        jsonObj.total--;
                        _this.$root.sidebarCount.postBoxCount--;
                    }); 
                    _this.postBoxList = jsonObj;
                    _this.ckbIds = [];
                    _this.isCheckAll = false;
                    _this.$swal.close();
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        searchByPostTitle(event) {
            var _this = this;
            if (event.keyCode === 13 || event.which === 13) {
                _this.postSearch = event.target.value;
                _this.searchPostBoxList();
            } 
            if (event.target.value.length == 0) {
                _this.postSearch = '';
                _this.searchPostBoxList();     
            }
        },
        searchPostBoxList() {
            var _this = this;
            _this.getMyBoxPosts(1, _this.postCategory, _this.postSearch);
        }
    },
    mounted() {
        var _this = this;
        _this.getAllCategories();
        _this.getMyBoxPosts();
    }
}
</script>
