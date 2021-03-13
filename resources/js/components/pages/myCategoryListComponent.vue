<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <span class="box-title">My Categories ({{boxCategories.total}})</span>
                        </div>
                        <div class="col-md-4 text-right">
                            <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'addCategoryRoute'}">Add Category</router-link>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-1">
                        <div class="col-md-3">
                            <button type="button" :disabled="ckbIds.length == 0" class="btn btn-sm btn-danger" @click="deleteAllSelected()">Delete Setected</button>
                        </div>
                        <div class="col-md-5"></div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Search.." v-on:keyup="searchCategories($event)">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">
                                            <input type="checkbox" v-model="isCheckAll" v-on:click="selectAll($event)">
                                        </th>
                                        <th>Name</th>
                                        <th style="width:60px; text-align:center;">PostBox</th>
                                        <th style="width:120px; text-align:center;">Visibility</th>
                                        <th style="width:100px; text-align:center;">Status</th>
                                        <th style="width:140px;">#</th>
                                    </tr>
                                </thead>
                                <!-- for pagination need to add an extra data -->
                                <tbody v-if="boxCategories.total > 0">
                                    <tr v-for="(category, index) in boxCategories.data" :key="category.id">
                                        <td>
                                            <input type="checkbox" v-model="ckbIds" :value="category.id">
                                            {{index + 1}}
                                        </td>
                                        <td><a href="javascript:void(0);" @click="searchByCategory(category.id)"><span>{{category.name}}</span></a></td>
                                        <td style="width:60px; text-align:center;">{{postBoxCount[category.id]}}</td>
                                        <td style="text-align:center;">
                                            <span v-if="category.visibility == 1">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-success" @click="visibilityChange(category, 0)">Private</a>
                                            </span>
                                            <span v-else>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" @click="visibilityChange(category, 1)">Public</a>
                                            </span>
                                        </td>
                                        <td style="text-align:center;">
                                            <span v-if="category.status == 1">
                                                <a href="javascript:void(0);" title="Active" @click="statusChange(category, 0)"><i class="far fa-2x fa-check-circle base-green"></i></a>
                                            </span>
                                            <span v-else>
                                                <a href="javascript:void(0);" title="Inactive" @click="statusChange(category, 1)"><i class="fas fa-2x fa-ban base-red"></i></a>
                                            </span>
                                        </td>
                                        <td>
                                            <router-link :to="{name: 'editCategoryRoute', params: {id: category.id}}" class="btn btn-success btn-sm"><i class="far fa-edit"></i></router-link>
                                            <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="deleteCategory(category)"><i class="far fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="6">No categories found! Please create category.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <pagination :data="boxCategories" @pagination-change-page="getAllCategories"></pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            pagination: this.$defaultPagination,
            boxCategories: {},
            ckbIds: [],
            isCheckAll: false,
            searchText: '',
            postBoxCount: [],
        }
    },
    watch: {
        ckbIds() {
            if (this.ckbIds.length == 0) {
                this.isCheckAll = false;
            }
        }
    },
    methods: {
        async getAllCategories(page = 1, searchText = this.searchText) {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get('/categories', {
                params: {
                    page: page,
                    categoryName: searchText,
                    pagination: _this.pagination
                }
            }).then(response => {
                _this.boxCategories = response.data.content.myCategories;
                _this.postBoxCount = response.data.content.postBoxCount;
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
        deleteCategory(category) {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to delete",
                icon: 'warning',
                showCancelButton: true,
                //confirmButtonColor: '#0a71b9',
                //cancelButtonColor: '#cc1f00',
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
                    this.deleteCategoryProcess(category); 
                }
            })
        },
        async deleteCategoryProcess(category) {
            var _this = this;
            var url = "/categories/delete";
            const deleteCategoryProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    categoryId: category.id
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
                    let index = _this.boxCategories.data.indexOf(category);
                    _this.boxCategories.data.splice(index, 1);
                    _this.boxCategories.total--;
                    _this.ckbIds = [];
                    _this.isCheckAll = false;
                    _this.$root.getCounts();
                    //console.log(_this.boxCategories.data.length);
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        statusChange(category, status) {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to change the status",
                icon: 'warning',
                showCancelButton: true,
                //confirmButtonColor: '#0a71b9',
                //cancelButtonColor: '#cc1f00',
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
                    this.changeStatusProcess(category, status); 
                }
            })
        },
        async changeStatusProcess(category, status) {
            var _this = this;
            var url = "/categories/changeStatus";
            const statusChangeProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    id: category.id,
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

                    let index = _this.boxCategories.data.indexOf(category);
                    _this.boxCategories.data[index].status = status;
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        visibilityChange(category, visibilityStatus) {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to change the visibility",
                icon: 'warning',
                showCancelButton: true,
                //confirmButtonColor: '#0a71b9',
                //cancelButtonColor: '#cc1f00',
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
                    this.changeVisibilityProcess(category, visibilityStatus); 
                }
            })
        },
        async changeVisibilityProcess(category, visibilityStatus) {
            var _this = this;
            var url = "/categories/changeVisibility";
            const visibilityChangeProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    id: category.id,
                    visibility: visibilityStatus
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

                    let index = _this.boxCategories.data.indexOf(category);
                    _this.boxCategories.data[index].visibility = visibilityStatus;
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
                this.ckbIds = this.boxCategories.data.map((boxCategories) => boxCategories.id); 
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
                //confirmButtonColor: '#0a71b9',
                //cancelButtonColor: '#cc1f00',
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
            var url = "/categories/bulkDelete";
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

                    let jsonObj = _this.boxCategories;
                    _this.ckbIds.forEach(element => {
                        var indexOf = jsonObj.data.findIndex(data=> data.id === element);
                        jsonObj.data.splice(indexOf, 1);
                        jsonObj.total--;
                    }); 
                    _this.boxCategories = jsonObj;
                    _this.ckbIds = [];
                    _this.isCheckAll = false;
                    _this.$swal.close();
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
        searchCategories(event) {
            var _this = this;
            //console.log(event.keyCode + " : " + event.which)
            if (event.keyCode === 13 || event.which === 13) {
                _this.searchText = event.target.value;
                _this.getAllCategories(1, _this.searchText);
            } 
            if (event.target.value.length == 0) {
                _this.searchText = '';
                _this.getAllCategories();        
            }
        },
        searchByCategory(categoryID) {
            this.$router.push({ path: '/', query: { category: categoryID }})
        }
    },
    mounted() {
        this.getAllCategories();    
    },
}
</script>
