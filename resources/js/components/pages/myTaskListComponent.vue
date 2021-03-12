<template>
    <div id="taskList">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">My Task List ({{taskList.total}})</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'addTaskRoute'}">Add Task</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <button type="button" :disabled="ckbIds.length == 0" class="btn btn-sm btn-danger" @click="deleteAllSelected()">Delete Setected</button>
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-2">
                                <select v-model.trim="searchStatus" class="sele-dropdown" style="width:100%;" v-on:change="searchByTaskStatus($event)">
                                    <option value="">Status</option>
                                    <option v-for="sts in taskStatus" :key="sts.id" :value="sts.id">{{sts.name}}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" v-model.trim="searchText" v-on:keyup="searchByTaskName($event)" class="form-control" placeholder="Search...">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">
                                                <input type="checkbox" v-model.trim="selectAllCkb" v-on:click="selectAll($event)">
                                            </th>
                                            <th>Task Name</th>
                                            <th style="width:70px; text-align:center;">SubTask</th>
                                            <th style="width:140px;">Status</th>
                                            <th style="width:120px;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="taskList.total > 0">
                                        <tr v-for="(task, index) in taskList.data" :key="task.id" 
                                            v-bind:class="{
                                                'table-primary': task.status == 2, 
                                                'table-success': task.status == 1, 
                                                'table-warning': task.status == 3, 
                                                'table-danger': task.status == 4 }">
                                            <td style="width:60px;">
                                                <input type="checkbox" v-model.trim="ckbIds" :value="task.id">
                                                {{index + 1}}
                                            </td>
                                            <td>
                                                <router-link :to="{name: 'viewTaskRoute', params: {id: task.id}}">{{task.name}}</router-link>
                                            </td>
                                            <td style="width:70px; text-align:center;">{{subTaskCount[task.id]}}</td>
                                            <td style="width:140px;">
                                                <select :value="task.status" class="sele-dropdown" v-on:change="changeTaskStatus(task, $event)">
                                                    <option v-for="sts in taskStatus" :key="sts.id" :value="sts.id">{{sts.name}}</option>
                                                </select>
                                            </td>
                                            <td style="width:120px;">
                                                <router-link :to="{name: 'editTaskRoute', params: {id: task.id}}" class="btn btn-success btn-sm"><i class="far fa-edit"></i></router-link>
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="deleteTask(task)"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="5">No Tasks Found!</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <pagination :data="taskList" @pagination-change-page="getAllTasks"></pagination>
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
            pagination: this.$defaultPagination,
            selectAllCkb: false,
            ckbIds: [],
            taskStatus: this.$taskStatusList,
            taskList: {},
            subTaskCount: [],
            searchText: '',
            searchStatus: ''
        }
    },
    watch: {
        ckbIds: function () {
            if (this.ckbIds.length == 0) {
                this.selectAllCkb = false
            }
        }
    },
    computed: {
        
    },
    methods: {
        async getAllTasks(page = 1, status = this.searchStatus, searchTxt = this.searchText) {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get('/tasks', {
                params: {
                    page: page,
                    status: status,
                    taskName: searchTxt,
                    pagination: _this.pagination
                }
            }).then(response => {
                _this.taskList = response.data.content.myTaskList;
                _this.subTaskCount = response.data.content.subTaskCount;
                _this.$root.isPageLoadingActive = false; 
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        async changeTaskStatus(task, event) {
            var _status = event.target.value;
            var _this = this;
            _this.$root.isPageLoadingActive = true; 
            var url = "/tasks/status";
            const changeStatusProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    id: task.id,
                    status: _status
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                _this.$root.isPageLoadingActive = false; 
                if (response.data.status == 200) {
                    _this.$toast.success({
                        title:'Success',
                        message:response.data.msg
                    });

                    let index = _this.taskList.data.indexOf(task);
                    _this.taskList.data[index].status = _status;
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
        deleteTask(task) {
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
                    this.deleteTaskProcess(task); 
                }
            })
        },
        async deleteTaskProcess(task) {
            var _this = this;
            var url = "/tasks/delete";
            const deleteTaskProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    taskId: task.id
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
                    let index = _this.taskList.data.indexOf(task);
                    _this.taskList.data.splice(index, 1);
                    _this.taskList.total--;
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
        selectAll(event) {
            if (event.target.checked) {
                this.ckbIds = this.taskList.data.map((taskList) => taskList.id); 
            } else {
               this.ckbIds = []; 
            }
        },
        deleteAllSelected() {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to delete selected records",
                icon: 'question',
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
            var url = "/tasks/bulkDelete";
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

                    let jsonObj = _this.taskList;
                    _this.ckbIds.forEach(element => {
                        var indexOf = jsonObj.data.findIndex(data=> data.id === element);
                        jsonObj.data.splice(indexOf, 1);
                        jsonObj.total--;
                    }); 
                    _this.taskList = jsonObj;
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
        searchByTaskStatus(event) {
            var _this = this;
            _this.searchStatus = event.target.value;
            _this.processFilter();
        },
        searchByTaskName(event) {
            var _this = this;
            if (event.keyCode === 13 || event.which === 13) {
                _this.searchText = event.target.value;
                _this.processFilter();
            } 
            if (event.target.value.length == 0) {
                _this.searchText = '';
                _this.processFilter();        
            }
        },
        processFilter() {
            var _this = this;
            _this.getAllTasks(1, _this.searchStatus);
        }
    },
    mounted() {
        this.getAllTasks();
    }
}
</script>

<style scoped>
.sele-dropdown {
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #cccbcb;
}
</style>
