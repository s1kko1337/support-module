
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
                    Отчеты по студентам
                </h1>
                <p class="text-lg text-indigo-100 text-center max-w-3xl mx-auto">
                    Генерация и загрузка отчетов по студентам и группам
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
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Отчеты по аттестации</h2>
                        <div class="flex space-x-3">
                            <button @click="fetchGroups" class="btn-secondary">
                                <RefreshCw class="w-5 h-5 mr-2"/>
                                Обновить список
                            </button>
                        </div>
                    </div>

                    <div class="mb-8">
                        <div class="bg-indigo-50 p-6 rounded-xl mb-8">
                            <h3 class="text-xl font-semibold text-indigo-800 mb-4">Отчет по группе</h3>
                            <p class="text-gray-700 mb-4">Загрузите отчет по аттестации для выбранной группы студентов</p>

                            <div class="flex flex-col sm:flex-row items-end space-y-4 sm:space-y-0 sm:space-x-4">
                                <div class="w-full sm:w-1/2">
                                    <label for="group-select" class="block text-sm font-medium text-gray-700 mb-1">Выберите группу</label>
                                    <select
                                        id="group-select"
                                        v-model="selectedGroup"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        :disabled="loading.groups || loading.groupReport"
                                    >
                                        <option value="" disabled>Выберите группу</option>
                                        <option v-for="group in groups" :key="group.id" :value="group.id">
                                            {{ group.name }}
                                        </option>
                                    </select>
                                </div>
                                <button
                                    @click="generateGroupReport"
                                    class="btn-primary w-full sm:w-auto"
                                    :disabled="!selectedGroup || loading.groupReport"
                                >
                                    <Loader2 v-if="loading.groupReport" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"/>
                                    <FileText v-else class="w-5 h-5 mr-2"/>
                                    Сгенерировать отчет
                                </button>
                            </div>
                        </div>

                        <div class="bg-green-50 p-6 rounded-xl">
                            <h3 class="text-xl font-semibold text-green-800 mb-4">Отчет по студенту</h3>
                            <p class="text-gray-700 mb-4">Загрузите детальный отчет по аттестации для выбранного студента</p>

                            <div class="flex flex-col space-y-4">
                                <div class="w-full">
                                    <label for="student-group-select" class="block text-sm font-medium text-gray-700 mb-1">Группа студента</label>
                                    <select
                                        id="student-group-select"
                                        v-model="studentReport.groupId"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        :disabled="loading.groups || loading.students || loading.studentReport"
                                        @change="fetchStudentsByGroup"
                                    >
                                        <option value="" disabled>Выберите группу</option>
                                        <option v-for="group in groups" :key="group.id" :value="group.id">
                                            {{ group.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="w-full">
                                    <label for="student-select" class="block text-sm font-medium text-gray-700 mb-1">Выберите студента</label>
                                    <select
                                        id="student-select"
                                        v-model="studentReport.studentId"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        :disabled="!studentReport.groupId || loading.students || loading.studentReport"
                                    >
                                        <option value="" disabled>Выберите студента</option>
                                        <option v-for="student in students" :key="student.id" :value="student.id">
                                            {{ `${student.surname} ${student.name} ${student.patronymic}` }}
                                        </option>
                                    </select>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        @click="generateStudentReport"
                                        class="btn-primary"
                                        :disabled="!studentReport.groupId || !studentReport.studentId || loading.studentReport"
                                    >
                                        <Loader2 v-if="loading.studentReport" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"/>
                                        <User v-else class="w-5 h-5 mr-2"/>
                                        Сгенерировать отчет по студенту
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- История отчетов (для демонстрации UI) -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">История отчетов</h3>
                        <div v-if="reportHistory.length === 0" class="py-6 text-center text-gray-500 bg-gray-50 rounded-lg">
                            История отчетов пуста
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-xl overflow-hidden">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип отчета</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата создания</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                <tr v-for="(report, index) in reportHistory" :key="index" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[report.type === 'group' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800', 'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full']">
                                            {{ report.type === 'group' ? 'Группа' : 'Студент' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ report.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ report.date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="downloadAgain(report)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
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
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import {
    RefreshCw,
    FileText,
    User,
    Download,
    X,
    Loader2,
    CheckCircle,
    XCircle,
    Info
} from 'lucide-vue-next';

// Состояние данных
const groups = ref([]);
const students = ref([]);
const selectedGroup = ref('');
const reportHistory = ref([]);

// Состояние загрузки
const loading = reactive({
    groups: false,
    students: false,
    groupReport: false,
    studentReport: false
});

// Данные для отчета студента
const studentReport = reactive({
    groupId: '',
    studentId: ''
});

// Toast уведомления
const toast = reactive({
    show: false,
    type: 'info', // success, error, info
    title: '',
    message: '',
    timeout: null
});

// Загрузка списка групп
const fetchGroups = async () => {
    loading.groups = true;

    try {
        const response = await axios.get('/api/v1/groups');
        groups.value = response.data.data;
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить список групп');
        console.error('Error fetching groups:', error);
    } finally {
        loading.groups = false;
    }
};

// Загрузка студентов по группе

// Загрузка студентов по группе
const fetchStudentsByGroup = async () => {
    if (!studentReport.groupId) return;

    loading.students = true;
    students.value = [];
    studentReport.studentId = '';

    try {
        // Получаем список всех студентов
        const response = await axios.get('/api/v1/students');

        // Фильтруем только студентов, которые относятся к выбранной группе
        const allStudents = response.data.data;
        students.value = allStudents.filter(student => student.group_id === studentReport.groupId);

        if (students.value.length === 0) {
            showToast('info', 'Информация', 'В выбранной группе нет студентов');
        }
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось загрузить список студентов');
        console.error('Error fetching students by group:', error);
    } finally {
        loading.students = false;
    }
};

// Генерация отчета по группе
const generateGroupReport = async () => {
    if (!selectedGroup.value) {
        showToast('error', 'Ошибка', 'Выберите группу для генерации отчета');
        return;
    }

    loading.groupReport = true;

    try {
        // Используем предоставленный API endpoint для генерации отчета группы
        const response = await axios.get(`/api/v1/students/groupReport/${selectedGroup.value}`, {
            responseType: 'blob' // Важно для получения файла
        });

        // Получаем имя группы для истории
        const groupName = groups.value.find(g => g.id === selectedGroup.value)?.name || `Группа ${selectedGroup.value}`;

        // Создаем ссылку для скачивания файла
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `group_cert_report_${groupName}.docx`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Добавляем запись в историю
        addToHistory('group', groupName);

        showToast('success', 'Успешно', `Отчет по группе ${groupName} успешно сгенерирован`);
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось сгенерировать отчет по группе');
        console.error('Error generating group report:', error);
    } finally {
        loading.groupReport = false;
    }
};

// Генерация отчета по студенту
const generateStudentReport = async () => {
    if (!studentReport.groupId || !studentReport.studentId) {
        showToast('error', 'Ошибка', 'Выберите группу и студента для генерации отчета');
        return;
    }

    loading.studentReport = true;

    try {
        // Используем предоставленный API endpoint для генерации отчета по студенту
        const response = await axios.get(`/api/v1/students/report/${studentReport.groupId}/${studentReport.studentId}`, {
            responseType: 'blob' // Важно для получения файла
        });

        // Находим данные студента для истории
        const student = students.value.find(s => s.id === studentReport.studentId);
        const studentName = student ? `${student.surname} ${student.name}` : `Студент ${studentReport.studentId}`;

        // Создаем ссылку для скачивания файла
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Student_Report_${studentName}.docx`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Добавляем запись в историю
        addToHistory('student', studentName);

        showToast('success', 'Успешно', `Отчет по студенту ${studentName} успешно сгенерирован`);
    } catch (error) {
        showToast('error', 'Ошибка', 'Не удалось сгенерировать отчет по студенту');
        console.error('Error generating student report:', error);
    } finally {
        loading.studentReport = false;
    }
};

// Добавление отчета в историю
const addToHistory = (type, name) => {
    const now = new Date();
    const formattedDate = now.toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    reportHistory.value.unshift({
        type,
        name,
        date: formattedDate,
        // Для демонстрации UI сохраняем минимальную информацию
        // В реальном приложении можно сохранять ID или путь к файлу
        groupId: type === 'group' ? selectedGroup.value : studentReport.groupId,
        studentId: type === 'student' ? studentReport.studentId : null
    });
};

// Повторная загрузка отчета из истории
const downloadAgain = (report) => {
    if (report.type === 'group') {
        selectedGroup.value = report.groupId;
        generateGroupReport();
    } else {
        studentReport.groupId = report.groupId;
        fetchStudentsByGroup().then(() => {
            studentReport.studentId = report.studentId;
            generateStudentReport();
        });
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
    // Загрузка списка групп
    await fetchGroups();
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
.animate-slideIn {
    animation: slideIn 0.3s ease-out;
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
