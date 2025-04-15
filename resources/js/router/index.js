import {createRouter, createWebHashHistory} from 'vue-router';

import Register from '../Components/Register.vue';
import Login from '../Components/Login.vue';
import App from '../Components/App.vue';
import Get from '../Components/Get.vue';

const routes = [
    {
        path: '/',
        name: 'app',
        component: App,
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
    },
    {
        path: '/get',
        name: 'get',
        component: Get,
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;
