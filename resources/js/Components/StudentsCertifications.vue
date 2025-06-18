
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
                    Аттестация студентов
                </h1>
                <p class="text-lg text-indigo-100 text-center max-w-3xl mx-auto">
                    Управление аттестацией студентов
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
                            <div class="flex items-center ml-3">
                                <span class="text-sm text-gray-600 mr-2">Группировать по группам:</span>
                                <label class="switch">
                                    <input type="checkbox" v-model="groupByGroups">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div v-if="loading" class="py-6 text-center">
                        <Loader2 class="animate-spin h-8 w-8 text-indigo-600 mx-auto" />
                    </div>
                    <div v-else-if="certifications.length === 0" class="py-6 text-center text-gray-500">
                        Нет доступных аттестаций
                    </div>
                    <div v-else>
                        <!-- Стандартный режим (без группировки) -->
                        <div v-if="!groupByGroups" class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-xl overflow-hidden">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название аттестации</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ФИО студента</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата аттестации</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                <tr v-for="certification in certifications" :key="`${certification.student_id}-${certification.certification_id}`" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ certification.certification_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ certification.student_fio }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[certification.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full']">
                                            {{ certification.passed ? 'Пройдено' : 'Не пройдено' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ formatDate(certification.date) }}
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

                        <!-- Режим с группировкой -->
                        <div v-else class="space-y-8">
                            <div v-for="(group, groupId) in groupedCertifications" :key="groupId" class="bg-white rounded-xl shadow overflow-hidden">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 flex justify-between items-center">
                                    <h3 class="text-lg font-medium text-white">{{ group.name }}</h3>
                                    <span class="bg-white text-green-700 font-semibold px-2 py-1 rounded-full text-sm">
                                        {{ group.certifications.length }} аттестаций
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название аттестации</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ФИО студента</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата аттестации</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                        <tr v-for="certification in group.certifications" :key="`${certification.student_id}-${certification.certification_id}`" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ certification.certification_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ certification.student_fio }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="[certification.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full']">
                                                    {{ certification.passed ? 'Пройдено' : 'Не пройдено' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                                {{ formatDate(certification.date) }}
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
                            </div>
                        </div>
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
                        <h3 class="text-xl font-bold text-gray-800">Просмотр данных аттестации</h3>
                        <button @click="closeViewModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <span class="text-sm text-indigo-700 font-medium">Название аттестации</span>
                            <div class="text-lg font-semibold mt-1">
                                {{ viewModal.certification.certification_name }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <span class="text-sm text-green-700 font-medium">ФИО студента</span>
                                <div class="text-lg font-semibold mt-1">{{ viewModal.certification.student_fio }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-amber-50 p-4 rounded-lg">
                                <span class="text-sm text-amber-700 font-medium">Статус аттестации</span>
                                <div class="text-lg font-semibold mt-1">
                                    <span :class="[viewModal.certification.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full']">
                                        {{ viewModal.certification.passed ? 'Пройдено' : 'Не пройдено' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Дата аттестации</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.certification.date) }}</div>
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
                        <!-- Выбор студента -->
                        <div>
                            <label for="student-select" class="block text-sm font-medium text-gray-700 mb-1">Студент*</label>
                            <select
                                id="student-select"
                                v-model="formModal.form.student_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                                <option value="" disabled>Выберите студента</option>
                                <option v-for="student in availableStudents" :key="student.id" :value="student.id">
                                    {{ `${student.surname} ${student.name} ${student.patronymic}` }}
                                </option>
                            </select>
                            <span v-if="formErrors.student_id" class="text-sm text-red-600">{{ formErrors.student_id[0] }}</span>
                        </div>

                        <!-- Выбор аттестации -->
                        <div>
                            <label for="certification-select" class="block text-sm font-medium text-gray-700 mb-1">Аттестация*</label>
                            <select
                                id="certification-select"
                                v-model="formModal.form.certification_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                                <option value="" disabled>Выберите аттестацию</option>
                                <option v-for="cert in availableCertifications" :key="cert.id" :value="cert.id">
                                    {{ cert.subject_name }}
                                </option>
                            </select>
                            <span v-if="formErrors.certification_id" class="text-sm text-red-600">{{ formErrors.certification_id[0] }}</span>
                        </div>

                        <!-- Статус аттестации -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Статус аттестации*</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="formModal.form.passed"
                                        :value="true"
                                        class="form-radio text-indigo-600 h-5 w-5"
                                    >
                                    <span class="ml-2 text-gray-700">Пройдено</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="formModal.form.passed"
                                        :value="false"
                                        class="form-radio text-red-600 h-5 w-5"
                                    >
                                    <span class="ml-2 text-gray-700">Не пройдено</span>
                                </label>
                            </div>
                            <span v-if="formErrors.passed" class="text-sm text-red-600">{{ formErrors.passed[0] }}</span>
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
                            Вы уверены, что хотите удалить аттестацию "{{ deleteModal.certification.certification_name }}"
                            для студента "{{ deleteModal.certification.student_fio }}"?
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
const availableStudents = ref([]);
const availableCertifications = ref([]);
const availableGroups = ref([]);
const loading = ref(false);
const formErrors = ref({});
const groupByGroups = ref(false);

// Группировка аттестаций по группам
const groupedCertifications = computed(() => {
    if (!groupByGroups.value) {
        return { ungrouped: certifications.value };
    }

    return certifications.value.reduce((groups, certification) => {
        // Находим студента, чтобы получить его группу
        const student = availableStudents.value.find(s => s.id === certification.student_id);
        const groupId = student?.group_id || 'unknown';
        const groupName = availableGroups.value.find(g => g.id === groupId)?.name || `Группа ${groupId}`;

        if (!groups[groupId]) {
            groups[groupId] = {
                id: groupId,
                name: groupName,
                certifications: []
            };
        }

        groups[groupId].certifications.push(certification);
        return groups;
    }, {});
});

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
        student_id: '',
        certification_id: '',
        passed: true
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
        const response = await axios.get('/api/v1/studentCertifications', {
            params: {
                page
            }
        });

        certifications.value = response.data.data;

        // Обновление информации о пагинации
        pagination.current_page = response.data.meta.current_page;
        pagination.last_page = response.data.meta.last_page;
        pagination.per_page = response.data.meta.per_page;
        pagination.total = response.data.meta.total;
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить данные аттестаций');
        console.error('Error fetching certifications:', error);
    } finally {
        loading.value = false;
    }
};

// Загрузка справочных данных (студенты, аттестации, группы)
const fetchReferenceData = async () => {
    try {
        // Загрузка списка доступных студентов
        const studentsResponse = await axios.get('/api/v1/students');
        availableStudents.value = studentsResponse.data.data;

        // Загрузка списка доступных аттестаций
        const certificationsResponse = await axios.get('/api/v1/certifications');
        availableCertifications.value = certificationsResponse.data.data;

        // Загрузка списка групп
        const groupsResponse = await axios.get('/api/v1/groups');
        availableGroups.value = groupsResponse.data.data;
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить справочные данные');
        console.error('Error fetching reference data:', error);
    }
};

// Форматирование даты
const formatDate = (dateString) => {
    if (!dateString) return 'Н/Д';

    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

// Изменение страницы пагинации
const changePage = (page) => {
    if (page < 1 || page > pagination.last_page) return;
    fetchCertifications(page);
};

// Открытие модального окна для создания аттестации
const openCreateModal = () => {
    formModal.isEdit = false;
    formModal.form = {
        student_id: '',
        certification_id: '',
        passed: true
    };
    formModal.certificationId = null;
    formErrors.value = {};
    formModal.show = true;
};

// Просмотр аттестации
const viewCertification = (certification) => {
    viewModal.certification = { ...certification };
    viewModal.show = true;
};

// Закрытие модального окна просмотра
const closeViewModal = () => {
    viewModal.show = false;
    setTimeout(() => {
        viewModal.certification = {};
    }, 300);
};

// Редактирование аттестации
const editCertification = (certification) => {
    formModal.isEdit = true;
    formModal.form = {
        student_id: certification.student_id,
        certification_id: certification.certification_id,
        passed: certification.passed
    };
    formModal.certificationId = certification.id;
    formErrors.value = {};
    formModal.show = true;

    // Если окно просмотра открыто, закрываем его
    if (viewModal.show) {
        closeViewModal();
    }
};

// Закрытие модального окна формы
const closeFormModal = () => {
    formModal.show = false;
    setTimeout(() => {
        formModal.form = {
            student_id: '',
            certification_id: '',
            passed: true
        };
        formModal.certificationId = null;
        formErrors.value = {};
    }, 300);
};

// Сохранение аттестации
const saveCertification = async () => {
    formModal.loading = true;
    formErrors.value = {};

    try {
        let response;

        if (formModal.isEdit) {
            // Обновление существующей аттестации
            response = await axios.put(`/api/v1/studentCertifications/${formModal.certificationId}`, formModal.form);
            showToast('success', 'Успешно', 'Аттестация успешно обновлена');
        } else {
            // Создание новой аттестации
            response = await axios.post('/api/v1/studentCertifications', formModal.form);
            showToast('success', 'Успешно', 'Аттестация успешно создана');
        }

        closeFormModal();
        fetchCertifications(pagination.current_page);
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            formErrors.value = error.response.data.errors;
            showToast('error', 'Ошибка валидации', 'Пожалуйста, проверьте правильность заполнения формы');
        } else {
            showToast('error', 'Ошибка', 'Не удалось сохранить аттестацию');
            console.error('Error saving certification:', error);
        }
    } finally {
        formModal.loading = false;
    }
};

// Подтверждение удаления аттестации
const confirmDelete = (certification) => {
    deleteModal.certification = { ...certification };
    deleteModal.show = true;
};

// Закрытие модального окна подтверждения удаления
const closeDeleteModal = () => {
    deleteModal.show = false;
    setTimeout(() => {
        deleteModal.certification = {};
    }, 300);
};

// Удаление аттестации
const deleteCertification = async () => {
    deleteModal.loading = true;

    try {
        await axios.delete(`/api/v1/studentCertifications/${deleteModal.certification.id}`);

        showToast('success', 'Успешно', 'Аттестация успешно удалена');
        closeDeleteModal();
        fetchCertifications(pagination.current_page);
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось удалить аттестацию');
        console.error('Error deleting certification:', error);
    } finally {
        deleteModal.loading = false;
    }
};

// Показать toast уведомление
const showToast = (type, title, message) => {
    // Если уже есть активный toast, очищаем его таймер
    if (toast.timeout) {
        clearTimeout(toast.timeout);
    }

    toast.type = type;
    toast.title = title;
    toast.message = message;
    toast.show = true;

    // Устанавливаем таймер для скрытия toast через 4 секунды
    toast.timeout = setTimeout(() => {
        hideToast();
    }, 4000);
};

// Скрыть toast уведомление
const hideToast = () => {
    toast.show = false;

    if (toast.timeout) {
        clearTimeout(toast.timeout);
        toast.timeout = null;
    }
};

// При монтировании компонента
onMounted(async () => {
    // Загрузка справочных данных
    await fetchReferenceData();

    // Загрузка списка аттестаций
    await fetchCertifications();
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

/* Добавить в секцию стилей */
/* Переключатель (Toggle Switch) */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: #6366f1;
}

input:focus + .slider {
    box-shadow: 0 0 1px #6366f1;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.slider.round {
    border-radius: 24px;
}

.slider.round:before {
    border-radius: 50%;
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
