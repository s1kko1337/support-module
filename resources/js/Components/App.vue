<template>
    <div class="flex flex-col min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 font-sans">
        <!-- Прелоадер при инициализации -->
        <div v-if="initializing" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
            <div class="text-center">
                <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-gray-600">Загрузка...</p>
            </div>
        </div>

        <!-- Навигационная панель -->
        <nav class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <router-link
                        :to="{name:'todo'}"
                        class="px-4 py-2 rounded-lg text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition duration-200"
                    >
                    <span class="text-xl font-bold text-indigo-600">SupportApp</span>
                    </router-link>
                    <div class="space-x-6">
                        <!-- Кнопки для гостей -->
                        <template v-if="!authStore.isAuthenticated">
                            <router-link
                                :to="{name:'register'}"
                                class="px-4 py-2 rounded-lg text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition duration-200"
                            >Регистрация
                            </router-link>
                            <router-link
                                :to="{name:'login'}"
                                class="px-4 py-2 rounded-lg text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition duration-200"
                            >Войти
                            </router-link>
                        </template>

                        <!-- Кнопки для авторизованных пользователей -->
                        <template v-else>
                            <router-link
                                :to="{name:'dashboard'}"
                                class="px-4 py-2 rounded-lg text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition duration-200"
                            >Панель управления
                            </router-link>
                            <button
                                @click="handleLogout"
                                class="px-4 py-2 rounded-lg text-gray-700 hover:text-red-600 hover:bg-red-50 transition duration-200"
                            >Выход
                            </button>

                            <!-- Информация о пользователе -->
                            <span v-if="authStore.user" class="ml-4 px-3 py-2 bg-indigo-50 rounded-lg text-indigo-700">
                                <router-link
                                    :to="{name:'passwordChange'}"
                                    class="px-4 py-2 rounded-lg text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition duration-200"
                                >{{ authStore.user.email || authStore.user.name }}
                            </router-link>
                            </span>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Основной контент - с flex-grow для заполнения доступного пространства -->
        <main class="flex-grow container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg p-6 h-full">
                <router-view></router-view>
            </div>
        </main>

        <!-- Футер - останется внизу из-за flex-структуры -->
        <footer class="bg-gray-900 text-white py-12 mt-auto">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-6 md:mb-0">
                        <h3 class="text-2xl font-bold mb-2">SupportApp</h3>
                        <p class="text-gray-400">© 2025. Все права защищены.</p>
                    </div>
                    <div class="flex flex-wrap justify-center gap-4">
                        <router-link :to="{name:'termsofuse'}" href="#" class="hover:text-white transition-colors duration-300">Условия пользования</router-link>
                        <a href="https://github.com/s1kko1337/support-module" class="hover:text-white transition-colors duration-300">Github</a>
                    </div>

                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import {ref, onMounted, onBeforeUnmount, nextTick} from 'vue';
import {useRouter} from 'vue-router';
import {useAuthStore} from '../Stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const initializing = ref(true);
const isScrolled = ref(false);

// Обработчик прокрутки страницы
const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

// Обработчик выхода из системы
const handleLogout = async () => {
    await authStore.logout();
    router.push({name: 'login'});
};

// Перенаправление на требуемую страницу в зависимости от аутентификации
const redirectIfNeeded = async () => {
    const currentRoute = router.currentRoute.value;

    // Если пользователь авторизован и находится на странице входа или регистрации,
    // перенаправляем на страницу GET
    if (authStore.isAuthenticated &&
        (currentRoute.name === 'login' || currentRoute.name === 'register')) {
        router.push({name: 'get'});
    }

    // Если пользователь не авторизован и пытается получить доступ к защищенным маршрутам
    if (!authStore.isAuthenticated && currentRoute.meta?.requiresAuth) {
        router.push({name: 'login'});
    }

    // Если маршрут требует верификации email, проверяем статус
    if (authStore.isAuthenticated && currentRoute.meta?.requiresVerification) {
        try {
            const verificationStatus = await authStore.checkVerificationStatus();
            if (verificationStatus && !verificationStatus.verified) {
                // Если email не подтвержден, перенаправляем на страницу верификации
                router.push({name: 'emailVerification'});
            }
        } catch (error) {
            console.error('Error checking verification status:', error);
        }
    }
};

// Инициализация при монтировании компонента
onMounted(async () => {
    try {
        // Добавляем слушатель события прокрутки
        window.addEventListener('scroll', handleScroll);

        // Инициализируем auth store и восстанавливаем сессию
        await authStore.initialize();
        await nextTick();
        // Перенаправляем если нужно
        redirectIfNeeded();
    } catch (error) {
        console.error('App initialization error:', error);
    } finally {
        initializing.value = false;
    }
});

// Следим за изменениями состояния аутентификации
const unsubscribe = authStore.$subscribe((mutation, state) => {
    // При изменении статуса аутентификации проверяем, нужно ли перенаправить
    if (mutation.events.key === 'token' || mutation.events.key === 'user') {
        redirectIfNeeded();
    }
});

// Очищаем подписки при уничтожении компонента
onBeforeUnmount(() => {
    unsubscribe();
    window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
/* Базовые стили для обеспечения корректной работы с высотой экрана */
:root {
    --app-height: 100%;
}

/* Обеспечиваем плавное изменение тени при скролле */
.transition-shadow {
    transition: box-shadow 0.3s ease;
}

/* Стили для улучшения прокрутки (опционально) */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #818cf8;
}
</style>
