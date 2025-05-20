
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
                    Мероприятия
                </h1>
                <p class="text-lg text-indigo-100 text-center max-w-3xl mx-auto">
                    Управление мероприятиями и событиями
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
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Список мероприятий</h2>
                        <div class="flex space-x-3">
                            <button @click="openCreateModal" class="btn-primary">
                                <Plus class="w-5 h-5 mr-2"/>
                                Добавить мероприятие
                            </button>
                            <button @click="fetchEvents" class="btn-secondary">
                                <RefreshCw class="w-5 h-5 mr-2"/>
                                Обновить
                            </button>
                        </div>
                    </div>

                    <!-- Таблица мероприятий -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-xl overflow-hidden">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Группа</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата проведения</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr v-if="loading">
                                <td colspan="5" class="px-6 py-4 text-center">
                                    <Loader2 class="animate-spin h-6 w-6 text-indigo-600 mx-auto" />
                                </td>
                            </tr>
                            <tr v-else-if="events.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Нет доступных мероприятий
                                </td>
                            </tr>
                            <tr v-for="event in events" :key="event.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ event.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        event.type === 'учебное' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800',
                                        'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full'
                                    ]">
                                        {{ event.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ getGroupName(event.group_id) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ formatDate(event.date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="viewEvent(event)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
                                        <Eye class="w-5 h-5" />
                                    </button>
                                    <button @click="editEvent(event)" class="text-amber-600 hover:text-amber-900 p-1 rounded-full hover:bg-amber-50">
                                        <Edit class="w-5 h-5" />
                                    </button>
                                    <button @click="confirmDelete(event)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
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

        <!-- Модальное окно для просмотра мероприятия -->
        <div v-if="viewModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-lg">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Просмотр мероприятия</h3>
                        <button @click="closeViewModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <span class="text-sm text-indigo-700 font-medium">Название</span>
                            <div class="text-lg font-semibold mt-1">{{ viewModal.event.name }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div :class="[
                                viewModal.event.type === 'учебное' ? 'bg-blue-50' : 'bg-purple-50',
                                'p-4 rounded-lg'
                            ]">
                                <span :class="[
                                    viewModal.event.type === 'учебное' ? 'text-blue-700' : 'text-purple-700',
                                    'text-sm font-medium'
                                ]">Тип</span>
                                <div class="text-lg font-semibold mt-1">{{ viewModal.event.type }}</div>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg">
                                <span class="text-sm text-green-700 font-medium">Группа</span>
                                <div class="text-lg font-semibold mt-1">{{ getGroupName(viewModal.event.group_id) }}</div>
                            </div>
                        </div>

                        <div class="bg-amber-50 p-4 rounded-lg">
                            <span class="text-sm text-amber-700 font-medium">Результат</span>
                            <div class="text-lg font-semibold mt-1">{{ viewModal.event.result }}</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg col-span-1">
                                <span class="text-sm text-gray-500 font-medium">Дата мероприятия</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.event.date) }}</div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded-lg col-span-1">
                                <span class="text-sm text-gray-500 font-medium">Дата создания</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.event.created_at) }}</div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded-lg col-span-1">
                                <span class="text-sm text-gray-500 font-medium">Последнее обновление</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.event.updated_at) }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button @click="editEvent(viewModal.event)" class="btn-primary mr-3">
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

        <!-- Модальное окно для создания/редактирования мероприятия -->
        <div v-if="formModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-lg">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ formModal.isEdit ? 'Редактирование мероприятия' : 'Добавление мероприятия' }}
                        </h3>
                        <button @click="closeFormModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <form @submit.prevent="saveEvent" class="space-y-4">
                        <!-- Выбор группы -->
                        <div>
                            <label for="group-select" class="block text-sm font-medium text-gray-700 mb-1">Группа*</label>
                            <select
                                id="group-select"
                                v-model="formModal.form.group_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                                <option value="" disabled>Выберите группу</option>
                                <option v-for="group in availableGroups" :key="group.id" :value="group.id.toString()">
                                    {{ group.name || 'Группа ' + group.id }}
                                </option>
                            </select>
                            <span v-if="formErrors.group_id" class="text-sm text-red-600">{{ formErrors.group_id[0] }}</span>
                        </div>

                        <!-- Название мероприятия -->
                        <div>
                            <label for="event-name" class="block text-sm font-medium text-gray-700 mb-1">Название мероприятия*</label>
                            <input
                                id="event-name"
                                v-model="formModal.form.name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите название мероприятия"
                                required
                            >
                            <span v-if="formErrors.name" class="text-sm text-red-600">{{ formErrors.name[0] }}</span>
                        </div>

                        <!-- Тип мероприятия -->
                        <div>
                            <label for="event-type" class="block text-sm font-medium text-gray-700 mb-1">Тип мероприятия*</label>
                            <select
                                id="event-type"
                                v-model="formModal.form.type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                                <option value="" disabled>Выберите тип</option>
                                <option value="учебное">Учебное</option>
                                <option value="внеучебное">Внеучебное</option>
                            </select>
                            <span v-if="formErrors.type" class="text-sm text-red-600">{{ formErrors.type[0] }}</span>
                        </div>

                        <!-- Результат мероприятия -->
                        <div>
                            <label for="event-result" class="block text-sm font-medium text-gray-700 mb-1">Результат*</label>
                            <textarea
                                id="event-result"
                                v-model="formModal.form.result"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите результат мероприятия"
                                required
                            ></textarea>
                            <span v-if="formErrors.result" class="text-sm text-red-600">{{ formErrors.result[0] }}</span>
                        </div>

                        <!-- Дата мероприятия -->
                        <div>
                            <label for="event-date" class="block text-sm font-medium text-gray-700 mb-1">Дата проведения*</label>
                            <input
                                id="event-date"
                                v-model="formModal.form.date"
                                type="datetime-local"
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
                            Вы уверены, что хотите удалить мероприятие "{{ deleteModal.event.name }}"?
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
                            @click="deleteEvent"
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
const events = ref([]);
const availableGroups = ref([]);
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

// Модальное окно для просмотра мероприятия
const viewModal = reactive({
    show: false,
    event: {}
});

// Модальное окно для формы создания/редактирования
const formModal = reactive({
    show: false,
    isEdit: false,
    loading: false,
    form: {
        group_id: '',
        name: '',
        type: '',
        result: '',
        date: ''
    },
    eventId: null
});

// Модальное окно подтверждения удаления
const deleteModal = reactive({
    show: false,
    event: {},
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

// Загрузка списка мероприятий
const fetchEvents = async (page = 1) => {
    loading.value = true;

    try {
        const response = await axios.get('/api/v1/events', {
            params: {
                page
            }
        });

        events.value = response.data.data;

        // Обновление информации о пагинации
        pagination.current_page = response.data.meta?.current_page || 1;
        pagination.last_page = response.data.meta?.last_page || 1;
        pagination.per_page = response.data.meta?.per_page || 15;
        pagination.total = response.data.meta?.total || 0;

    } catch (error) {
        showToast('error', 'Ошибка загрузки', 'Не удалось загрузить список мероприятий.');
        console.error('Error fetching events:', error);
    } finally {
        loading.value = false;
    }
};

// Загрузка списка доступных групп
const fetchGroups = async () => {
    try {
        const response = await axios.get('/api/v1/groups');
        availableGroups.value = response.data.data;
    } catch (error) {
        showToast('error', 'Ошибка загрузки', 'Не удалось загрузить список групп.');
        console.error('Error fetching groups:', error);
    }
};

// Получение названия группы по ID
const getGroupName = (groupId) => {
    if (!groupId) return 'Неизвестная группа';

    const group = availableGroups.value.find(g => g.id == groupId);
    return group ? (group.name || `Группа ${group.id}`) : `Группа ${groupId}`;
};

// Изменение страницы пагинации
const changePage = (page) => {
    if (page < 1 || page > pagination.last_page) return;
    pagination.current_page = page;
    fetchEvents(page);
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

// Преобразование даты из формата ISO в формат для input type="datetime-local"
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    // Преобразуем ISO формат в локальный формат для input datetime-local
    const date = new Date(dateString);
    // Формат YYYY-MM-DDThh:mm
    return new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
        .toISOString()
        .slice(0, 16);
};

// Преобразование локального формата даты обратно в ISO для отправки на сервер
const formatDateForServer = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString();
};

// Открытие модального окна для просмотра мероприятия
const viewEvent = async (event) => {
    try {
        // Получаем полные данные о мероприятии
        const response = await axios.get(`/api/v1/events/${event.id}`);

        // Обработка ответа API - данные находятся в поле 'data'
        viewModal.event = response.data.data || response.data;
        viewModal.show = true;
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить данные мероприятия.');
        console.error('Error fetching event details:', error);
    }
};

// Закрытие модального окна просмотра
const closeViewModal = () => {
    viewModal.show = false;
    viewModal.event = {};
};

// Открытие модального окна для создания мероприятия
const openCreateModal = async () => {
    formModal.isEdit = false;
    formModal.form = {
        group_id: '',
        name: '',
        type: 'учебное', // По умолчанию учебное
        result: '',
        date: formatDateForInput(new Date().toISOString()) // Текущая дата и время
    };
    formModal.eventId = null;
    formErrors.value = {};

    // Загружаем список групп, если еще не загружен
    if (availableGroups.value.length === 0) {
        await fetchGroups();
    }

    formModal.show = true;
};

// Открытие модального окна для редактирования мероприятия
const editEvent = async (event) => {
    formModal.isEdit = true;
    formModal.eventId = event.id;
    formErrors.value = {};

    try {
        // Получаем полные данные о мероприятии для редактирования
        const response = await axios.get(`/api/v1/events/${event.id}`);
        const eventData = response.data.data || response.data;

        // Заполняем форму данными мероприятия
        formModal.form = {
            group_id: eventData.group_id.toString(),
            name: eventData.name,
            type: eventData.type,
            result: eventData.result,
            date: formatDateForInput(eventData.date)
        };

        // Загружаем список групп, если еще не загружен
        if (availableGroups.value.length === 0) {
            await fetchGroups();
        }

        formModal.show = true;

        // Если открыто окно просмотра, закрываем его
        if (viewModal.show) {
            closeViewModal();
        }
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить данные мероприятия для редактирования.');
        console.error('Error fetching event for edit:', error);
    }
};

// Закрытие модального окна формы
const closeFormModal = () => {
    formModal.show = false;
    formModal.isEdit = false;
    formModal.loading = false;
    formModal.form = {
        group_id: '',
        name: '',
        type: '',
        result: '',
        date: ''
    };
    formModal.eventId = null;
    formErrors.value = {};
};

// Сохранение мероприятия (создание или обновление)
const saveEvent = async () => {
    formModal.loading = true;
    formErrors.value = {};

    try {
        const eventData = {
            ...formModal.form,
            date: formatDateForServer(formModal.form.date)
        };

        let response;

        if (formModal.isEdit) {
            // Обновляем существующее мероприятие
            response = await axios.put(`/api/v1/events/${formModal.eventId}`, eventData);
            showToast('success', 'Успешно', 'Мероприятие успешно обновлено.');
        } else {
            // Создаем новое мероприятие
            response = await axios.post('/api/v1/events', eventData);
            showToast('success', 'Успешно', 'Мероприятие успешно создано.');
        }

        // Закрываем модальное окно и обновляем список мероприятий
        closeFormModal();
        fetchEvents(pagination.current_page);
    } catch (error) {
        console.error('Error saving event:', error);

        if (error.response && error.response.data && error.response.data.errors) {
            // Если сервер вернул ошибки валидации
            formErrors.value = error.response.data.errors;
            showToast('error', 'Ошибка валидации', 'Пожалуйста, проверьте правильность заполнения формы.');
        } else {
            showToast('error', 'Ошибка', 'Не удалось сохранить мероприятие.');
        }
    } finally {
        formModal.loading = false;
    }
};

// Открытие модального окна подтверждения удаления
const confirmDelete = (event) => {
    deleteModal.event = event;
    deleteModal.show = true;
};

// Закрытие модального окна подтверждения удаления
const closeDeleteModal = () => {
    deleteModal.show = false;
    deleteModal.loading = false;
    deleteModal.event = {};
};

// Удаление мероприятия
const deleteEvent = async () => {
    deleteModal.loading = true;

    try {
        await axios.delete(`/api/v1/events/${deleteModal.event.id}`);

        // Закрываем модальное окно и обновляем список мероприятий
        closeDeleteModal();

        // Если текущая страница стала пустой из-за удаления последнего элемента,
        // переходим на предыдущую страницу, кроме случая когда это первая страница
        if (events.value.length === 1 && pagination.current_page > 1) {
            changePage(pagination.current_page - 1);
        } else {
            fetchEvents(pagination.current_page);
        }

        showToast('success', 'Успешно', 'Мероприятие успешно удалено.');
    } catch (error) {
        console.error('Error deleting event:', error);
        showToast('error', 'Ошибка', 'Не удалось удалить мероприятие.');
    } finally {
        deleteModal.loading = false;
    }
};

// Показать toast уведомление
const showToast = (type, title, message, duration = 5000) => {
    // Если уже есть активное уведомление, скрываем его
    if (toast.show) {
        clearTimeout(toast.timeout);
    }

    toast.type = type;
    toast.title = title;
    toast.message = message;
    toast.show = true;

    // Автоматическое скрытие через указанное время
    toast.timeout = setTimeout(() => {
        hideToast();
    }, duration);
};

// Скрыть toast уведомление
const hideToast = () => {
    toast.show = false;
    if (toast.timeout) {
        clearTimeout(toast.timeout);
        toast.timeout = null;
    }
};

// Инициализация при монтировании компонента
onMounted(async () => {
    try {
        // Загрузка списка групп и мероприятий
        await fetchGroups();
        await fetchEvents();
    } catch (error) {
        console.error('Error during component initialization:', error);
    }
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
