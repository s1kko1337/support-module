import { createRouter, createWebHistory } from 'vue-router';
import { onMounted } from 'vue';

import App from '../Components/App.vue';
import { useRouterStore } from "../stores/routerStore.js";



const routes = [
    {
        path: '/',
        name: 'Main',
        component: App,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const routerStore = useRouterStore();
    if (to.path === '/') {
        routerStore.setLastVisitedRoute('/');
    } else {
        routerStore.setLastVisitedRoute(to.path);
    }
    next();
});

export default router;
