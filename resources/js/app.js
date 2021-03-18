/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;
Vue.config.productionTip = true;

/* ============================================================================ */
// import toaster
import CxltToastr from 'cxlt-vue2-toastr'
import 'cxlt-vue2-toastr/dist/css/cxlt-vue2-toastr.css'

var toastrConfigs = {
    position: 'top right',
    showDuration: 1000,
    timeOut: 5000,
    closeButton: true,
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut',
    //progressBar: true
}
Vue.use(CxltToastr, toastrConfigs)
// end toaster

// sweet-alert 2
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
const options = {
    confirmButtonColor: '#0a71b9',
    cancelButtonColor: '#cc1f00',
};
Vue.use(VueSweetalert2, options);
// end sweet-alert 2

// vuelidate validations
import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)
// end vuelidate validations

// global constant define
Vue.prototype.$defaultPagination = 10;
Vue.prototype.$visibilityList = [
    {
        'id' : 0,
        'name' : 'Public'
    },
    {
        'id' : 1,
        'name' : 'Private'
    }
];

Vue.prototype.$statusList = [
    {
        'id' : 0,
        'name' : 'Inactive'
    },
    {
        'id' : 1,
        'name' : 'Active'
    }
];

Vue.prototype.$taskStatusList = [
    {
        'id' : 0,
        'name' : 'Initialize'
    },
    {
        'id' : 1,
        'name' : 'Completed'
    },
    {
        'id' : 2,
        'name' : 'In-Progress'
    },
    {
        'id' : 3,
        'name' : 'On-Hold'
    },
    {
        'id' : 4,
        'name' : 'Stoped'
    }
];

Vue.prototype.$noteStatusList = [
    {
        'id' : 0,
        'name' : 'In-Progress'
    },
    {
        'id' : 1,
        'name' : 'Published'
    }
];
// end global constant define

import VueCookies from 'vue-cookies'
Vue.use(VueCookies)
Vue.$cookies.config('30d')

/* ============================================================================ */

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.prototype.$isLoggedInUser = false;

// import routes
import webRoutes from './routes/route';

Vue.component('pagination', require('laravel-vue-pagination'));

// components
Vue.component('header-navmenu', require('./components/headerNavMenuComponent.vue').default);
Vue.component('myoption-navmenu', require('./components/sidebarMyOptionComponent.vue').default);
Vue.component('page-loader', require('./components/pageLoadingComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
 Vue.prototype.$excludeAuthCheckRoute = [
    'loginRoute',
    'registrationRoute'
]

Vue.prototype.$outPageRoute = [
    'rootRoute'
]

Vue.prototype.$isFullPageLayout = false;

const app = new Vue({
    data() {
        return {
            isPageLoadingActive: true,
            sidebarCount: {
                postBoxCount: 0,
                categoryCount: 0,
                keywordCount: 0,
                taskCount: 0,
                noteCount: 0,
                blogCount: 0
            },
            isUserLoggedIn: false,
            appUserInfo: []
        }
    },
    components: {
    },
    methods: {
        async getCounts() {
            var _this = this;
            axios.get('/dashboard/counts').then(response => {
                _this.sidebarCount.categoryCount = response.data.content.categoryCount;
                _this.sidebarCount.postBoxCount = response.data.content.postBoxCount;
                _this.sidebarCount.keywordCount = response.data.content.keywordCount;
                _this.sidebarCount.taskCount = response.data.content.tasksCount;
                _this.sidebarCount.noteCount = response.data.content.notesCount;
            }).catch(function (error) {
                _this.$toast.error({
                    title:'System Error',
                    message:'Something went wrong!'
                });
            });
        },
        async checkAuth() {
            var _this = this;
            axios.post('/auth/checkAuth').then(response => {
                _this.isPageLoadingActive = false;
                if (response.data.status == 200) {
                    _this.isUserLoggedIn = true;
                    _this.appUserInfo = response.data.content.user;
                    Vue.prototype.$isLoggedInUser = true;
                    if (Vue.prototype.$excludeAuthCheckRoute.includes(_this.$route.name)) {
                        _this.$router.push({name: 'dashboardRoute'})
                    }
                }
            }).catch(function (error) {
                _this.isPageLoadingActive = false;
                if (error.response.status === 401) {
                    if (!Vue.prototype.$excludeAuthCheckRoute.includes(_this.$route.name) && !Vue.prototype.$outPageRoute.includes(_this.$route.name)) {
                        _this.$toast.error({
                            title:'Unauthorized Access',
                            message:'Something went wrong!'
                        });
                        _this.$router.push({path: '/'})
                    }
                } else {
                    _this.$toast.error({
                        title:'System Error',
                        message:'Something went wrong!'
                    });
                }
                
            });
        }
    },
    mounted() {
        this.getCounts();
        this.checkAuth();
        if (Vue.prototype.$outPageRoute.includes(this.$route.name)) {
            Vue.prototype.$isFullPageLayout = true;
        }
    },
    el: '#app',
    router: webRoutes
});
