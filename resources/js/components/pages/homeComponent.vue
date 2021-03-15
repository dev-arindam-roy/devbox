<template>
    <div id="home">
        <header-navmenu class="mb-3"></header-navmenu>
        <div class="container mb-5">
            <div class="row">
                <div class="col-md-3">
                    <myoption-navmenu class="mb-3"
                        :post-box-count="sidebarCount.postBoxCount"
                        :category-count="sidebarCount.categoryCount"
                        :keyword-count="sidebarCount.keywordCount"
                        :task-count="sidebarCount.taskCount"
                        :note-count="sidebarCount.noteCount" 
                        :blog-count="sidebarCount.blogCount"></myoption-navmenu>
                </div>
                <div class="col-md-9">
                    <router-view :key="$route.path"></router-view>
                    <!-- <router-view name="boxpost"></router-view> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
    }
}
</script>
