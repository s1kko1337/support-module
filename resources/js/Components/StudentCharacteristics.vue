
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
                    Характеристики студентов
                </h1>
                <p class="text-lg text-indigo-100 text-center max-w-3xl mx-auto">
                    Управление характеристиками студентов
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
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Список характеристик</h2>
                        <div class="flex space-x-3">
                            <button @click="openCreateModal" class="btn-primary">
                                <Plus class="w-5 h-5 mr-2"/>
                                Добавить характеристику
                            </button>
                            <button @click="fetchCharacteristics" class="btn-secondary">
                                <RefreshCw class="w-5 h-5 mr-2"/>
                                Обновить
                            </button>
                        </div>
                    </div>

                    <div v-if="loading" class="py-6 text-center">
                        <Loader2 class="animate-spin h-8 w-8 text-indigo-600 mx-auto" />
                    </div>
                    <div v-else-if="characteristics.length === 0" class="py-6 text-center text-gray-500">
                        Нет доступных характеристик
                    </div>
                    <div v-else>
                        <!-- Стандартный режим (без группировки) -->
                        <div v-if="!groupByGroups" class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-xl overflow-hidden">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Характеристики</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ФИО студента</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата создания</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                <tr v-for="characteristic in characteristics" :key="characteristic.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ characteristic.id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">
                                            {{ characteristic.student_fio }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[characteristic.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full']">
                                            {{ characteristic.passed ? 'Утверждено' : 'Не утверждено' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ formatDate(characteristic.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="viewCharacteristic(characteristic)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
                                            <Eye class="w-5 h-5" />
                                        </button>
                                        <button @click="editCharacteristic(characteristic)" class="text-amber-600 hover:text-amber-900 p-1 rounded-full hover:bg-amber-50">
                                            <Edit class="w-5 h-5" />
                                        </button>
                                        <button @click="confirmDelete(characteristic)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
                                            <Trash2 class="w-5 h-5" />
                                        </button>
                                        <button @click="downloadCharacteristic(characteristic)" class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50">
                                            <Download class="w-5 h-5" />
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Режим с группировкой -->
                        <div v-else class="space-y-8">
                            <div v-for="(group, groupId) in groupedCharacteristics" :key="groupId" class="bg-white rounded-xl shadow overflow-hidden">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 flex justify-between items-center">
                                    <h3 class="text-lg font-medium text-white">{{ group.name }}</h3>
                                    <span class="bg-white text-green-700 font-semibold px-2 py-1 rounded-full text-sm">
                                        {{ group.characteristics.length }} характеристик
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Характеристики</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ФИО студента</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата создания</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                        <tr v-for="characteristic in group.characteristics" :key="characteristic.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ characteristic.id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">
                                                    {{ characteristic.student_fio }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="[characteristic.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full']">
                                                    {{ characteristic.passed ? 'Утверждено' : 'Не утверждено' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                                {{ formatDate(characteristic.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <button @click="viewCharacteristic(characteristic)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
                                                    <Eye class="w-5 h-5" />
                                                </button>
                                                <button @click="editCharacteristic(characteristic)" class="text-amber-600 hover:text-amber-900 p-1 rounded-full hover:bg-amber-50">
                                                    <Edit class="w-5 h-5" />
                                                </button>
                                                <button @click="confirmDelete(characteristic)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
                                                    <Trash2 class="w-5 h-5" />
                                                </button>
                                                <button @click="downloadCharacteristic(characteristic)" class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50">
                                                    <Download class="w-5 h-5" />
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
                    <div v-if="characteristicsPagination.last_page > 1" class="flex justify-center mt-6">
                        <nav class="flex items-center space-x-1">
                            <button
                                @click="changePage(1)"
                                :disabled="characteristicsPagination.current_page === 1"
                                :class="[characteristicsPagination.current_page === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                <ChevronsLeft class="w-5 h-5" />
                            </button>

                            <button
                                @click="changePage(characteristicsPagination.current_page - 1)"
                                :disabled="characteristicsPagination.current_page === 1"
                                :class="[characteristicsPagination.current_page === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                <ChevronLeft class="w-5 h-5" />
                            </button>

                            <button
                                v-for="page in displayedPages"
                                :key="page"
                                @click="changePage(page)"
                                :class="[page === characteristicsPagination.current_page ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                {{ page }}
                            </button>

                            <button
                                @click="changePage(characteristicsPagination.current_page + 1)"
                                :disabled="characteristicsPagination.current_page === characteristicsPagination.last_page"
                                :class="[characteristicsPagination.current_page === characteristicsPagination.last_page ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                <ChevronRight class="w-5 h-5" />
                            </button>

                            <button
                                @click="changePage(characteristicsPagination.last_page)"
                                :disabled="characteristicsPagination.current_page === characteristicsPagination.last_page"
                                :class="[characteristicsPagination.current_page === characteristicsPagination.last_page ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-indigo-50', 'px-3 py-2 rounded-md text-sm font-medium']"
                            >
                                <ChevronsRight class="w-5 h-5" />
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для просмотра характеристики -->
        <div v-if="viewModal.show" class="modal-backdrop">
            <div class="modal-content max-w-4xl animate-scaleIn">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-medium text-white">Просмотр характеристики</h3>
                    <button @click="closeViewModal" class="text-white hover:text-indigo-100">
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <div class="p-6">
                    <div class="bg-indigo-50 p-4 rounded-lg mb-6">
                        <div class="flex items-center mb-2">
                            <Info class="text-indigo-600 w-5 h-5 mr-2" />
                            <h4 class="font-semibold text-indigo-700">Информация о студенте</h4>
                        </div>
                        <p class="text-indigo-900">
                            <!-- Показываем ФИО студента из характеристики -->
                            {{ viewModal.characteristic.student_fio || 'Информация о студенте недоступна' }}
                        </p>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2 text-gray-700">Характеристика:</h4>
                        <div class="bg-white border rounded-lg p-4">
                            <div v-if="viewModal.characteristic.text" class="prose prose-indigo max-w-none">
                                <div v-html="viewModal.characteristic.text"></div>
                            </div>
                            <div v-else class="text-gray-500 italic">
                                Текст характеристики не доступен
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-8">
                        <div class="flex items-center">
                            <span class="mr-2">Статус:</span>
                            <span :class="[viewModal.characteristic.passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full']">
                                {{ viewModal.characteristic.passed ? 'Утверждено' : 'Не утверждено' }}
                            </span>
                        </div>

                        <div class="flex space-x-3">
                            <button @click="downloadCharacteristic(viewModal.characteristic)" class="btn-primary">
                                <Download class="w-5 h-5 mr-2" />
                                Скачать
                            </button>
                            <button @click="editCharacteristic(viewModal.characteristic)" class="btn-secondary">
                                <Edit class="w-5 h-5 mr-2" />
                                Редактировать
                            </button>
                            <button @click="confirmDelete(viewModal.characteristic)" class="btn-danger">
                                <Trash2 class="w-5 h-5 mr-2" />
                                Удалить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно для формы создания/редактирования -->
        <div v-if="formModal.show" class="modal-backdrop">
            <div class="modal-content max-w-4xl animate-scaleIn">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-medium text-white">
                        {{ formModal.isEdit ? 'Редактирование характеристики' : 'Создание характеристики' }}
                    </h3>
                    <button @click="closeFormModal" class="text-white hover:text-indigo-100">
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <div class="p-6">
                    <form @submit.prevent="saveCharacteristic">
                        <div class="mb-6">
                            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Студент:</label>
                            <select
                                id="student_id"
                                v-model="formModal.form.student_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                :disabled="formModal.isEdit"
                                :class="{'border-red-500': formErrors.student_id}"
                            >
                                <option value="" disabled>Выберите студента</option>
                                <option v-for="student in students" :key="student.id" :value="student.id">
                                    {{ student.surname }} {{ student.name }} {{ student.patronymic }}
                                </option>
                            </select>
                            <p v-if="formErrors.student_id" class="mt-1 text-sm text-red-500">{{ formErrors.student_id[0] }}</p>
                        </div>

                        <!-- Точно 3 раздела для характеристики -->
                        <div v-if="!formModal.isEdit" class="mb-6 space-y-4">
                            <h4 class="font-semibold text-gray-700">Текст характеристики (3 раздела):</h4>

                            <!-- Первый раздел с подсказкой -->
                            <div class="border rounded-lg bg-white p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Раздел 1:</label>
                                <div class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-md text-blue-800 text-sm">
                                    <Info class="inline-block w-5 h-5 mr-1 align-text-bottom" />
                                    В начале всегда автоматически будет добавлено: "Студент [ФИО], [дата рождения] г.р., учебной группы [группа]"
                                </div>
                                <textarea
                                    v-model="formModal.form.characteristics[0]"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    :class="{'border-red-500': formErrors['characteristics.0']}"
                                    placeholder="Введите текст первого раздела характеристики..."
                                ></textarea>
                                <p v-if="formErrors['characteristics.0']" class="mt-1 text-sm text-red-500">{{ formErrors['characteristics.0'][0] }}</p>
                            </div>

                            <!-- Второй раздел -->
                            <div class="border rounded-lg bg-white p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Раздел 2:</label>
                                <textarea
                                    v-model="formModal.form.characteristics[1]"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    :class="{'border-red-500': formErrors['characteristics.1']}"
                                    placeholder="Введите текст второго раздела характеристики..."
                                ></textarea>
                                <p v-if="formErrors['characteristics.1']" class="mt-1 text-sm text-red-500">{{ formErrors['characteristics.1'][0] }}</p>
                            </div>

                            <!-- Третий раздел -->
                            <div class="border rounded-lg bg-white p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Раздел 3:</label>
                                <textarea
                                    v-model="formModal.form.characteristics[2]"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    :class="{'border-red-500': formErrors['characteristics.2']}"
                                    placeholder="Введите текст третьего раздела характеристики..."
                                ></textarea>
                                <p v-if="formErrors['characteristics.2']" class="mt-1 text-sm text-red-500">{{ formErrors['characteristics.2'][0] }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center">
                                <input
                                    id="passed"
                                    type="checkbox"
                                    v-model="formModal.form.passed"
                                    class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <label for="passed" class="ml-2 block text-sm text-gray-700">
                                    Утвердить характеристику
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-8">
                            <button type="button" @click="closeFormModal" class="btn-secondary">
                                Отмена
                            </button>
                            <button type="submit" class="btn-primary" :disabled="formModal.loading">
                                <span v-if="formModal.loading">
                                    <Loader2 class="animate-spin w-5 h-5 mr-2" />
                                    Сохранение...
                                </span>
                                <span v-else>
                                    <CheckCircle class="w-5 h-5 mr-2" />
                                    {{ formModal.isEdit ? 'Сохранить изменения' : 'Создать характеристику' }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления -->
        <div v-if="deleteModal.show" class="modal-backdrop">
            <div class="modal-content max-w-md animate-scaleIn">
                <div class="bg-red-500 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-medium text-white">Подтверждение удаления</h3>
                    <button @click="closeDeleteModal" class="text-white hover:text-red-100">
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <AlertTriangle class="text-red-500 w-8 h-8 mr-3 flex-shrink-0" />
                        <p class="text-gray-700">
                            Вы уверены, что хотите удалить характеристику студента
                            <span class="font-semibold">{{ deleteModal.characteristic.student_fio }}</span>?
                            Это действие нельзя будет отменить.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button @click="closeDeleteModal" class="btn-secondary">
                            Отмена
                        </button>
                        <button @click="deleteCharacteristic" class="btn-danger" :disabled="deleteModal.loading">
                            <span v-if="deleteModal.loading">
                                <Loader2 class="animate-spin w-5 h-5 mr-2" />
                                Удаление...
                            </span>
                            <span v-else>
                                <Trash2 class="w-5 h-5 mr-2" />
                                Удалить
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast-уведомление -->
        <div v-if="toast.show"
             class="fixed right-0 bottom-0 m-6 max-w-sm animate-slideIn"
             :class="{
                'bg-green-50 border-green-400': toast.type === 'success',
                'bg-red-50 border-red-400': toast.type === 'error',
                'bg-blue-50 border-blue-400': toast.type === 'info'
            }"
        >
            <div class="rounded-lg border shadow-lg overflow-hidden">
                <div class="px-4 py-3 flex justify-between items-center"
                     :class="{
                        'bg-green-100': toast.type === 'success',
                        'bg-red-100': toast.type === 'error',
                        'bg-blue-100': toast.type === 'info'
                    }"
                >
                    <div class="flex items-center">
                        <CheckCircle v-if="toast.type === 'success'" class="w-5 h-5 text-green-600 mr-2" />
                        <XCircle v-if="toast.type === 'error'" class="w-5 h-5 text-red-600 mr-2" />
                        <Info v-if="toast.type === 'info'" class="w-5 h-5 text-blue-600 mr-2" />
                        <span class="font-medium"
                              :class="{
                                'text-green-700': toast.type === 'success',
                                'text-red-700': toast.type === 'error',
                                'text-blue-700': toast.type === 'info'
                            }"
                        >{{ toast.title }}</span>
                    </div>
                    <button @click="hideToast" class="text-gray-400 hover:text-gray-500">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <div class="px-4 py-3 bg-white">
                    <p class="text-sm text-gray-700">{{ toast.message }}</p>
                </div>
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
        Info,
        Download
    } from 'lucide-vue-next';
    import { useAuthStore } from '../Stores/auth';

    // Инициализация хранилища аутентификации
    const authStore = useAuthStore();

    // Состояние данных
    const characteristics = ref([]);
    const students = ref([]);
    const characteristicStudentMap = ref({});
    const availableGroups = ref([]);
    const loading = ref(false);
    const formErrors = ref({});
    const groupByGroups = ref(false);

    // Модальное окно для уведомлений (Toast)
    const toast = reactive({
    show: false,
    type: 'info', // success, error, info
    title: '',
    message: '',
    timeout: null
});

    // Получение информации о студенте по ID характеристики
    const getStudentForCharacteristic = (characteristicId) => {
    const studentId = characteristicStudentMap.value[characteristicId];
    if (!studentId) return null;

    return students.value.find(s => s.id === studentId);
};

    const downloadCharacteristic = async (characteristic) => {
        try {
            // Устанавливаем responseType: 'blob' для получения бинарных данных
            const response = await axios.get(
                `/api/v1/studentCharacteristics/download/${characteristic.id}`,
                {
                    responseType: 'blob'
                }
            );

            // Создаем URL объект из полученных данных
            const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
            const url = window.URL.createObjectURL(blob);

            // Создаем временный элемент ссылки для скачивания
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `student_characteristic_${characteristic.id}.docx`);
            document.body.appendChild(link);

            // Имитируем клик для начала скачивания
            link.click();

            // Удаляем временный элемент и освобождаем URL
            window.URL.revokeObjectURL(url);
            document.body.removeChild(link);

            showToast('Успешно', 'Файл успешно скачан', 'success');
        } catch (error) {
            console.error('Ошибка при скачивании файла:', error);
            showToast('Ошибка', 'Не удалось скачать файл', 'error');
        }
    };

    // Группировка характеристик по группам
    const groupedCharacteristics = computed(() => {
    if (!groupByGroups.value) {
    return { ungrouped: { name: 'Все характеристики', characteristics: characteristics.value } };
}

    const groups = {};
    characteristics.value.forEach(characteristic => {
    const student = getStudentForCharacteristic(characteristic.id);
    const groupId = student?.group_id || 'unknown';
    const groupName = availableGroups.value.find(g => g.id === groupId)?.name || 'Без группы';

    if (!groups[groupId]) {
    groups[groupId] = {
    id: groupId,
    name: groupName,
    characteristics: []
};
}

    groups[groupId].characteristics.push(characteristic);
});

    return groups;
});

    // Состояние пагинации для студентов
    const studentsPagination = reactive({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
});

    // Состояние пагинации для характеристик
    const characteristicsPagination = reactive({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
});

    // Вычисляемые страницы для пагинации характеристик
    const displayedPages = computed(() => {
    const pages = [];
    const maxDisplayed = 5;
    const halfMax = Math.floor(maxDisplayed / 2);

    let start = Math.max(1, characteristicsPagination.current_page - halfMax);
    let end = Math.min(characteristicsPagination.last_page, start + maxDisplayed - 1);

    if (end - start + 1 < maxDisplayed) {
    start = Math.max(1, end - maxDisplayed + 1);
}

    for (let i = start; i <= end; i++) {
    pages.push(i);
}

    return pages;
});

    // Модальное окно для просмотра характеристики
    const viewModal = reactive({
    show: false,
    characteristic: {},
    student: null
});

    // Модальное окно для формы создания/редактирования
    const formModal = reactive({
    show: false,
    isEdit: false,
    loading: false,
    form: {
    student_id: '',
    characteristics: ['', '', ''],
    passed: false
},
    characteristicId: null
});

    // Модальное окно подтверждения удаления
    const deleteModal = reactive({
    show: false,
    loading: false,
    characteristic: {}
});

    // Функция для форматирования даты
    const formatDate = (dateString) => {
    if (!dateString) return 'Н/Д';

    const options = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
};

    return new Date(dateString).toLocaleDateString('ru-RU', options);
};

    // Функция для получения списка характеристик
    const fetchCharacteristics = async () => {
    loading.value = true;
    try {
    const response = await axios.get('/api/v1/studentCharacteristics', {
    params: {
    page: characteristicsPagination.current_page,
    per_page: characteristicsPagination.per_page
}
});

    characteristics.value = response.data.data;

    // Обновление пагинации
    characteristicsPagination.current_page = response.data.meta.current_page;
    characteristicsPagination.last_page = response.data.meta.last_page;
    characteristicsPagination.total = response.data.meta.total;
    characteristicsPagination.per_page = response.data.meta.per_page;

    // Если текущая страница больше последней, переходим на последнюю
    if (characteristicsPagination.current_page > characteristicsPagination.last_page && characteristicsPagination.last_page > 0) {
    changePage(characteristicsPagination.last_page);
}

    // Связываем характеристики со студентами
    mapCharacteristicsToStudents();
} catch (error) {
    console.error('Ошибка при загрузке характеристик:', error);
    showToast('error', 'Ошибка загрузки', 'Не удалось загрузить характеристики. Пожалуйста, попробуйте позже.');
} finally {
    loading.value = false;
}
};

    // Функция для получения студентов
    const fetchStudents = async () => {
    try {
    const response = await axios.get('/api/v1/students');
    students.value = response.data.data.map(student => ({
    ...student,
    // Создаем полное ФИО для удобства
    fio: `${student.surname} ${student.name} ${student.patronymic}`
}));

    // Обновление пагинации
    studentsPagination.current_page = response.data.meta.current_page;
    studentsPagination.last_page = response.data.meta.last_page;
    studentsPagination.total = response.data.meta.total;

    // Если есть еще страницы, загружаем их
    if (response.data.meta.current_page < response.data.meta.last_page) {
    for (let page = 2; page <= response.data.meta.last_page; page++) {
    const nextPageResponse = await axios.get('/api/v1/students', {
    params: { page }
});

    const moreStudents = nextPageResponse.data.data.map(student => ({
    ...student,
    fio: `${student.surname} ${student.name} ${student.patronymic}`
}));

    students.value = [...students.value, ...moreStudents];
}
}
} catch (error) {
    console.error('Ошибка при загрузке студентов:', error);
    showToast('error', 'Ошибка загрузки', 'Не удалось загрузить список студентов.');
}
};

    // Связывание характеристик со студентами
    const mapCharacteristicsToStudents = () => {
    // Сбрасываем текущие связи
    characteristicStudentMap.value = {};

    // Для каждой характеристики находим студента по characteristic_id
    characteristics.value.forEach(characteristic => {
    const student = students.value.find(s => s.characteristic_id === characteristic.id);
    if (student) {
    characteristicStudentMap.value[characteristic.id] = student.id;
}
});
};

    // Функция для получения списка групп
    const fetchGroups = async () => {
    try {
    const response = await axios.get('/api/v1/groups');
    availableGroups.value = response.data;
} catch (error) {
    console.error('Ошибка при загрузке групп:', error);
    showToast('error', 'Ошибка загрузки', 'Не удалось загрузить список групп.');
}
};

    // Функция для изменения страницы пагинации
    const changePage = (page) => {
    if (page < 1 || page > characteristicsPagination.last_page || page === characteristicsPagination.current_page) {
    return;
}

    characteristicsPagination.current_page = page;
    fetchCharacteristics();
};

    // Открытие модального окна для создания новой характеристики
    const openCreateModal = () => {
    formModal.isEdit = false;
    formModal.characteristicId = null;
    formModal.form = {
    student_id: '',
    characteristics: ['', '', ''],
    passed: false
};
    formErrors.value = {};
    formModal.show = true;
};

    // Открытие модального окна для просмотра характеристики
    const viewCharacteristic = (characteristic) => {
    const student = getStudentForCharacteristic(characteristic.id);

    viewModal.characteristic = { ...characteristic };
    viewModal.student = student || null;
    viewModal.show = true;
};

    // Закрытие модального окна просмотра
    const closeViewModal = () => {
    viewModal.show = false;
    setTimeout(() => {
    viewModal.characteristic = {};
    viewModal.student = null;
}, 300);
};

    // Открытие модального окна для редактирования характеристики
    const editCharacteristic = (characteristic) => {
    const student = getStudentForCharacteristic(characteristic.id);

    formModal.isEdit = true;
    formModal.characteristicId = characteristic.id;
    formModal.form = {
    student_id: student?.id || '',
    characteristics: [],
    passed: characteristic.passed
};
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
    setTimeout(() => {
    formModal.form = {
    student_id: '',
    characteristics: ['', '', ''],
    passed: false
};
    formModal.isEdit = false;
    formModal.characteristicId = null;
    formErrors.value = {};
}, 300);
};

    // Открытие модального окна для подтверждения удаления
    const confirmDelete = (characteristic) => {
    deleteModal.characteristic = characteristic;
    deleteModal.show = true;

    // Если открыто модальное окно просмотра, закрываем его
    if (viewModal.show) {
    closeViewModal();
}
};

    // Закрытие модального окна подтверждения удаления
    const closeDeleteModal = () => {
    deleteModal.show = false;
    setTimeout(() => {
    deleteModal.characteristic = {};
}, 300);
};

    // Сохранение характеристики (создание или обновление)
    const saveCharacteristic = async () => {
    formModal.loading = true;
    formErrors.value = {};

    try {
    let response;

    if (formModal.isEdit) {
    // Обновление существующей характеристики
    response = await axios.put(`/api/v1/studentCharacteristics/${formModal.characteristicId}`, {
    passed: formModal.form.passed
});
    showToast('success', 'Успешно обновлено', 'Характеристика успешно обновлена.');
} else {
    // Создание новой характеристики
    response = await axios.post('/api/v1/studentCharacteristics', {
    student_id: formModal.form.student_id,
    characteristics: formModal.form.characteristics,
    passed: formModal.form.passed
});
    showToast('success', 'Успешно создано', 'Характеристика успешно создана.');
}

    closeFormModal();
    fetchCharacteristics();
} catch (error) {
    console.error('Ошибка при сохранении характеристики:', error);

    if (error.response?.status === 422) {
    // Ошибки валидации
    formErrors.value = error.response.data.errors || {};
    showToast('error', 'Ошибка валидации', 'Проверьте правильность заполнения полей формы.');
} else {
    showToast('error', 'Ошибка сохранения', 'Не удалось сохранить характеристику. Пожалуйста, попробуйте позже.');
}
} finally {
    formModal.loading = false;
}
};

    // Удаление характеристики
    const deleteCharacteristic = async () => {
    deleteModal.loading = true;

    try {
    await axios.delete(`/api/v1/studentCharacteristics/${deleteModal.characteristic.id}`);
    showToast('success', 'Успешно удалено', 'Характеристика успешно удалена.');
    closeDeleteModal();
    fetchCharacteristics();
} catch (error) {
    console.error('Ошибка при удалении характеристики:', error);
    showToast('error', 'Ошибка удаления', 'Не удалось удалить характеристику. Пожалуйста, попробуйте позже.');
} finally {
    deleteModal.loading = false;
}
};

    // Функция для отображения уведомлений
    const showToast = (type, title, message, duration = 5000) => {
    // Очищаем предыдущий таймаут, если есть
    if (toast.timeout) {
    clearTimeout(toast.timeout);
}

    // Устанавливаем параметры уведомления
    toast.type = type;
    toast.title = title;
    toast.message = message;
    toast.show = true;

    // Устанавливаем таймаут для автоматического скрытия
    toast.timeout = setTimeout(() => {
    hideToast();
}, duration);
};

    // Функция для скрытия уведомления
    const hideToast = () => {
    toast.show = false;

    if (toast.timeout) {
    clearTimeout(toast.timeout);
    toast.timeout = null;
}
};

    // Проверка аутентификации и загрузка данных при монтировании компонента
    onMounted(async () => {
    // Убедимся, что пользователь аутентифицирован
    if (!authStore.isAuthenticated) {
    showToast('error', 'Требуется авторизация', 'Пожалуйста, войдите в систему для доступа к этой странице.');
    return;
}

    try {
    loading.value = true;

    // Загружаем данные параллельно
    await Promise.all([
    fetchStudents(),
    fetchGroups()
    ]);

    // После загрузки вспомогательных данных загружаем характеристики
    await fetchCharacteristics();
} catch (error) {
    console.error('Ошибка при инициализации страницы:', error);
    showToast('error', 'Ошибка загрузки', 'Не удалось загрузить необходимые данные. Пожалуйста, обновите страницу.');
} finally {
    loading.value = false;
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
