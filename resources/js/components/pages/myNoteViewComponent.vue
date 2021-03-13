<template>
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="deleteNote()"><i class="far fa-trash-alt"></i></a>
                                <a href="javascript:void(0);" class="btn btn-success btn-sm" @click="editNote()"><i class="far fa-edit"></i></a>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'myNoteListRoute'}">My Note List</router-link>
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'addNoteRoute'}">Create Note</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="capture" style="background-color: #fff8ba;">
                        <h1 v-html="noteInfo.title" style="font-weight:bold;"></h1>
                        <hr/>
                        <div v-html="noteInfo.note_content"></div>
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
            noteInfo: []
        }
    },
    methods: {
        async getNoteInfo() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get(`/notes/view/${_this.$route.params.slug}`).then(response => {
                _this.noteInfo = response.data.content.noteInfo;
                _this.$root.isPageLoadingActive = false; 
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
        deleteNote() {
            var _this = this;
            _this.$swal({
                title: 'Are you sure?',
                text: "You want to delete",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete!'
            }).then((actionResult) => {
                if (actionResult.value) {
                    this.deleteNoteProcess(); 
                }
            })
        },
        async deleteNoteProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/notes/delete";
            const deleteNoteProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    noteId: _this.noteInfo.id
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
                    _this.$root.getCounts();
                    setTimeout( function() { 
                        _this.$router.push({name: 'myNoteListRoute'}) 
                    }, 1000);
                }
            })
            .catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        editNote() {
            var _this = this;
            _this.$router.push({
                name: 'editNoteRoute',
                params: {
                    id: _this.noteInfo.id
                }
            })
        }
    },
    mounted() {
        this.getNoteInfo();
    }
}
</script>
