<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="box-title">Dashboard</span>
                            </div>
                            <div class="col-md-6 text-right">
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <router-link :to="{name : 'blogListRoute'}">
                                    <div class="chip">
                                        <img src="/images/blogging.png" width="96" height="96">
                                        Blogs ({{$root.sidebarCount.blogCount}})
                                    </div>
                                </router-link>
                            </div>
                            <div class="col-md-4">
                                <router-link :to="{name : 'myTaskListRoute'}">
                                    <div class="chip">
                                        <img src="/images/to-do-list.png" width="96" height="96">
                                        Tasks ({{$root.sidebarCount.taskCount}})
                                    </div>
                                </router-link>
                            </div>
                            <div class="col-md-4">
                                <router-link :to="{name : 'myNoteListRoute'}">
                                    <div class="chip">
                                        <img src="/images/notes.png" width="96" height="96">
                                        Notes ({{$root.sidebarCount.noteCount}})
                                    </div>
                                </router-link>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-4">
                                <router-link :to="{name : 'myPostBoxesRoute'}">
                                    <div class="chip">
                                        <img src="/images/programming-code-signs.png" width="96" height="96">
                                        Snippets ({{$root.sidebarCount.postBoxCount}})
                                    </div>
                                </router-link>
                            </div>
                            <div class="col-md-4">
                                <router-link :to="{name : 'myCategoryListRoute'}">
                                    <div class="chip">
                                        <img src="/images/categories.png" width="96" height="96">
                                        Categories ({{$root.sidebarCount.categoryCount}})
                                    </div>
                                </router-link>
                            </div>
                            <div class="col-md-4">
                                <router-link :to="{name : 'myKeywordListRoute'}">
                                    <div class="chip">
                                        <img src="/images/keyword-research.png" width="96" height="96">
                                        Keywords ({{$root.sidebarCount.keywordCount}})
                                    </div>
                                </router-link>
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
    mounted() {
        this.$root.isPageLoadingActive = false;
    }
}
</script>

<style scoped>
.chip {
  display: inline-block;
  padding: 0 25px;
  height: 50px;
  font-size: 16px;
  line-height: 50px;
  border-radius: 25px;
  background-color: #f1f1f1;
  font-weight: bold;
}

.chip img {
  float: left;
  margin: 0 10px 0 -25px;
  height: 50px;
  width: 50px;
  border-radius: 50%;
}
</style>
