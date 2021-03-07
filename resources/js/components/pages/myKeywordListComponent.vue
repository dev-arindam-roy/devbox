<template>
    <div id="keywordsID">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><span class="box-title">My Keyword List ({{keywordsList.length}})</span></div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6">
                                <input type="text" v-model.trim="searchKeyword" class="form-control" placeholder="Search keyword" v-on:keyup="searchKeywords($event)">
                            </div>
                        </div>
                        <div class="row mt-3" v-if="keywordsList.length > 0">
                            <div class="col-md-12">
                                <span style="font-size: 16px !important;" class="keywords keyword-size badge badge-secondary mr-1" v-for="keyword in keywordsList" v-bind:key="keyword.keywordName" @click="searchByKeyword(keyword.keywordName)">{{keyword.keywordName}} ({{keyword.use}})</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        
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
            searchKeyword: '',
            keywordsList: []
        }
    },
    methods: {
        async getAllKeywords() {
            var _this = this;
            _this.$root.isPageLoadingActive = true;
            axios.get('/keywords', {
                params: {
                    searchKeyword: _this.searchKeyword
                }
            }).then(response => {
                _this.keywordsList = response.data.content.keywords;
                _this.$root.isPageLoadingActive = false; 
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        searchKeywords(event) {
            var _this = this;
            if (event.keyCode === 13 || event.which === 13) {
                _this.searchKeyword = event.target.value;
                _this.getAllKeywords();
            } 
            if (event.target.value.length == 0) {
                _this.searchText = '';
                _this.getAllKeywords();        
            }
        },
        searchByKeyword(keyword) {
            this.$router.push({ path: '/', query: { keyword: keyword }})
        }
    },
    mounted() {
        this.getAllKeywords();
    }
}
</script>

