
import { createRouter, createWebHistory } from 'vue-router';
import Register from '../Components/Auth/Register.vue';
import Login from '../Components/Auth/Login.vue';
import TermsOfUse from '../Components/TermsOfUse.vue';
import EmailVerification from "../Components/Mail/EmailVerification.vue";
import EmailVerifyLink from "../Components/Mail/EmailVerifyLink.vue";
import { useAuthStore } from '../Stores/auth';
import PersonalOffice from "../Components/PersonalOffice.vue";
import PasswordResetRequest from "../Components/Auth/PasswordResetRequest.vue";
import PasswordChange from "../Components/Auth/PasswordChange.vue";
import PasswordResetForm from "../Components/Auth/PasswordResetForm.vue";
import Subjects from "../Components/Subjects.vue";
import Certifications from "../Components/Certifications.vue";
import Groups from "../Components/Groups.vue";
import Events from "../Components/Events.vue";
import Students from "../Components/Students.vue";
import StudentsCertifications from "../Components/StudentsCertifications.vue";
import StudentCharacteristics from "../Components/StudentCharacteristics.vue";
import Reports from "../Components/Reports.vue";


const routes = [
    {
        path: '/',
        name: 'personalOffice',
        component: PersonalOffice,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/subjects',
        name: 'subjects',
        component: Subjects,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/certifications',
        name: 'certifications',
        component: Certifications,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/groups',
        name: 'groups',
        component: Groups,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/events',
        name: 'events',
        component: Events,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/students',
        name: 'students',
        component: Students,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/reports',
        name: 'reports',
        component: Reports,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/studentsCertifications',
        name: 'studentsCertifications',
        component: StudentsCertifications,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
    },
    {
        path: '/studentCharacteristics',
        name: 'studentCharacteristics',
        component: StudentCharacteristics,
        meta: {
            requiresAuth: true,
            requiresVerification: true
        }
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
        next({ name: 'personalOffice' });
        return;
    }

    // Во всех остальных случаях разрешаем навигацию
    next();
});

export default router;
