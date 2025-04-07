<template>
    <div class="w-full min-h-screen bg-primary flex flex-col">
            <div class="max-w-2xl text-center space-y-8 p-6 bg-amber-300">
                        <div class="px-8 py-3 mt-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-lg">
                            Тут будет подобие админка
                        </div>
                </div>
            </div>
</template>


<script>
import {computed, onMounted, ref, watch} from 'vue';
import {useRouterStore} from "../stores/routerStore.js";
import {useRouter} from "vue-router";
export default {

    setup() {
        const routerStore = useRouterStore();
        const router = useRouter();


        onMounted(async () => {
            const lastVisitedRoute = routerStore.lastVisitedRoute;
            await new Promise(resolve => setTimeout(resolve, 1));

            const isResetPasswordRoute = router.currentRoute.value.name === 'ResetPassword' && router.currentRoute.value.params.token;
            if (isResetPasswordRoute) {
                return;
            }

            try {
                const currentRoutePath = router.currentRoute.value.path;
                const authRoutes = ['/', '/login'];
                const shouldRedirect = authRoutes.includes(currentRoutePath) && lastVisitedRoute;

                if (shouldRedirect) {
                    const routeExists = router.getRoutes().some(route => route.path === lastVisitedRoute);

                    if (routeExists) {
                        await router.push(lastVisitedRoute);
                    }
                }
            } catch (error) {
                if (error.response) {
                } else {
                }
            }
        });

        const currentRouter = computed(() => router.currentRoute.value.path);

        return {
            currentRouter,
        }
    }
}
</script>
