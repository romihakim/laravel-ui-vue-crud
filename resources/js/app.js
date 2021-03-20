/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import VueRouter from 'vue-router';

window.Vue = require('vue').default;
window.Vue.use(VueRouter);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('editor', require('@tinymce/tinymce-vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 const router = new VueRouter({
    mode: 'history',
    linkExactActiveClass: 'active',
    routes: [
        { name: 'categoryIndex', path: '/category', component: require('./categories/Index.vue').default },
        { name: 'categoryCreate', path: '/category/create', component: require('./categories/Create.vue').default },
        { name: 'categoryEdit', path: '/category/edit', component: require('./categories/Edit.vue').default },
        { name: 'categoryShow', path: '/category/show', component: require('./categories/Show.vue').default },
        { name: 'postIndex', path: '/post', component: require('./posts/Index.vue').default },
        { name: 'postCreate', path: '/post/create', component: require('./posts/Create.vue').default },
        { name: 'postEdit', path: '/post/edit', component: require('./posts/Edit.vue').default },
        { name: 'postShow', path: '/post/show', component: require('./posts/Show.vue').default }
    ]
});

const app = new Vue({
    el: '#app',
    router,
});
