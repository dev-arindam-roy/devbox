import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import _addEditBoxComponent from '../components/pages/addEditBoxComponent.vue'
import _postBoxComponent from '../components/pages/postBoxComponent.vue'
import _myPostBoxListComponent from '../components/pages/myPostBoxListComponent.vue'

import _myCategoryListComponent from '../components/pages/myCategoryListComponent.vue'
import _addEditCategoryComponent from '../components/pages/addEditCategoryComponent.vue'

import _myKeywordListComponent from '../components/pages/myKeywordListComponent.vue'

import _myTaskListComponent from '../components/pages/myTaskListComponent.vue'
import _addTaskComponent from '../components/pages/addTaskComponent.vue'

import _myNoteListComponent from '../components/pages/myNoteListComponent.vue'

import _404Component from '../components/pages/404Component.vue'

const webRoutes = new VueRouter({
    mode: 'history',
    linkExactActiveClass: 'active',
    base: process.env.BASE_URL,
    routes: [
        {
            // path: '/',
            // components: {
            //     default: _boxSearchComponent,
            //     boxpost: _postBoxComponent
            // },
            // name: 'homeRoute'

            path: '/',
            component: _postBoxComponent,
            name: 'homeRoute'
        },

        {
            path: '/my-post-boxes',
            component: _myPostBoxListComponent,
            name: 'myPostBoxesRoute',
        },
        {
            path: '/my-post-boxes/addbox',
            component: _addEditBoxComponent,
            name: 'addBoxRoute',
        },
        {
            path: '/my-post-boxes/edit/:id',
            component: _addEditBoxComponent,
            name: 'editPostBoxRoute',
        },



        {
            path: '/my-categories',
            component: _myCategoryListComponent,
            name: 'myCategoryListRoute',
        },
        {
            path: '/my-categories/add',
            component: _addEditCategoryComponent,
            name: 'addCategoryRoute',
        },
        {
            path: '/my-categories/edit/:id',
            component: _addEditCategoryComponent,
            name: 'editCategoryRoute',
        },



        {
            path: '/my-keywords',
            component: _myKeywordListComponent,
            name: 'myKeywordListRoute',
        },



        {
            path: '/my-tasks',
            component: _myTaskListComponent,
            name: 'myTaskListRoute',
        },
        {
            path: '/my-tasks/add',
            component: _addTaskComponent,
            name: 'addTaskRoute',
        },
        {
            path: '/my-notes',
            component: _myNoteListComponent,
            name: 'myNoteListRoute',
        },


        {
            path: '*',
            name: '404',
            component: _404Component
        }
    ]
});

export default webRoutes;