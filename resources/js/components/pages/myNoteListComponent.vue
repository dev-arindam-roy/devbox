<template>
    <div id="NoteList">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">My Notes ({{noteList.total}})</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'addNoteRoute'}">Add Note</router-link>
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
                                <select v-model.trim="searchStatus" class="sele-dropdown" style="width:100%;" v-on:change="searchByNoteStatus($event)">
                                    <option value="">Status</option>
                                    <option v-for="sts in noteStatusList" :key="sts.id" :value="sts.id">{{sts.name}}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" v-model.trim="searchText" v-on:keyup="searchByNoteTitle($event)" class="form-control" placeholder="Search...">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">
                                                <input type="checkbox" v-model.trim="selectAllCkb" v-on:click="selectAll($event)">
                                            </th>
                                            <th>Note Title</th>
                                            <th style="width:120px; text-align:center;">Modified</th>
                                            <th style="width:120px;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="noteList.total > 0">
                                        <tr v-for="(note, index) in noteList.data" :key="note.id">
                                            <td style="width: 60px;">
                                                <input type="checkbox" v-model.trim="ckbIds" :value="note.id">
                                                {{index + 1}}
                                            </td>
                                            <td>
                                                <router-link :to="{name: 'viewNoteRoute', params: {slug: note.slug}}">{{note.title}}</router-link>
                                            </td>
                                            <td style="width:120px; text-align:center;">
                                                <span v-text="note.updated_at != '' ? dateFormat(note.updated_at) : dateFormat(note.created_at)"></span>
                                            </td>
                                            <td style="width:120px;">
                                                <router-link :to="{name: 'editNoteRoute', params: {id: note.id}}" class="btn btn-success btn-sm"><i class="far fa-edit"></i></router-link>
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="deleteNote(note)"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="4">Notes Not Found!</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <pagination :data="noteList" @pagination-change-page="getAllNotes"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
var moment = require('moment');
export default {
    data() {
        return {
            pagination: this.$defaultPagination,
            noteStatusList: this.$noteStatusList,
            noteList: {},
            ckbIds: [],
            searchText: '',
            searchStatus: '',
            selectAllCkb: false,
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
        dateFormat(date) {
            return moment(date).format('DD-MM-YYYY');
        },
        async getAllNotes(page = 1) {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get('/notes', {
                params: {
                    page: page,
                    noteTitle: _this.searchText,
                    status: _this.searchStatus,
                    pagination: _this.pagination
                }
            }).then(response => {
                _this.noteList = response.data.content.myNoteList;
                _this.ckbIds = [];
                _this.selectAllCkb = false;
                _this.$root.isPageLoadingActive = false; 
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        deleteNote(note) {
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
                    this.deleteNoteProcess(note); 
                }
            })
        },
        async deleteNoteProcess(note) {
            var _this = this;
            var url = "/notes/delete";
            const deleteNoteProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    noteId: note.id
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
                    let index = _this.noteList.data.indexOf(note);
                    _this.noteList.data.splice(index, 1);
                    _this.noteList.total--;
                    _this.ckbIds = [];
                    _this.selectAllCkb = false;
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
                this.ckbIds = this.noteList.data.map((noteList) => noteList.id); 
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
            var url = "/notes/bulkDelete";
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

                    let jsonObj = _this.noteList;
                    _this.ckbIds.forEach(element => {
                        var indexOf = jsonObj.data.findIndex(data=> data.id === element);
                        jsonObj.data.splice(indexOf, 1);
                        jsonObj.total--;
                    }); 
                    _this.noteList = jsonObj;
                    _this.ckbIds = [];
                    _this.selectAllCkb = false;
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
        searchByNoteTitle(event) {
            var _this = this;
            if (event.keyCode === 13 || event.which === 13) {
                _this.searchText = event.target.value;
                _this.getAllNotes();
            } 
            if (event.target.value.length == 0) {
                _this.searchText = '';
                _this.getAllNotes();        
            }
        },
        searchByNoteStatus(event) {
            var _this = this;
            _this.searchStatus = event.target.value;
            _this.getAllNotes();  
        }
    },
    mounted() {
        this.getAllNotes();
    }
}
</script>
