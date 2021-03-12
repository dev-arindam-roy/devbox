<template>
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="deleteTask()"><i class="far fa-trash-alt"></i></a>
                                <router-link :to="{name: 'editTaskRoute', params: {id: $route.params.id}}" class="btn btn-success btn-sm"><i class="far fa-edit"></i></router-link>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'myTaskListRoute'}">My Task List</router-link>
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'addTaskRoute'}">Add Task</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="capture" style="background-color: #fff8ba;">
                        <h1 v-html="taskInfo.name" style="font-weight:bold;"></h1>
                        <hr/>
                        <p class="tsk-details" v-html="taskInfo.description"></p>
                        <div class="subtask-block" v-if="subTaskInfo.length > 0">
                            <div class="row" v-for="(stk, indx) in subTaskInfo" :key="stk.id">
                                <div class="col-md-12">
                                    <h3><strong>{{indx+1}}. {{stk.name}}</strong></h3>
                                    <p v-html="stk.description"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    data() {
        return {
            taskInfo: [],
            subTaskInfo: []
        }
    },
    methods: {
        async getTaskInfo() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get(`/tasks/view/${_this.$route.params.id}`).then(response => {
                _this.taskInfo = response.data.content.taskInfo;
                _this.subTaskInfo = response.data.content.subTasksInfo;
                _this.$root.isPageLoadingActive = false; 
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
                if (error.response.status === 404) {
                    setTimeout(function () { 
                        window.location.href = '/';
                        //_this.$router.push({ path: '/404'})
                    }, 2000);
                }
            });
        },
        deleteTask() {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to delete",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!'
            }).then((actionResult) => {
                if (actionResult.value) {
                    this.deleteTaskProcess(); 
                }
            })
        },
        async deleteTaskProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/tasks/delete";
            const deleteTaskProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    taskId: _this.taskInfo.id
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
                    setTimeout( function() { 
                        window.location.href = window.location.origin + "/my-tasks"; 
                    }, 1000);
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        }
    },
    mounted() {
        this.getTaskInfo();
    }
}
</script>
