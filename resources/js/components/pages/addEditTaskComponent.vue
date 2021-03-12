<template>
    <form @submit.prevent="submitTask">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title" v-if="$route.params.id != undefined">Edit Task</span>
                                <span class="box-title" v-else>Create Task</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link class="btn btn-sm btn-success" :to="{name: 'viewTaskRoute', params: {id: $route.params.id}}" v-show="$route.params.id != undefined"><i class="far fa-eye"></i></router-link>
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'addTaskRoute'}" v-show="$route.params.id != undefined">Add Task</router-link>
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'myTaskListRoute'}">All Tasks</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <validationErrorComponent :validationErrorListArr="validationErrors"></validationErrorComponent>
                        <div class="row">
                            <!-- edit time add task status -->
                            <div class="col-md-12" style="border-radius: 5px;" v-if="$route.params.id != undefined" v-bind:class="{'bg-primary' : taskStatus == 2, 'bg-success' : taskStatus == 1, 'bg-warning' : taskStatus == 3, 'bg-danger' : taskStatus == 4}">
                                <div class="form-group mt-3">
                                    <select :value="taskStatus" class="sele-dropdown" v-on:change="changeTaskStatus($event)">
                                        <option v-for="sts in taskStatusList" :key="sts.id" :value="sts.id">{{sts.name}}</option>
                                    </select>
                                </div>
                            </div>
                            <!-- end -->
                            
                            <div class="col-md-12" v-bind:class="{'mt-2' : $route.params.id != undefined}">
                                <div class="form-group">
                                    <label class="form-lebel">Task Name: <em>*</em></label>
                                    <input type="text" v-model.trim="taskName" class="form-control" placeholder="Task Name">
                                    <div class="text-danger" v-if="!$v.taskName.required && $v.taskName.$error">Please enter task name</div>
                                    <div class="text-danger" v-if="!$v.taskName.minLength && $v.taskName.required && $v.taskName.$error">Minimum 3 chars required</div>
                                </div>
                                <!-- edit time add task editor -->
                                <div class="form-group" v-if="$route.params.id != undefined">
                                    <label class="form-lebel">Task Details:</label>
                                    <vue-editor v-model="taskDescription" :editorToolbar="customToolbar"></vue-editor>
                                </div>
                                <!-- end -->
                            </div>
                        </div>
                        <div class="subTaskContainer">
                            <div class="row" v-bind:class="{ 'sub-task-border': index > 0 }" v-for="(subTask, index) in subTaskList" :key="index">
                                <div class="col-md-10" v-bind:class="{ 'mt-2': index > 0 }">
                                    <div class="form-group">
                                        <label class="form-lebel"><strong style="color:#000;">{{index + 1}}.</strong> Add SubTask Name:</label>
                                        <input type="text" v-model.trim="subTask.name" class="form-control" placeholder="SubTask Name">
                                    </div>
                                    <div class="form-group">
                                        <textarea v-model.trim="subTask.description" class="form-control" placeholder="SubTask Description"></textarea>
                                    </div>
                                     <!-- edit time add subtask status -->
                                    <div class="form-group" v-if="$route.params.id != undefined && subTask.status != undefined">
                                        <select :value="subTask.status" class="sele-dropdown" v-on:change="changeSubTaskStatus(subTask, $event)" v-bind:class="{'bg-primary' : subTask.status == 2, 'bg-success' : subTask.status == 1, 'bg-warning' : subTask.status == 3, 'bg-danger' : subTask.status == 4}">
                                            <option v-for="sts in taskStatusList" :key="sts.id" :value="sts.id">{{sts.name}}</option>
                                        </select>
                                    </div>
                                    <!-- end -->
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-danger" style="margin-top:35px;" @click="remove(index)" v-show="index || ( !index && subTaskList.length > 1)"><i class="far fa-trash-alt"></i></button>
                                    <button type="button" class="btn btn-sm btn-success" style="margin-top:35px;" @click="add(index)" v-show="index == subTaskList.length - 1"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="submit" class="btn btn-success" :value="submitButtonText">
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" v-show="$route.params.id == undefined" class="btn btn-danger" @click="resetAll">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
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
            taskStatusList: this.$taskStatusList,

            taskName: '',
            taskDescription: '',
            taskStatus: 0,
            subTaskList: [
                {
                    name: '',
                    description: ''
                }
            ],
            validationErrors: [],

            editTaskID: 0,
            submitButtonText: 'Add Task',
        }
    },
    validations: {
        taskName: {
            required,
            minLength: minLength(3)
        }
    },
    methods: {
        resetAll() {
            var _this = this;
            _this.taskName = '';
            _this.subTaskList = [{name: '', description: ''}];
            _this.validationErrors = [],
            _this.$v.$reset();
        },
        add(index) {
            this.subTaskList.push({ name: '' });
            this.addRemoveToast();
        },
        remove(index) {
            this.subTaskList.splice(index, 1);
            this.addRemoveToast();
        },
        submitTask() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                if (_this.$route.params.id != undefined) {
                    _this.updateTaskProcess();
                } else {
                    _this.createTaskProcess();
                }
            }
        },
        async createTaskProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/tasks/add";
            const addTaskProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    taskName: _this.taskName,
                    subTasks: _this.subTaskList
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                _this.$root.isPageLoadingActive = false;
                if (response.data.status == 200) {
                    if (response.data.type == 'success') {
                        _this.resetAll();
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
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
        async updateTaskProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = `/tasks/update/${_this.$route.params.id}`;
            const updateTaskProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    taskName: _this.taskName,
                    taskDescription: _this.taskDescription,
                    subTasks: _this.subTaskList
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                _this.$root.isPageLoadingActive = false;
                if (response.data.status == 200) {
                    if (response.data.type == 'success') {
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
                        _this.$v.$reset();
                        _this.getEditTaskInfo(_this.$route.params.id);
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
        async getEditTaskInfo(editTskId) {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get(`/tasks/view/${editTskId}`).then(response => {
                _this.taskName = response.data.content.taskInfo.name;
                _this.taskDescription = response.data.content.taskInfo.description;
                _this.taskStatus = response.data.content.taskInfo.status;

                if (response.data.content.subTasksInfo.length > 0) {
                    _this.subTaskList = response.data.content.subTasksInfo;
                } else {
                    _this.subTaskList = [{name: '', description: ''}];
                }
                
                _this.$root.isPageLoadingActive = false; 
                _this.submitButtonText = 'Save Changes';
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
        async changeSubTaskStatus(subtask, event) {
            var _status = event.target.value;
            var _this = this;
            _this.$root.isPageLoadingActive = true; 
            var url = "/tasks/subtask-status";
            const changeStatusProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    id: subtask.id,
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

                    let index = _this.subTaskList.indexOf(subtask);
                    _this.subTaskList[index].status = _status;
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        async changeTaskStatus(event) {
            var _status = event.target.value;
            var _this = this;
            _this.$root.isPageLoadingActive = true; 
            var url = "/tasks/status";
            const changeStatusProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    id: _this.editTaskID,
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
                    _this.taskStatus = _status;
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
        addRemoveToast() {
            var _this = this;
            if (_this.$route.params.id != undefined) {
                _this.$toast.info({
                    title:'Info',
                    message:'Please click on Save Changes button to save the edit'
                });
            }
        }
    },
    mounted() {
        if (this.$route.params.id != undefined) {
            this.editTaskID = this.$route.params.id;
            this.getEditTaskInfo(this.editTaskID);
        }
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
