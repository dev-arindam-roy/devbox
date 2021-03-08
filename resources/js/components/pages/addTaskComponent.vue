<template>
    <form @submit.prevent="submitTask">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">Create Task</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'myTaskListRoute'}">All Tasks</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <validationErrorComponent :validationErrorListArr="validationErrors"></validationErrorComponent>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-lebel">Task Name: <em>*</em></label>
                                    <input type="text" v-model.trim="taskName" class="form-control" placeholder="Task Name">
                                    <div class="text-danger" v-if="!$v.taskName.required && $v.taskName.$error">Please enter task name</div>
                                    <div class="text-danger" v-if="!$v.taskName.minLength && $v.taskName.required && $v.taskName.$error">Minimum 3 chars required</div>
                                </div>
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
                                <input type="submit" class="btn btn-success" value="Add Task">
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-danger" @click="resetAll">Cancel</button>
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
            taskName: '',
            subTaskList: [
                {
                    name: '',
                    description: ''
                }
            ],
            validationErrors: [],
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
        },
        remove(index) {
            this.subTaskList.splice(index, 1);
        },
        submitTask() {
            var _this = this;
            _this.$v.$touch();
            if (!this.$v.$error) {
                _this.createTaskProcess();
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
    },
    mounted() {
    }
}
</script>
