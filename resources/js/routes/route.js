import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import _loginComponent from '../components/pages/loginComponent.vue'
import _registrationComponent from '../components/pages/registrationComponent.vue'

import _authInProgressComponent from '../components/pages/authInProgressComponent.vue'

import _dashboardComponent from '../components/pages/dashboardComponent.vue'
import _homeComponent from '../components/pages/homeComponent.vue'

import _myAccountComponent from '../components/pages/myAccountComponent.vue'

import _addEditBoxComponent from '../components/pages/addEditBoxComponent.vue'
import _postBoxComponent from '../components/pages/postBoxComponent.vue'
import _myPostBoxListComponent from '../components/pages/myPostBoxListComponent.vue'

import _myCategoryListComponent from '../components/pages/myCategoryListComponent.vue'
import _addEditCategoryComponent from '../components/pages/addEditCategoryComponent.vue'

import _myKeywordListComponent from '../components/pages/myKeywordListComponent.vue'

import _myTaskListComponent from '../components/pages/myTaskListComponent.vue'
import _addEditTaskComponent from '../components/pages/addEditTaskComponent.vue'
import _myTaskViewComponent from '../components/pages/myTaskViewComponent.vue'

import _myNoteListComponent from '../components/pages/myNoteListComponent.vue'
import _addEditNoteComponent from '../components/pages/addEditNoteComponent.vue'
import _myNoteViewComponent from '../components/pages/myNoteViewComponent.vue'

import _myBlogListComponent from '../components/pages/myBlogListComponent.vue'
import _addEditBlogComponent from '../components/pages/addEditBlogComponent.vue'

import _404Component from '../components/pages/404Component.vue'

const _numericRegx = /^\d+$/
const _alphNumericRegx = /^[A-Za-z0-9-_]+$/

const webRoutes = new VueRouter({
    mode: 'history',
    linkExactActiveClass: 'active',
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/',
            component: _loginComponent,
            name: 'loginRoute',
        },
        {
            path: '/create-account',
            component: _registrationComponent,
            name: 'registrationRoute'
        },
        {
            path: '/authentication-inprogress',
            component: _authInProgressComponent,
            name: 'authInprogressRoute'
        },

        {
            path: '/dashboard',
            component: _dashboardComponent,
            name: 'dashboardRoute'
        },
        {
            path: '/home',
            component: _homeComponent,
            name: 'homeRoute'
        },


        {
            path: '/my-account',
            component: _myAccountComponent,
            name: 'myAccountRoute'
        },


        {
            // path: '/',
            // components: {
            //     default: _boxSearchComponent,
            //     boxpost: _postBoxComponent
            // },
            // name: 'homeRoute'

            path: '/my-post-boxes',
            component: _postBoxComponent,
            name: 'snippetRoute',
            // meta: {
            //     requiredAuth: true
            // }
        },

        {
            path: '/my-post-boxes/list',
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
            props: true,
            beforeEnter: (to, from, next) => {
                if (!_numericRegx.test(to.params.id)) {
                    next({name: 'notfound'})
                }
                next()
            }
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
            props: true,
            beforeEnter: (to, from, next) => {
                if (!_numericRegx.test(to.params.id)) {
                    next({name: 'notfound'})
                }
                next()
            }
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
            component: _addEditTaskComponent,
            name: 'addTaskRoute',
        },
        {
            path: '/my-tasks/edit/:id',
            component: _addEditTaskComponent,
            name: 'editTaskRoute',
            props: true,
            beforeEnter: (to, from, next) => {
                if (!_numericRegx.test(to.params.id)) {
                    next({name: 'notfound'})
                }
                next()
            }
        },
        {
            path: '/my-task/view/:id',
            component: _myTaskViewComponent,
            name: 'viewTaskRoute',
            props: true,
            beforeEnter: (to, from, next) => {
                if (!_numericRegx.test(to.params.id)) {
                    next({name: 'notfound'})
                }
                next()
            }
        },


        {
            path: '/my-notes',
            component: _myNoteListComponent,
            name: 'myNoteListRoute',
        },
        {
            path: '/my-notes/add',
            component: _addEditNoteComponent,
            name: 'addNoteRoute'
        },
        {
            path: '/my-notes/view/:slug',
            component: _myNoteViewComponent,
            name: 'viewNoteRoute',
            props: true,
            beforeEnter: (to, from, next) => {
                if (!_alphNumericRegx.test(to.params.slug)) {
                    next({name: 'notfound'})
                }
                next()
            }
        },
        {
            path: '/my-notes/edit/:id',
            component: _addEditNoteComponent,
            name: 'editNoteRoute',
            props: true,
            beforeEnter: (to, from, next) => {
                if (!_numericRegx.test(to.params.id)) {
                    next({name: 'notfound'})
                }
                next()
            }
        },

        {
            path: '/blogs',
            component: _myBlogListComponent,
            name: 'blogListRoute'
        },
        {
            path: '/blogs/add',
            component: _addEditBlogComponent,
            name: 'addBlogRoute'
        },


        {
            path: '/notes/:slug',
            component: _myNoteViewComponent,
            name: 'publicViewNoteRoute',
            props: true,
            beforeEnter: (to, from, next) => {
                if (!_alphNumericRegx.test(to.params.slug)) {
                    next({name: 'notfound'})
                }
                next()
            }
        },

        {
            path: '*',
            name: '404',
            component: _404Component
        },
        {
            path: '/404',
            name: 'notfound',
            component: _404Component
        }
    ]
});

webRoutes.beforeEach((to, from, next) => {
    //console.log(to.name + '--' + from.name);
    // if (to.matched.some(record => record.meta.requiredAuth)) {
    //     console.log(Vue.prototype.$isLoggedInUser);
    // }
    next();
});

export default webRoutes;