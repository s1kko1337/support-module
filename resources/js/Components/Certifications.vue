
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
                    Аттестации
                </h1>
                <p class="text-lg text-indigo-100 text-center max-w-3xl mx-auto">
                    Управление аттестациями по предметам
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
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Список аттестаций</h2>
                        <div class="flex space-x-3">
                            <button @click="openCreateModal" class="btn-primary">
                                <Plus class="w-5 h-5 mr-2"/>
                                Добавить аттестацию
                            </button>
                            <button @click="fetchCertifications" class="btn-secondary">
                                <RefreshCw class="w-5 h-5 mr-2"/>
                                Обновить
                            </button>
                        </div>
                    </div>

                    <!-- Таблица аттестаций -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-xl overflow-hidden">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Предмет</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата аттестации</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата создания</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr v-if="loading">
                                <td colspan="4" class="px-6 py-4 text-center">
                                    <Loader2 class="animate-spin h-6 w-6 text-indigo-600 mx-auto" />
                                </td>
                            </tr>
                            <tr v-else-if="certifications.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    Нет доступных аттестаций
                                </td>
                            </tr>
                            <tr v-for="certification in certifications" :key="certification.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ certification.subject_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ formatDateOnly(certification.date) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ formatDate(certification.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="viewCertification(certification)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
                                        <Eye class="w-5 h-5" />
                                    </button>
                                    <button @click="editCertification(certification)" class="text-amber-600 hover:text-amber-900 p-1 rounded-full hover:bg-amber-50">
                                        <Edit class="w-5 h-5" />
                                    </button>
                                    <button @click="confirmDelete(certification)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
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

        <!-- Модальное окно для просмотра аттестации -->
        <div v-if="viewModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-lg">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Просмотр аттестации</h3>
                        <button @click="closeViewModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <span class="text-sm text-indigo-700 font-medium">Предмет</span>
                            <div class="text-lg font-semibold mt-1">{{ viewModal.certification.subject_name }}</div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <span class="text-sm text-blue-700 font-medium">Дата аттестации</span>
                            <div class="text-lg font-semibold mt-1">{{ formatDateOnly(viewModal.certification.date) }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Дата создания</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.certification.created_at) }}</div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Последнее обновление</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.certification.updated_at) }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button @click="editCertification(viewModal.certification)" class="btn-primary mr-3">
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

        <!-- Модальное окно для создания/редактирования аттестации -->
        <div v-if="formModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-lg">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ formModal.isEdit ? 'Редактирование аттестации' : 'Добавление аттестации' }}
                        </h3>
                        <button @click="closeFormModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <form @submit.prevent="saveCertification" class="space-y-4">
                        <!-- Выбор предмета -->
                        <div>
                            <label for="subject-select" class="block text-sm font-medium text-gray-700 mb-1">Предмет*</label>
                            <select
                                id="subject-select"
                                v-model="formModal.form.subject_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                                <option value="" disabled>Выберите предмет</option>
                                <option v-for="subject in availableSubjects" :key="subject.id" :value="subject.id">
                                    {{ subject.name }}
                                </option>
                            </select>
                            <span v-if="formErrors.subject_id" class="text-sm text-red-600">{{ formErrors.subject_id[0] }}</span>
                        </div>

                        <!-- Дата аттестации -->
                        <div>
                            <label for="certification-date" class="block text-sm font-medium text-gray-700 mb-1">Дата аттестации*</label>
                            <input
                                id="certification-date"
                                v-model="formModal.form.date"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                            <span v-if="formErrors.date" class="text-sm text-red-600">{{ formErrors.date[0] }}</span>
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
                            Вы уверены, что хотите удалить аттестацию по предмету "{{ deleteModal.certification.subject_name }}"?
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
                            @click="deleteCertification"
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
const certifications = ref([]);
const availableSubjects = ref([]);
const loading = ref(false);
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

// Модальное окно для просмотра аттестации
const viewModal = reactive({
    show: false,
    certification: {}
});

// Модальное окно для формы создания/редактирования
const formModal = reactive({
    show: false,
    isEdit: false,
    loading: false,
    form: {
        subject_id: '',
        date: ''
    },
    certificationId: null
});

// Модальное окно подтверждения удаления
const deleteModal = reactive({
    show: false,
    certification: {},
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

// Загрузка списка аттестаций
const fetchCertifications = async (page = 1) => {
    loading.value = true;

    try {
        const response = await axios.get('/api/v1/certifications', {
            params: {
                page
            }
        });

        certifications.value = response.data.data;

        // Обновление информации о пагинации
        pagination.current_page = response.data.links.current_page || 1;
        pagination.last_page = response.data.links.last_page || 1;
        pagination.per_page = 15;
        pagination.total = (pagination.last_page * pagination.per_page);

    } catch (error) {
        showToast('error', 'Ошибка загрузки', 'Не удалось загрузить список аттестаций.');
        console.error('Error fetching certifications:', error);
    } finally {
        loading.value = false;
    }
};

// Загрузка списка доступных предметов
const fetchSubjects = async () => {
    try {
        const response = await axios.get('/api/v1/subjects');
        availableSubjects.value = response.data.data;
    } catch (error) {
        showToast('error', 'Ошибка загрузки', 'Не удалось загрузить список предметов.');
        console.error('Error fetching subjects:', error);
    }
};

// Изменение страницы пагинации
const changePage = (page) => {
    if (page < 1 || page > pagination.last_page) return;
    pagination.current_page = page;
    fetchCertifications(page);
};

// Форматирование даты и времени
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

// Форматирование только даты
const formatDateOnly = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
};

// Преобразование даты из формата YYYY-MM-DD в объект Date
const parseDate = (dateString) => {
    if (!dateString) return new Date();
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day);
};

// Преобразование даты обратно в формат YYYY-MM-DD для input type="date"
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Открытие модального окна для просмотра аттестации
const viewCertification = async (certification) => {
    try {
        // Получаем полные данные об аттестации
        const response = await axios.get(`/api/v1/certifications/${certification.id}`);

        // Обработка ответа API - данные находятся в поле 'data'
        viewModal.certification = response.data.data || response.data;
        viewModal.show = true;
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить данные аттестации.');
        console.error('Error fetching certification details:', error);
    }
};

// Закрытие модального окна просмотра
const closeViewModal = () => {
    viewModal.show = false;
    viewModal.certification = {};
};

// Открытие модального окна для создания аттестации
const openCreateModal = async () => {
    formModal.isEdit = false;
    formModal.form = {
        subject_id: '',
        date: new Date().toISOString().split('T')[0] // Текущая дата как значение по умолчанию
    };
    formModal.certificationId = null;
    formErrors.value = {};

    // Загружаем список предметов, если еще не загружен
    if (availableSubjects.value.length === 0) {
        await fetchSubjects();
    }

    formModal.show = true;
};

// Открытие модального окна для редактирования аттестации
const editCertification = async (certification) => {
    // Загружаем список предметов, если еще не загружен
    if (availableSubjects.value.length === 0) {
        await fetchSubjects();
    }

    try {
        // Если были переданы данные из view модального окна, используем их,
        // иначе загружаем данные с сервера для получения полной информации
        let certData = certification;

        if (!certification.subject_id) {
            const response = await axios.get(`/api/v1/certifications/${certification.id}`);
            certData = response.data.data || response.data;
        }

        formModal.isEdit = true;
        formModal.form = {
            subject_id: certData.subject_id.toString(),
            date: formatDateForInput(certData.date) // Форматируем дату для поля ввода
        };
        formModal.certificationId = certData.id;
        formErrors.value = {};
        formModal.show = true;

        // Если открыто модальное окно просмотра, закрываем его
        if (viewModal.show) {
            closeViewModal();
        }
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить данные аттестации для редактирования.');
        console.error('Error preparing certification for edit:', error);
    }
};

// Закрытие модального окна формы
const closeFormModal = () => {
    formModal.show = false;
    formErrors.value = {};
};

// Сохранение аттестации (создание или обновление)
const saveCertification = async () => {
    formModal.loading = true;
    formErrors.value = {};

    try {
        let response;

        if (formModal.isEdit) {
            // Обновление аттестации
            response = await axios.put(`/api/v1/certifications/${formModal.certificationId}`, formModal.form);
            showToast('success', 'Аттестация обновлена', 'Информация об аттестации успешно обновлена.');
        } else {
            // Создание новой аттестации
            response = await axios.post('/api/v1/certifications', formModal.form);
            showToast('success', 'Аттестация создана', 'Новая аттестация успешно добавлена.');
        }

        // Обновляем список аттестаций
        await fetchCertifications(pagination.current_page);
        closeFormModal();

    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            formErrors.value = error.response.data.errors;
        } else {
            showToast('error', 'Ошибка', 'Произошла ошибка при сохранении аттестации.');
        }
        console.error('Error saving certification:', error);
    } finally {
        formModal.loading = false;
    }
};

// Открытие модального окна подтверждения удаления
const confirmDelete = (certification) => {
    deleteModal.certification = certification;
    deleteModal.show = true;
};

// Закрытие модального окна подтверждения удаления
const closeDeleteModal = () => {
    deleteModal.show = false;
    deleteModal.certification = {};
};

// Удаление аттестации
const deleteCertification = async () => {
    deleteModal.loading = true;

    try {
        await axios.delete(`/api/v1/certifications/${deleteModal.certification.id}`);
        showToast('success', 'Аттестация удалена', 'Аттестация успешно удалена из системы.');

        // Обновляем список аттестаций
        await fetchCertifications(pagination.current_page);
        closeDeleteModal();

    } catch (error) {
        showToast('error', 'Ошибка удаления', 'Не удалось удалить аттестацию.');
        console.error('Error deleting certification:', error);
    } finally {
        deleteModal.loading = false;
    }
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
    fetchCertifications();
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
