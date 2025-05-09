
import { createRouter, createWebHistory } from 'vue-router';
import Register from '../Components/Register.vue';
import Login from '../Components/Login.vue';
import App from '../Components/App.vue';
import Get from '../Components/Get.vue';
import TermsOfUse from '../Components/TermsOfUse.vue';
import EmailVerification from "../Components/EmailVerification.vue";
import EmailVerifyLink from "../Components/EmailVerifyLink.vue";
import { useAuthStore } from '../Stores/auth';
import ToDo from "../Components/ToDo.vue";
import PasswordResetRequest from "../Components/PasswordResetRequest.vue";
import PasswordChange from "../Components/PasswordChange.vue";
import PasswordResetForm from "../Components/PasswordResetForm.vue";

const routes = [
    // {
    //     path: '/',
    //     name: 'app',
    //     component: App,
    // },

    {
        path: '/',
        name: 'todo',
        component: ToDo,
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
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/termsofuse',
        name: 'termsofuse',
        component: TermsOfUse,
    },
    {
        path: '/email/verify',
        name: 'emailVerification',
        component: EmailVerification,
    },
    {
        path: '/email/verify/:id/:hash',
        name: 'emailVerifyLink',
        component: EmailVerifyLink,
    },
    {
        path: '/forgot-password',
        name: 'passwordRequest',
        component: PasswordResetRequest,
        meta: {
            auth: false
        }
    },
    {
        path: '/reset-password/:token',
        name: 'passwordReset',
        component: PasswordResetForm,
        meta: {
            auth: false
        }
    },

    {
        path: '/profile/password',
        name: 'passwordChange',
        component: PasswordChange,
        meta: {
            auth: true
        }
    }

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Добавляем навигационные хуки
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    if (!authStore.initialized) {
        await authStore.initialize();
    }
    if (to.name === 'emailVerifyLink') {
        next();
        return;
    }

    // Проверяем, требует ли маршрут аутентификации
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // Если пользователь не авторизован, перенаправляем на страницу входа
        if (!authStore.isAuthenticated) {
            next({ name: 'login' });
            return;
        }

        // Если маршрут требует верификации почты
        if (to.matched.some(record => record.meta.requiresVerification)) {
            try {
                // Проверяем статус верификации
                if (!authStore.isVerified) {
                    const verificationStatus = await authStore.checkVerificationStatus();

                    // Если email не подтвержден, перенаправляем на страницу верификации
                    if (!verificationStatus.verified) {
                        next({ name: 'emailVerification' });
                        return;
                    }
                }
            } catch (error) {
                console.error('Ошибка при проверке статуса верификации:', error);
                // При ошибке проверки перенаправляем на страницу верификации для безопасности
                next({ name: 'emailVerification' });
                return;
            }
        }
    }

    // Если пользователь авторизован и пытается перейти на страницу входа или регистрации
    if (authStore.isAuthenticated && (to.name === 'login' || to.name === 'register')) {
        next({ name: 'get' });
        return;
    }

    // Во всех остальных случаях разрешаем навигацию
    next();
});

export default router;
