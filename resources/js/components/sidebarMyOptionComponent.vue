<template>
    <div id="MyOption">
        <div class="list-group mb-3">
            <router-link :to="{name: 'homeRoute'}" class="list-group-item list-group-item-action text-option-bold">Home</router-link>
            <router-link :to="{name : 'addBoxRoute'}" class="list-group-item list-group-item-action text-option-bold">Add PostBox</router-link>
            <router-link :to="{name : 'addCategoryRoute'}" class="list-group-item list-group-item-action text-option-bold">Add Category</router-link>
        </div>
        <div class="list-group">
            <router-link :to="{name : 'myPostBoxesRoute'}" class="list-group-item list-group-item-action text-option-bold">Post Boxes <span class="badge badge-primary navbg badge-pill float-right" v-text="postBoxCount"></span></router-link>
            <router-link :to="{name : 'myCategoryListRoute'}" class="list-group-item list-group-item-action text-option-bold">Categories <span class="badge badge-primary navbg badge-pill float-right" v-text="categoryCount"></span></router-link>
            <router-link :to="{name : 'myKeywordListRoute'}" class="list-group-item list-group-item-action text-option-bold">Keywords <span class="badge badge-primary navbg badge-pill float-right" v-text="keywordCount"></span></router-link>
            <router-link :to="{name : 'myTaskListRoute'}" class="list-group-item list-group-item-action text-option-bold">Tasks <span class="badge badge-primary navbg badge-pill float-right" v-text="taskCount"></span></router-link>
            <router-link :to="{name : 'myNoteListRoute'}" class="list-group-item list-group-item-action text-option-bold">Notes <span class="badge badge-primary navbg badge-pill float-right" v-text="noteCount"></span></router-link>
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
