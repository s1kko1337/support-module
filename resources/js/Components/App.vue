
<template>
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 font-sans">
        <!-- Прелоадер при инициализации -->
        <div v-if="initializing" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
            <div class="text-center">
                <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-gray-600">Загрузка...</p>
            </div>
        </div>

        <!-- Навигационная панель -->
        <nav class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <div class="text-xl font-bold text-indigo-600">SupportApp</div>
                    <div class="space-x-6">
                        <!-- Кнопки для гостей -->
                        <template v-if="!authStore.isAuthenticated">
                            <router-link
                                :to="{name:'register'}"
                                class="px-4 py-2 rounded-lg text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition duration-200"
                            >Register</router-link>
                            <router-link
                                :to="{name:'login'}"
                                class="px-4 py-2 rounded-lg text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition duration-200"
                            >Login</router-link>
                        </template>

                        <!-- Кнопки для авторизованных пользователей -->
                        <template v-else>
                            <router-link
                                :to="{name:'get'}"
                                class="px-4 py-2 rounded-lg text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 transition duration-200"
                            >GET</router-link>
                            <button
                                @click="handleLogout"
                                class="px-4 py-2 rounded-lg text-gray-700 hover:text-red-600 hover:bg-red-50 transition duration-200"
                            >Logout</button>

                            <!-- Информация о пользователе -->
                            <span v-if="authStore.user" class="ml-4 px-3 py-2 bg-indigo-50 rounded-lg text-indigo-700">
                                {{ authStore.user.email || authStore.user.name }}
                            </span>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Основной контент -->
        <main class="container mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <router-view></router-view>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../Stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const initializing = ref(true);

// Обработчик выхода из системы
const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};

// Перенаправление на требуемую страницу в зависимости от аутентификации
const redirectIfNeeded = () => {
    const currentRoute = router.currentRoute.value;

    // Если пользователь авторизован и находится на странице входа или регистрации,
    // перенаправляем на страницу GET
    if (authStore.isAuthenticated &&
        (currentRoute.name === 'login' || currentRoute.name === 'register')) {
        router.push({ name: 'get' });
    }

    // Если пользователь не авторизован и пытается получить доступ к защищенным маршрутам
    if (!authStore.isAuthenticated && currentRoute.meta.requiresAuth) {
        router.push({ name: 'login' });
    }
};

// Инициализация при монтировании компонента
onMounted(async () => {
    try {
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

// Очищаем подписку при уничтожении компонента
onBeforeUnmount(() => {
    unsubscribe();
});
</script>
