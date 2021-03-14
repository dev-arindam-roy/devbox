<template>
    <div id="MyOption">
        <div class="list-group mb-3">
            <router-link :to="{name: 'homeRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="fas fa-home"></i> Home</router-link>
            <router-link :to="{name : 'addBlogRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="far fa-plus-square"></i> Add Blog</router-link>
            <router-link :to="{name : 'addBoxRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="far fa-plus-square"></i> Add PostBox</router-link>
            <router-link :to="{name : 'addCategoryRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="far fa-plus-square"></i> Add Category</router-link>
        </div>
        <div class="list-group">
            <router-link :to="{name : 'myPostBoxesRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="fas fa-list"></i> Post Boxes <span class="badge badge-primary navbg badge-pill float-right" v-text="$props.postBoxCount"></span></router-link>
            <router-link :to="{name : 'myCategoryListRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="fas fa-list"></i> Categories <span class="badge badge-primary navbg badge-pill float-right" v-text="$props.categoryCount"></span></router-link>
            <router-link :to="{name : 'myKeywordListRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="fas fa-list"></i> Keywords <span class="badge badge-primary navbg badge-pill float-right" v-text="$props.keywordCount"></span></router-link>
            <router-link :to="{name : 'myTaskListRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="fas fa-list"></i> Tasks <span class="badge badge-primary navbg badge-pill float-right" v-text="$props.taskCount"></span></router-link>
            <router-link :to="{name : 'myNoteListRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="fas fa-list"></i> Notes <span class="badge badge-primary navbg badge-pill float-right" v-text="$props.noteCount"></span></router-link>
            <router-link :to="{name : 'blogListRoute'}" class="list-group-item list-group-item-action text-option-bold"><i class="fas fa-list"></i> Blogs <span class="badge badge-primary navbg badge-pill float-right" v-text="$props.blogCount"></span></router-link>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        postBoxCount: {
            type: Number,
            default: 0
        },
        categoryCount: {
            type: Number,
            default: 0
        },
        keywordCount: {
            type: Number,
            default: 0
        },
        taskCount: {
            type: Number,
            default: 0
        },
        noteCount: {
            type: Number,
            default: 0
        },
        blogCount: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
        }
    },
    methods: {
    },
    mounted() {
    }
}
</script>
