/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const store = createStore({
    state: {
        authenticated:  false,
        admin: false,
        currentPage: 'dashboard',
    },
    mutations: {
        setAuthentication(state, status){
            state.authenticated = status;
        },
        setAdmin(state, admin){
            state.admin = !!admin;
        },
        changePage(state, page){
            state.currentPage = page;
        }
    }
})

const app = createApp({});

/*import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);*/

/*  Views */
import StartView from  './views/StartView.vue';
app.component('start-view', StartView);

import CommonView from  './views/CommonView.vue';
app.component('common-view', CommonView);

import LoginView from  './views/LoginView.vue';
app.component('login-view', LoginView);

/* Components */
/* Main */
import SideBar from  './components/SideBar.vue';
app.component('side-bar', SideBar);
/* Dashboard */
import Dashboard from './components/Dashboard.vue';
app.component('dashboard', Dashboard);
/* Users */
import Users from './components/Users.vue';
app.component('users', Users);
import UsersComponent from "./components/UsersComponent.vue";
app.component('users-component', UsersComponent);
/* Jobs */
import Jobs from './components/Jobs.vue';
app.component('jobs', Jobs);
import JobsComponent from "./components/JobsComponent.vue";
app.component('jobs-component', JobsComponent);
import AddJobComponent from "./components/AddJobComponent.vue";
app.component('add_job-component', AddJobComponent);

/* Authentication */
import Login from './components/Login.vue';
app.component('login', Login);

import Register from './components/Register.vue';
app.component('register', Register);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.use(store);
app.mount('#app');
