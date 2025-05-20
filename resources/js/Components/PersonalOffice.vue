<template>
    <div class="bg-gradient-to-br from-indigo-50 via-white to-blue-50 min-h-screen pb-12">
        <!-- Хедер с анимированным фоном -->
        <header class="relative py-12 mb-8 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-600 opacity-90"></div>
            <div class="absolute inset-0">
                <div class="wave-pattern"></div>
            </div>
            <div class="container mx-auto px-6 relative z-10">
                <h1 class="text-3xl md:text-4xl font-bold text-white text-center mb-3">
                    Личный кабинет
                </h1>
                <p class="text-lg text-indigo-100 text-center max-w-3xl mx-auto">
                    Управляйте вашими данными и настройками в одном месте
                </p>
            </div>
        </header>

        <!-- Основной контент -->
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Секция профиля пользователя -->
            <div
                class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 transform transition duration-300 hover:shadow-2xl">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600"></div>
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="flex items-center mb-4 md:mb-0">
                            <div
                                class="w-20 h-20 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xl font-bold border-4 border-white shadow-lg">
                                {{ userInitials }}
                            </div>
                            <div class="ml-6">
                                <h2 class="text-2xl font-bold text-gray-800">{{ userName }}</h2>
                                <p class="text-gray-600">{{ userEmail }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <router-link :to="{name: 'passwordChange'}" class="btn-secondary">
                                <Key class="w-5 h-5 mr-2"/>
                                Сменить пароль
                            </router-link>
                            <button @click="editProfile" class="btn-primary">
                                <Edit class="w-5 h-5 mr-2"/>
                                Редактировать профиль
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Панель управления модулями -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Карточка модуля: Аттестации -->
                <router-link :to="{name:'certifications'}" href="#" class="module-card"
                             :class="{ 'active': activeModule === 'certifications' }">
                    <div class="icon-wrapper bg-blue-100">
                        <ShieldCheck class="w-8 h-8 text-blue-600"/>
                    </div>
                    <div class="module-content">
                        <h3 class="module-title">Аттестации</h3>
                        <p class="module-description">Управление аттестациями</p>
                    </div>
                    <div class="module-arrow">
                        <ChevronRight class="w-5 h-5"/>
                    </div>
                </router-link>

                <!-- Карточка модуля: События -->
                <router-link :to="{name:'events'}" href="#" class="module-card"
                             :class="{ 'active': activeModule === 'events' }">
                    <div class="icon-wrapper bg-green-100">
                        <Calendar class="w-8 h-8 text-green-600"/>
                    </div>
                    <div class="module-content">
                        <h3 class="module-title">События</h3>
                        <p class="module-description">Просмотр и управление расписанием событий и мероприятий</p>
                    </div>
                    <div class="module-arrow">
                        <ChevronRight class="w-5 h-5"/>
                    </div>
                </router-link>

                <!-- Карточка модуля: Группы -->
                <router-link :to="{name:'groups'}" href="#" class="module-card"
                             :class="{ 'active': activeModule === 'groups' }">
                    <div class="icon-wrapper bg-purple-100">
                        <Users class="w-8 h-8 text-purple-600"/>
                    </div>
                    <div class="module-content">
                        <h3 class="module-title">Группы</h3>
                        <p class="module-description">Управление учебными группами и их участниками</p>
                    </div>
                    <div class="module-arrow">
                        <ChevronRight class="w-5 h-5"/>
                    </div>
                </router-link>

                <!-- Карточка модуля: Студенты -->
                <router-link :to="{name:'students'}" href="#" class="module-card"
                             :class="{ 'active': activeModule === 'students' }">
                    <div class="icon-wrapper bg-amber-100">
                        <GraduationCap class="w-8 h-8 text-amber-600"/>
                    </div>
                    <div class="module-content">
                        <h3 class="module-title">Студенты</h3>
                        <p class="module-description">Управление данными студентов и их успеваемостью</p>
                    </div>
                    <div class="module-arrow">
                        <ChevronRight class="w-5 h-5"/>
                    </div>
                </router-link>

                <!-- Карточка модуля: Аттестация студентов -->
                <router-link :to="{name:'studentsCertifications'}" href="#" class="module-card"
                             :class="{ 'active': activeModule === 'studentCertifications' }">
                    <div class="icon-wrapper bg-red-100">
                        <FileText class="w-8 h-8 text-red-600"/>
                    </div>
                    <div class="module-content">
                        <h3 class="module-title">Аттестация студентов</h3>
                        <p class="module-description">Отслеживание аттестации студентов</p>
                    </div>
                    <div class="module-arrow">
                        <ChevronRight class="w-5 h-5"/>
                    </div>
                </router-link>

                <!-- Карточка модуля: Характеристики студентов -->
                <router-link :to="{name:'studentCharacteristics'}" href="#" class="module-card"
                             :class="{ 'active': activeModule === 'studentCharacteristics' }">
                    <div class="icon-wrapper bg-teal-100">
                        <ClipboardList class="w-8 h-8 text-teal-600"/>
                    </div>
                    <div class="module-content">
                        <h3 class="module-title">Характеристики студентов</h3>
                        <p class="module-description">Управление информацией о характеристиках студентов</p>
                    </div>
                    <div class="module-arrow">
                        <ChevronRight class="w-5 h-5"/>
                    </div>
                </router-link>

                <!-- Карточка модуля: Предметы -->

                <router-link :to="{name:'subjects'}" href="#" class="module-card"
                             :class="{ 'active': activeModule === 'subjects' }">
                    <div class="icon-wrapper bg-indigo-100">
                        <BookOpen class="w-8 h-8 text-indigo-600"/>
                    </div>
                    <div class="module-content">
                        <h3 class="module-title">Предметы</h3>
                        <p class="module-description">Управление предметами и учебными материалами</p>
                    </div>
                    <div class="module-arrow">
                        <ChevronRight class="w-5 h-5"/>
                    </div>
                </router-link>

                <!-- Карточка модуля: Отчеты -->
                <router-link :to="{name:''}" href="#" class="module-card"
                             :class="{ 'active': activeModule === 'reports' }" @click="navigateTo('reports')">
                    <div class="icon-wrapper bg-pink-100">
                        <BarChart2 class="w-8 h-8 text-pink-600"/>
                    </div>
                    <div class="module-content">
                        <h3 class="module-title">Отчеты</h3>
                        <p class="module-description">Генерация и просмотр отчетов по группам и студентам</p>
                    </div>
                    <div class="module-arrow">
                        <ChevronRight class="w-5 h-5"/>
                    </div>
                </router-link>


                <!-- Диалог редактирования профиля -->
                <div v-if="showProfileEditDialog"
                     class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-auto animate-fadeIn">
                    <div
                        class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-auto transform transition-all duration-300 animate-scaleIn">
                        <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-bold text-gray-800">Редактирование профиля</h3>
                                <button @click="showProfileEditDialog = false"
                                        class="text-gray-500 hover:text-gray-700 transition-colors">
                                    <X class="w-6 h-6"/>
                                </button>
                            </div>

                            <form @submit.prevent="updateProfile" class="space-y-4">
                                <div>
                                    <label for="edit-name"
                                           class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                                    <input
                                        id="edit-name"
                                        type="text"
                                        v-model="editForm.name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Ваше имя"
                                    >
                                </div>

                                <div>
                                    <label for="edit-surname"
                                           class="block text-sm font-medium text-gray-700 mb-1">Фамилия</label>
                                    <input
                                        id="edit-surname"
                                        type="text"
                                        v-model="editForm.surname"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Ваша фамилия"
                                    >
                                </div>

                                <div>
                                    <label for="edit-email"
                                           class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input
                                        id="edit-email"
                                        type="email"
                                        v-model="editForm.email"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Ваш email"
                                        readonly
                                    >
                                    <p class="mt-1 text-sm text-gray-500">Email нельзя изменить</p>
                                </div>

                                <div class="flex justify-end pt-4">
                                    <button
                                        type="button"
                                        @click="showProfileEditDialog = false"
                                        class="btn-secondary mr-3"
                                    >
                                        Отмена
                                    </button>
                                    <button
                                        type="submit"
                                        class="btn-primary"
                                        :disabled="profileUpdateLoading"
                                    >
                                        <Loader2 v-if="profileUpdateLoading"
                                                 class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"/>
                                        Сохранить
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue';
import {useRouter} from 'vue-router';
import axios from 'axios';
import {useAuthStore} from '../Stores/auth.js';

// Импорт Lucide-vue иконок
import {
    Key,
    Edit,
    ShieldCheck,
    Calendar,
    Users,
    GraduationCap,
    FileText,
    ClipboardList,
    BookOpen,
    BarChart2,
    PenTool,
    ChevronRight,
    X,
    Loader2
} from 'lucide-vue-next';

const router = useRouter();
const authStore = useAuthStore();

// Данные пользователя
const userName = ref('');
const userEmail = ref('');
const activeModule = ref('');
const showProfileEditDialog = ref(false);
const profileUpdateLoading = ref(false);

// Форма редактирования профиля
const editForm = ref({
    name: '',
    surname: '',
    email: ''
});

// Вычисляемое свойство для инициалов пользователя
const userInitials = computed(() => {
    const nameParts = userName.value.split(' ');
    if (nameParts.length >= 2) {
        return (nameParts[0][0] + nameParts[1][0]).toUpperCase();
    } else if (nameParts.length === 1 && nameParts[0]) {
        return nameParts[0][0].toUpperCase();
    }
    return 'ПК'; // По умолчанию "Личный Кабинет"
});

// Загрузка данных пользователя при монтировании компонента
onMounted(async () => {
    if (authStore.isAuthenticated) {
        try {
            const response = await axios.get('/api/v1/user');
            if (response.data) {
                const user = response.data;
                userName.value = `${user.name} ${user.surname}`;
                userEmail.value = user.email;

                // Инициализация формы редактирования
                editForm.value = {
                    name: user.name || '',
                    surname: user.surname || '',
                    email: user.email || ''
                };
            }
        } catch (error) {
            console.error('Ошибка при загрузке данных пользователя:', error);
        }
    }
});

const editProfile = () => {
    showProfileEditDialog.value = true;
};

const updateProfile = async () => {
    profileUpdateLoading.value = true;

    try {
        const response = await axios.post('/api/v1/user/update', {
            name: editForm.value.name,
            surname: editForm.value.surname
        });

        if (response.data) {
            // Обновляем отображаемое имя пользователя
            userName.value = `${editForm.value.name} ${editForm.value.surname}`;
            showProfileEditDialog.value = false;

            // Можно добавить уведомление об успешном обновлении
        }
    } catch (error) {
        console.error('Ошибка при обновлении профиля:', error);
    } finally {
        profileUpdateLoading.value = false;
    }
}
</script>
<style scoped>
/* Анимированный фон с волнами */
.wave-pattern {
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%),
    radial-gradient(circle at 70% 70%, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
    background-size: 200% 200%, 180% 180%;
    animation: wave 15s linear infinite;
}

@keyframes wave {
    0% {
        background-position: 0% 0%, 0% 0%;
    }
    50% {
        background-position: 100% 100%, 70% 70%;
    }
    100% {
        background-position: 0% 0%, 0% 0%;
    }
}

/* Модульные карточки */
.module-card {
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    padding: 1rem;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition-property: all;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
}

.module-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: translateY(-0.25rem);
}

.module-card.active {
    outline: 2px solid #6366f1;
    outline-offset: -2px;
}

.icon-wrapper {
    padding: 0.75rem;
    border-radius: 0.5rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.module-content {
    flex: 1 1 0%;
}

.module-title {
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.module-description {
    font-size: 0.875rem;
    color: #6b7280;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.module-arrow {
    margin-left: 0.5rem;
    color: #9ca3af;
    transform: translateX(0);
    transition-property: transform;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.module-card:hover .module-arrow {
    transform: translateX(0.25rem);
    color: #6366f1;
}

/* Кнопки */
.btn-primary {
    background-image: linear-gradient(to right, #4f46e5, #9333ea);
    color: white;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    padding-left: 1rem;
    padding-right: 1rem;
    border-radius: 0.5rem;
    transition-property: all;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transform: scale(1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-primary:hover {
    background-image: linear-gradient(to right, #4338ca, #7e22ce);
    transform: scale(1.02);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.btn-secondary {
    background-color: #f3f4f6;
    color: #1f2937;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    padding-left: 1rem;
    padding-right: 1rem;
    border-radius: 0.5rem;
    transition-property: all;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-secondary:hover {
    background-color: #e5e7eb;
}

/* Анимации */
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}

.animate-scaleIn {
    animation: scaleIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
