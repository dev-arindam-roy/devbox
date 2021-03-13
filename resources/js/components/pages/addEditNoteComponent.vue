<template>
    <form if="addEditNote" @submit.prevent="addEditNote">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title" v-text="editNoteId == 0 ? 'Create Note' : 'Edit Note'"></span>
                            </div>
                            <div class="col-md-6 text-right">
                                <a class="btn btn-sm btn-success" v-show="$route.params.id != undefined" @click="viewNote()"><i class="far fa-eye"></i></a>
                                <router-link class="btn btn-sm btn-primary navbg" :to="{name: 'myNoteListRoute'}">All Notes</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <validationErrorComponent :validationErrorListArr="validationErrors" class="mb-1"></validationErrorComponent>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Edit Mode -->
                                <div class="noteEditMode" v-if="editNoteId != 0">
                                    <div class="form-group">
                                        <select v-model.trim="noteStatus" class="sele-dropdown" @change="updateNoteStatus($event)">
                                            <option v-for="sts in noteStatusList" :key="sts.id" :value="sts.id">{{sts.name}}</option>
                                        </select>
                                        <select v-model.trim="notePercentage" class="sele-dropdown" @change="updateProgressPercentage($event)">
                                            <option v-for="percentageItem in percentageList" :key="percentageItem" :value="percentageItem">{{percentageItem}}%</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" v-bind:class="progressBar.bgClass" role="progressbar" v-bind:style="[progressBar.style]" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{progressBar.value}}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Edit Mode -->
                                <div class="form-group">
                                    <label class="form-lebel">Heading: <em>*</em></label>
                                    <input type="text" v-model.trim="title" class="form-control" placeholder="Note Title">
                                    <div class="text-danger" v-if="!$v.title.required && $v.title.$error">Please enter note title</div>
                                    <div class="text-danger" v-if="!$v.title.minLength && $v.title.required && $v.title.$error">Minimum 3 chars required</div>
                                </div>
                                <!-- Edit Mode -->
                                <div class="form-group" v-show="editNoteId != 0">
                                    <i class="far fa-edit hoverme" @click="editSlug"></i> <a :href="noteSlug" target="_blank">{{noteSlug}}</a>
                                </div>
                                <div class="row" v-show="editNoteId != 0 && isSlugEditable">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="form-lebel">Slug: <em>*</em></label>
                                            <input type="text" v-model.trim="slugPart" class="form-control" placeholder="Slug">
                                            <div class="text-danger" v-if="!$v.slugPart.required && $v.slugPart.regxSlug && $v.slugPart.$error">Please enter a slug</div>
                                            <div class="text-danger" v-if="!$v.slugPart.regxSlug && $v.slugPart.$error">Please enter a valid slug</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" style="margin-top: 34px;" class="btn btn-sm btn-danger" :disabled="isCheckSlugBtnDisabled" @click="checkSlug()">Check Slug</button>
                                    </div>
                                </div>
                                <!-- End Edit Mode -->
                                <div class="form-group">
                                    <label class="form-lebel">Content: <em>*</em></label>
                                    <vue-editor id="noteEditor" v-model="note_content" :editorToolbar="customToolbar"></vue-editor>
                                    <div class="text-danger" v-if="!$v.note_content.required && $v.note_content.$error">Please enter note content</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" :disabled="isDisabled" class="btn btn-success">{{submitButtonText}}</button>
                        <input type="hidden" v-model.trim="editNoteId">
                        <button type="button" class="btn btn-danger float-right" @click="editNoteId == 0 ? resetAll() : backToNoteList()">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import { VueEditor } from 'vue2-editor'
import { required, minLength, requiredIf } from 'vuelidate/lib/validators'
import validationErrorComponent from '../validationErrorsComponent.vue'
const regxSlug = (value, vm) => {
  if (value == '') return true
  if (value != '') {
    return /^[A-Za-z0-9_-]+$/.test(value)
  }
}
export default {
    components: {
        validationErrorComponent,
        VueEditor
    },
    data() {
        return {
            noteStatusList: this.$noteStatusList,
            percentageList: [0, 10, 25, 50, 75, 100],
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
            validationErrors: [],

            title: '',
            note_content: '',
            noteStatus: 0,
            notePercentage: 0,
            noteSlug: '',
            slugPart: '',
            tempCurrentSlug: '',
            isSlugEditable: false,
            submitButtonText: 'Create',
            editNoteInfo: [],
            editNoteId: 0,

            progressBar: {
                style: {
                    width: '0%',
                    color: '#fff',
                },
                value: '0%',
                bgClass: '',
            },
        }
    },
    watch: {

    },
    computed: {
        isDisabled: function () {
            return this.title == '' || this.note_content == '';
        },
        isCheckSlugBtnDisabled: function () {
            return this.slugPart == '' || this.tempCurrentSlug == this.slugPart;
        }
    },
    validations: {
        title: {
            required,
            minLength: minLength(3)
        },
        note_content: {
            required
        },
        slugPart: {
            requiredIf: requiredIf(function () {
                return this.editNoteId != 0
            }),
            regxSlug
        },
    },
    methods: {
        resetAll() {
            var _this = this;
            _this.validationErrors = [];
            _this.title = '';
            _this.note_content = '';
            _this.submitButtonText = 'Create';
            _this.editNoteId = 0;
        },
        backToNoteList() {
            this.$router.push({ name: 'myNoteListRoute' })
        },
        addEditNote() {
            var _this = this;
            _this.$v.$touch();
            if (!_this.$v.$error) {
                if (_this.$route.params.id != undefined) {
                    _this.updateNoteProcess();
                } else {
                    _this.createNoteProcess();
                }
            }
        },
        async createNoteProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = "/notes/add";
            const addNoteProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    title: _this.title,
                    note_content: _this.note_content
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.validationErrors = [];
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
                        // Access root method
                        _this.$root.getCounts();
                        setTimeout( function () { 
                            _this.$router.push({ name: 'viewNoteRoute', params: { slug: response.data.content.note.slug }})
                        }, 2000);
                    }
                    if (response.data.type == 'error') {
                        _this.$root.isPageLoadingActive = false;
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
        async updateNoteProcess() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            var url = `/notes/update/${_this.editNoteId}`;
            const updateNoteProcess = await axios({
                method: 'post',
                url: url,
                data: {
                    title: _this.title,
                    note_content: _this.note_content,
                    status: _this.noteStatus,
                    completed_percentage: _this.notePercentage,
                    isSlugEditable: _this.isSlugEditable,
                    slugPart: _this.slugPart
                },
                headers: {'Content-Type': 'application/json'}
            })
            .then(function (response) {
                if (response.data.status == 200) {
                    _this.validationErrors = [];
                    _this.$v.$reset();
                    if (response.data.type == 'success') {
                        _this.$toast.success({
                            title:'Success',
                            message:response.data.msg 
                        });
                        setTimeout( function () { 
                            _this.$router.push({ name: 'viewNoteRoute', params: { slug: response.data.content.note.slug }})
                        }, 2000);
                    }
                    if (response.data.type == 'error') {
                        console.log('slug error');
                        _this.$root.isPageLoadingActive = false;
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
        async getNoteEditInfo(editId) {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get(`/notes/edit/${editId}`).then(response => {
                // full object
                _this.editNoteInfo = response.data.content.noteInfo;
                _this.title = response.data.content.noteInfo.title;
                _this.note_content = response.data.content.noteInfo.note_content;
                _this.noteStatus = response.data.content.noteInfo.status;
                _this.notePercentage = response.data.content.noteInfo.completed_percentage;
                // slug
                _this.slugPart = response.data.content.noteInfo.slug;
                // slug in a temp variable
                _this.tempCurrentSlug = response.data.content.noteInfo.slug;
                // slug with full url
                _this.noteSlug = window.location.origin + '/notes/' + response.data.content.noteInfo.slug;
                _this.progressPercentageProcess();
                _this.submitButtonText = 'Save Changes';
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
        progressPercentageProcess() {
            var _this = this;
            var bg = '';
            var clr = '#fff';
            if (_this.notePercentage > 0 && _this.notePercentage <= 10) {
                bg = 'bg-danger';
            } else if (_this.notePercentage > 10 && _this.notePercentage <= 25) {
                bg = 'bg-warning';
                clr = '#000';
            } else if (_this.notePercentage > 25 && _this.notePercentage <= 75) {
                bg = 'bg-primary';
            } else if (_this.notePercentage > 75) {
                bg = 'bg-success';
            }
            _this.progressBar.style.width = _this.notePercentage + '%';
            _this.progressBar.style.color = clr;
            _this.progressBar.value = _this.notePercentage + '%';
            _this.progressBar.bgClass = bg;
        },
        updateProgressPercentage(event) {
            var _this = this;
            if (event.target.value < 100) {
                _this.noteStatus = 0;
            }
            if (event.target.value == 100) {
                _this.noteStatus = 1;
            }
            _this.notePercentage = event.target.value;
            _this.progressPercentageProcess();
        },
        updateNoteStatus(event) {
            var _this = this;
            if (event.target.value == 1) {
                _this.notePercentage = 100;
            } else {
                _this.notePercentage = _this.editNoteInfo.completed_percentage;
            }
            _this.progressPercentageProcess();
        },
        editSlug() {
            var _this = this;
            if (_this.isSlugEditable) {
                _this.isSlugEditable = false;
            } else {
                _this.isSlugEditable = true;
            }
        },
        async checkSlug() {
            var _this = this;
            _this.$v.slugPart.$touch();
            if (_this.slugPart.length > 0 && !_this.$v.slugPart.$error) {
                _this.$root.isPageLoadingActive = true;
                var url = "/check-slug/existOrNot";
                const checkSlugProcess = await axios({
                    method: 'post',
                    url: url,
                    data: {
                        slug: _this.slugPart,
                        id: _this.editNoteId
                    },
                    headers: {'Content-Type': 'application/json'}
                })
                .then(function (response) {
                    if (response.data.status == 200) {
                        _this.$root.isPageLoadingActive = false;
                        if (response.data.type == 'error') {
                            _this.$toast.error({
                                title:'Error',
                                message:response.data.msg 
                            });
                        }
                        if (response.data.type == 'success') {
                            _this.$swal({
                                position: 'center',
                                icon: 'success',
                                title: 'Slug is available',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                    }
                })
                .catch(function (error) {
                    _this.$root.isPageLoadingActive = false;
                    _this.$toast.error({
                        title:'System Error',
                        message:'Something went wrong!'
                    });
                });
            }
        },
        viewNote() {
            var _this = this;
            _this.$router.push({
                name: 'viewNoteRoute',
                params: {
                    slug: _this.slugPart
                }
            })
        }
    },
    mounted() {
        if (this.$route.params.id != undefined) {
            this.editNoteId = this.$route.params.id;
            this.getNoteEditInfo(this.editNoteId);
        }
    }
}
</script>

<style>
.ql-editor {
    min-height: 300px !important
}
.sele-dropdown {
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #cccbcb;
}
.hoverme:hover {
    cursor: pointer;
}
.swal2-title {
    font-size: 18px !important;
}
</style>
