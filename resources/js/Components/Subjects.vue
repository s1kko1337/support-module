
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
                    Предметы
                </h1>
                <p class="text-lg text-indigo-100 text-center max-w-3xl mx-auto">
                    Управление предметами и учебными материалами
                </p>
            </div>
        </header>

        <!-- Основной контент -->
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Панель инструментов -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 transform transition duration-300 hover:shadow-2xl">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600"></div>
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Список предметов</h2>
                        <div class="flex space-x-3">
                            <button @click="openCreateModal" class="btn-primary">
                                <Plus class="w-5 h-5 mr-2"/>
                                Добавить предмет
                            </button>
                            <button @click="fetchSubjects" class="btn-secondary">
                                <RefreshCw class="w-5 h-5 mr-2"/>
                                Обновить
                            </button>
                        </div>
                    </div>

                    <!-- Таблица предметов -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-xl overflow-hidden">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Часы практики</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Часы лекций</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата создания</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr v-if="loading">
                                <td colspan="5" class="px-6 py-4 text-center">
                                    <Loader2 class="animate-spin h-6 w-6 text-indigo-600 mx-auto" />
                                </td>
                            </tr>
                            <tr v-else-if="subjects.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Нет доступных предметов
                                </td>
                            </tr>
                            <tr v-for="subject in subjects" :key="subject.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ subject.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ subject.hours_of_practical }} ч.
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ subject.hours_of_lectures }} ч.
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ formatDate(subject.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="viewSubject(subject)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
                                        <Eye class="w-5 h-5" />
                                    </button>
                                    <button @click="editSubject(subject)" class="text-amber-600 hover:text-amber-900 p-1 rounded-full hover:bg-amber-50">
                                        <Edit class="w-5 h-5" />
                                    </button>
                                    <button @click="confirmDelete(subject)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
                                        <Trash2 class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагинация -->
                    <div v-if="pagination.last_page > 1" class="flex justify-center mt-6">
                        <nav class="flex items-center space-x-1">
                            <button
                                @click="changePage(1)"
                                :disabled="pagination.current_page === 1"
                                :class="[pagination.current_page === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                <ChevronsLeft class="w-5 h-5" />
                            </button>
                            <button
                                @click="changePage(pagination.current_page - 1)"
                                :disabled="pagination.current_page === 1"
                                :class="[pagination.current_page === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                <ChevronLeft class="w-5 h-5" />
                            </button>

                            <template v-for="page in displayedPages" :key="page">
                                <button
                                    @click="changePage(page)"
                                    :class="[pagination.current_page === page ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50', 'px-4 py-2 rounded-md text-sm font-medium']"
                                >
                                    {{ page }}
                                </button>
                            </template>

                            <button
                                @click="changePage(pagination.current_page + 1)"
                                :disabled="pagination.current_page === pagination.last_page"
                                :class="[pagination.current_page === pagination.last_page ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                <ChevronRight class="w-5 h-5" />
                            </button>
                            <button
                                @click="changePage(pagination.last_page)"
                                :disabled="pagination.current_page === pagination.last_page"
                                :class="[pagination.current_page === pagination.last_page ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                <ChevronsRight class="w-5 h-5" />
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для просмотра предмета -->
        <div v-if="viewModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-lg">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Просмотр предмета</h3>
                        <button @click="closeViewModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <span class="text-sm text-indigo-700 font-medium">Название</span>
                            <div class="text-lg font-semibold mt-1">{{ viewModal.subject.name }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <span class="text-sm text-green-700 font-medium">Часы практики</span>
                                <div class="text-2xl font-semibold mt-1">{{ viewModal.subject.hours_of_practical }}</div>
                            </div>

                            <div class="bg-blue-50 p-4 rounded-lg">
                                <span class="text-sm text-blue-700 font-medium">Часы лекций</span>
                                <div class="text-2xl font-semibold mt-1">{{ viewModal.subject.hours_of_lectures }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Дата создания</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.subject.created_at) }}</div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Последнее обновление</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.subject.updated_at) }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button @click="editSubject(viewModal.subject)" class="btn-primary mr-3">
                                <Edit class="w-5 h-5 mr-2"/>
                                Редактировать
                            </button>
                            <button @click="closeViewModal" class="btn-secondary">
                                Закрыть
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для создания/редактирования предмета -->
        <div v-if="formModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-lg">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ formModal.isEdit ? 'Редактирование предмета' : 'Добавление предмета' }}
                        </h3>
                        <button @click="closeFormModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <form @submit.prevent="saveSubject" class="space-y-4">
                        <!-- Название предмета -->
                        <div>
                            <label for="subject-name" class="block text-sm font-medium text-gray-700 mb-1">Название предмета*</label>
                            <input
                                id="subject-name"
                                v-model="formModal.form.name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите название предмета"
                                required
                            >
                            <span v-if="formErrors.name" class="text-sm text-red-600">{{ formErrors.name[0] }}</span>
                        </div>

                        <!-- Часы практики -->
                        <div>
                            <label for="hours-practical" class="block text-sm font-medium text-gray-700 mb-1">Часы практики*</label>
                            <input
                                id="hours-practical"
                                v-model="formModal.form.hours_of_practical"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Количество часов"
                                required
                            >
                            <span v-if="formErrors.hours_of_practical" class="text-sm text-red-600">{{ formErrors.hours_of_practical[0] }}</span>
                        </div>

                        <!-- Часы лекций -->
                        <div>
                            <label for="hours-lectures" class="block text-sm font-medium text-gray-700 mb-1">Часы лекций*</label>
                            <input
                                id="hours-lectures"
                                v-model="formModal.form.hours_of_lectures"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Количество часов"
                                required
                            >
                            <span v-if="formErrors.hours_of_lectures" class="text-sm text-red-600">{{ formErrors.hours_of_lectures[0] }}</span>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button
                                type="button"
                                @click="closeFormModal"
                                class="btn-secondary mr-3"
                            >
                                Отмена
                            </button>
                            <button
                                type="submit"
                                class="btn-primary"
                                :disabled="formModal.loading"
                            >
                                <Loader2 v-if="formModal.loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"/>
                                {{ formModal.isEdit ? 'Обновить' : 'Создать' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Модальное окно для подтверждения удаления -->
        <div v-if="deleteModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-md">
                <div class="h-2 bg-gradient-to-r from-red-400 to-red-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex flex-col items-center mb-6 text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                            <AlertTriangle class="h-8 w-8 text-red-600" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Подтвердите удаление</h3>
                        <p class="text-gray-600">
                            Вы уверены, что хотите удалить предмет "{{ deleteModal.subject.name }}"?
                            Это действие нельзя отменить.
                        </p>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button
                            @click="closeDeleteModal"
                            class="btn-secondary"
                        >
                            Отмена
                        </button>
                        <button
                            @click="deleteSubject"
                            class="btn-danger"
                            :disabled="deleteModal.loading"
                        >
                            <Loader2 v-if="deleteModal.loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"/>
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast уведомления -->
        <div v-if="toast.show"
             class="fixed bottom-4 right-4 max-w-md bg-white rounded-lg shadow-lg overflow-hidden animate-slideIn"
             :class="{
                'border-l-4 border-green-500': toast.type === 'success',
                'border-l-4 border-red-500': toast.type === 'error',
                'border-l-4 border-blue-500': toast.type === 'info',
            }"
        >
            <div class="p-4 flex items-start">
                <div class="flex-shrink-0 mr-3">
                    <CheckCircle v-if="toast.type === 'success'" class="h-6 w-6 text-green-500" />
                    <XCircle v-else-if="toast.type === 'error'" class="h-6 w-6 text-red-500" />
                    <Info v-else-if="toast.type === 'info'" class="h-6 w-6 text-blue-500" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ toast.title }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ toast.message }}</p>
                </div>
                <button @click="hideToast" class="ml-4 text-gray-400 hover:text-gray-500">
                    <X class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import {
    Plus,
    RefreshCw,
    Search,
    Eye,
    Edit,
    Trash2,
    X,
    AlertTriangle,
    Loader2,
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
    CheckCircle,
    XCircle,
    Info
} from 'lucide-vue-next';

// Состояние данных
const subjects = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const searchTimeout = ref(null);
const formErrors = ref({});

// Состояние пагинации
const pagination = reactive({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
});

// Вычисляемые страницы для пагинации
const displayedPages = computed(() => {
    const pages = [];
    const maxDisplayed = 5;
    const halfMax = Math.floor(maxDisplayed / 2);

    let start = Math.max(1, pagination.current_page - halfMax);
    let end = Math.min(pagination.last_page, start + maxDisplayed - 1);

    if (end - start + 1 < maxDisplayed) {
        start = Math.max(1, end - maxDisplayed + 1);
    }

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return pages;
});

// Модальное окно для просмотра предмета
const viewModal = reactive({
    show: false,
    subject: {}
});

// Модальное окно для формы создания/редактирования
const formModal = reactive({
    show: false,
    isEdit: false,
    loading: false,
    form: {
        name: '',
        hours_of_practical: 0,
        hours_of_lectures: 0
    },
    subjectId: null
});

// Модальное окно подтверждения удаления
const deleteModal = reactive({
    show: false,
    subject: {},
    loading: false
});

// Toast уведомления
const toast = reactive({
    show: false,
    type: 'info', // success, error, info
    title: '',
    message: '',
    timeout: null
});

// Загрузка списка предметов
const fetchSubjects = async (page = 1) => {
    loading.value = true;

    try {
        const response = await axios.get('/api/v1/subjects', {
            params: {
                page,
                search: searchQuery.value
            }
        });

        subjects.value = response.data.data;

        // Обновление информации о пагинации
        pagination.current_page = response.data.meta.current_page;
        pagination.last_page = response.data.meta.last_page;
        pagination.per_page = response.data.meta.per_page;
        pagination.total = response.data.meta.total;

    } catch (error) {
        showToast('error', 'Ошибка загрузки', 'Не удалось загрузить список предметов.');
        console.error('Error fetching subjects:', error);
    } finally {
        loading.value = false;
    }
};

// Изменение страницы пагинации
const changePage = (page) => {
    if (page < 1 || page > pagination.last_page) return;
    pagination.current_page = page;
    fetchSubjects(page);
};

// Форматирование даты
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

// Открытие модального окна для просмотра предмета
const viewSubject = async (subject) => {
    try {
        // Получаем полные данные о предмете
        const response = await axios.get(`/api/v1/subjects/${subject.id}`);
        viewModal.subject = response.data;
        viewModal.show = true;
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить данные предмета.');
        console.error('Error fetching subject details:', error);
    }
};

// Закрытие модального окна просмотра
const closeViewModal = () => {
    viewModal.show = false;
    viewModal.subject = {};
};

// Открытие модального окна для создания предмета
const openCreateModal = () => {
    formModal.isEdit = false;
    formModal.form = {
        name: '',
        hours_of_practical: 0,
        hours_of_lectures: 0
    };
    formModal.subjectId = null;
    formErrors.value = {};
    formModal.show = true;
};

// Открытие модального окна для редактирования предмета
const editSubject = (subject) => {
    formModal.isEdit = true;
    formModal.form = {
        name: subject.name,
        hours_of_practical: subject.hours_of_practical,
        hours_of_lectures: subject.hours_of_lectures
    };
    formModal.subjectId = subject.id;
    formErrors.value = {};
    formModal.show = true;

    // Если открыто модальное окно просмотра, закрываем его
    if (viewModal.show) {
        closeViewModal();
    }
};

// Закрытие модального окна формы
const closeFormModal = () => {
    formModal.show = false;
    formErrors.value = {};
};

// Сохранение предмета (создание или обновление)
const saveSubject = async () => {
    formModal.loading = true;
    formErrors.value = {};

    try {
        let response;

        if (formModal.isEdit) {
            // Обновление предмета
            response = await axios.put(`/api/v1/subjects/${formModal.subjectId}`, formModal.form);
            showToast('success', 'Предмет обновлен', 'Информация о предмете успешно обновлена.');
        } else {
            // Создание нового предмета
            response = await axios.post('/api/v1/subjects', formModal.form);
            showToast('success', 'Предмет создан', 'Новый предмет успешно добавлен.');
        }

        // Обновляем список предметов
        await fetchSubjects(pagination.current_page);
        closeFormModal();

    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            formErrors.value = error.response.data.errors;
        } else {
            showToast('error', 'Ошибка', 'Произошла ошибка при сохранении предмета.');
        }
        console.error('Error saving subject:', error);
    } finally {
        formModal.loading = false;
    }
};

// Открытие модального окна подтверждения удаления
const confirmDelete = (subject) => {
    deleteModal.subject = subject;
    deleteModal.show = true;
};

// Закрытие модального окна подтверждения удаления
const closeDeleteModal = () => {
    deleteModal.show = false;
    deleteModal.subject = {};
};

// Удаление предмета
const deleteSubject = async () => {
    deleteModal.loading = true;

    try {
        await axios.delete(`/api/v1/subjects/${deleteModal.subject.id}`);
        showToast('success', 'Предмет удален', 'Предмет успешно удален из системы.');

        // Обновляем список предметов
        await fetchSubjects(pagination.current_page);
        closeDeleteModal();

    } catch (error) {
        showToast('error', 'Ошибка удаления', 'Не удалось удалить предмет.');
        console.error('Error deleting subject:', error);
    } finally {
        deleteModal.loading = false;
    }
};

// Поиск предметов с задержкой
const handleSearch = () => {
    clearTimeout(searchTimeout.value);

    searchTimeout.value = setTimeout(() => {
        pagination.current_page = 1; // Сбрасываем на первую страницу при поиске
        fetchSubjects(1);
    }, 500);
};

// Показать toast-уведомление
const showToast = (type, title, message, duration = 5000) => {
    // Очищаем предыдущий timeout, если есть
    if (toast.timeout) clearTimeout(toast.timeout);

    toast.type = type;
    toast.title = title;
    toast.message = message;
    toast.show = true;

    // Автоматически скрываем уведомление через указанное время
    toast.timeout = setTimeout(() => {
        hideToast();
    }, duration);
};

// Скрыть toast-уведомление
const hideToast = () => {
    toast.show = false;
    toast.timeout = null;
};

// Загрузка данных при монтировании компонента
onMounted(() => {
    fetchSubjects();
});
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

/* Модальные окна */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
    padding: 1rem;
    overflow-y: auto;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content {
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
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

.btn-danger {
    background-color: #ef4444;
    color: white;
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

.btn-danger:hover {
    background-color: #dc2626;
    box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.2), 0 4px 6px -2px rgba(239, 68, 68, 0.1);
}

/* Анимации */
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}

.animate-scaleIn {
    animation: scaleIn 0.3s ease-out;
}

.animate-slideIn {
    animation: slideIn 0.3s ease-out;
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

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
