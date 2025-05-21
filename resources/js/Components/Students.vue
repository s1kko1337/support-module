
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
                    Студенты
                </h1>
                <p class="text-lg text-indigo-100 text-center max-w-3xl mx-auto">
                    Управление данными студентов
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
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Список студентов</h2>
                        <div class="flex space-x-3">
                            <button @click="openCreateModal" class="btn-primary">
                                <Plus class="w-5 h-5 mr-2"/>
                                Добавить студента
                            </button>
                            <button @click="fetchStudents" class="btn-secondary">
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
                    <div v-else-if="students.length === 0" class="py-6 text-center text-gray-500">
                        Нет доступных студентов
                    </div>
                    <div v-else>
                        <!-- Стандартный режим (без группировки) -->
                        <div v-if="!groupByGroups" class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-xl overflow-hidden">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ФИО</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Группа</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Образование</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Форма обучения</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата рождения</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ `${student.surname} ${student.name} ${student.patronymic}` }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ student.group_name }}
                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ student.education }}
                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ student.education_form }}
                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ formatDate(student.date_of_birth) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="viewStudent(student)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
                                            <Eye class="w-5 h-5" />
                                        </button>
                                        <button @click="editStudent(student)" class="text-amber-600 hover:text-amber-900 p-1 rounded-full hover:bg-amber-50">
                                            <Edit class="w-5 h-5" />
                                        </button>
                                        <button @click="confirmDelete(student)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
                                            <Trash2 class="w-5 h-5" />
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Режим с группировкой -->
                        <div v-else class="space-y-8">
                            <div v-for="(group, groupId) in groupedStudents" :key="groupId" class="bg-white rounded-xl shadow overflow-hidden">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 flex justify-between items-center">
                                    <h3 class="text-lg font-medium text-white">{{ group.name }}</h3>
                                    <span class="bg-white text-green-700 font-semibold px-2 py-1 rounded-full text-sm">
                    {{ group.students.length }} студент(ов)
                </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ФИО</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Образование</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Форма обучения</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата рождения</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                        <tr v-for="student in group.students" :key="student.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ `${student.surname} ${student.name} ${student.patronymic}` }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ student.education }}
                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ student.education_form }}
                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                                {{ formatDate(student.date_of_birth) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <button @click="viewStudent(student)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-50">
                                                    <Eye class="w-5 h-5" />
                                                </button>
                                                <button @click="editStudent(student)" class="text-amber-600 hover:text-amber-900 p-1 rounded-full hover:bg-amber-50">
                                                    <Edit class="w-5 h-5" />
                                                </button>
                                                <button @click="confirmDelete(student)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50">
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

        <!-- Модальное окно для просмотра студента -->
        <div v-if="viewModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-lg">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Просмотр данных студента</h3>
                        <button @click="closeViewModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <span class="text-sm text-indigo-700 font-medium">ФИО</span>
                            <div class="text-lg font-semibold mt-1">
                                {{ `${viewModal.student.surname} ${viewModal.student.name} ${viewModal.student.patronymic}` }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <span class="text-sm text-green-700 font-medium">Группа</span>
                                <div class="text-lg font-semibold mt-1">{{ viewModal.student.group_name }}</div>
                            </div>

                            <div class="bg-blue-50 p-4 rounded-lg">
                                <span class="text-sm text-blue-700 font-medium">Телефон</span>
                                <div class="text-lg font-semibold mt-1">{{ viewModal.student.phone_number }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-amber-50 p-4 rounded-lg">
                                <span class="text-sm text-amber-700 font-medium">Образование</span>
                                <div class="text-lg font-semibold mt-1">{{ viewModal.student.education }}</div>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-lg">
                                <span class="text-sm text-purple-700 font-medium">Форма обучения</span>
                                <div class="text-lg font-semibold mt-1">{{ viewModal.student.education_form }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Дата рождения</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.student.date_of_birth) }}</div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Гражданство</span>
                                <div class="text-md font-medium mt-1">{{ viewModal.student.citizenship }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Дата создания</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.student.created_at) }}</div>
                            </div>

                            <div class="p-4 border border-gray-200 rounded-lg">
                                <span class="text-sm text-gray-500 font-medium">Последнее обновление</span>
                                <div class="text-md font-medium mt-1">{{ formatDate(viewModal.student.updated_at) }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button @click="editStudent(viewModal.student)" class="btn-primary mr-3">
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

        <!-- Модальное окно для создания/редактирования студента -->
        <div v-if="formModal.show" class="modal-backdrop">
            <div class="modal-content animate-scaleIn max-w-lg">
                <div class="h-2 bg-gradient-to-r from-indigo-400 to-purple-600 rounded-t-xl"></div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ formModal.isEdit ? 'Редактирование данных студента' : 'Добавление студента' }}
                        </h3>
                        <button @click="closeFormModal" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <X class="w-6 h-6"/>
                        </button>
                    </div>

                    <form @submit.prevent="saveStudent" class="space-y-4">
                        <!-- Фамилия -->
                        <div>
                            <label for="student-surname" class="block text-sm font-medium text-gray-700 mb-1">Фамилия*</label>
                            <input
                                id="student-surname"
                                v-model="formModal.form.surname"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите фамилию"
                                required
                            >
                            <span v-if="formErrors.surname" class="text-sm text-red-600">{{ formErrors.surname[0] }}</span>
                        </div>

                        <!-- Имя -->
                        <div>
                            <label for="student-name" class="block text-sm font-medium text-gray-700 mb-1">Имя*</label>
                            <input
                                id="student-name"
                                v-model="formModal.form.name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите имя"
                                required
                            >
                            <span v-if="formErrors.name" class="text-sm text-red-600">{{ formErrors.name[0] }}</span>
                        </div>

                        <!-- Отчество -->
                        <div>
                            <label for="student-patronymic" class="block text-sm font-medium text-gray-700 mb-1">Отчество*</label>
                            <input
                                id="student-patronymic"
                                v-model="formModal.form.patronymic"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите отчество"
                                required
                            >
                            <span v-if="formErrors.patronymic" class="text-sm text-red-600">{{ formErrors.patronymic[0] }}</span>
                        </div>

                        <!-- Дата рождения -->
                        <div>
                            <label for="date-of-birth" class="block text-sm font-medium text-gray-700 mb-1">Дата рождения*</label>
                            <input
                                id="date-of-birth"
                                v-model="formModal.form.date_of_birth"
                                type="date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                            <span v-if="formErrors.date_of_birth" class="text-sm text-red-600">{{ formErrors.date_of_birth[0] }}</span>
                        </div>

                        <!-- Телефон -->
                        <div>
                            <label for="phone-number" class="block text-sm font-medium text-gray-700 mb-1">Телефон*</label>
                            <input
                                id="phone-number"
                                v-model="formModal.form.phone_number"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="+7 (999) 999-99-99"
                                required
                            >
                            <span v-if="formErrors.phone_number" class="text-sm text-red-600">{{ formErrors.phone_number[0] }}</span>
                        </div>

                        <!-- Гражданство -->
                        <div>
                            <label for="citizenship" class="block text-sm font-medium text-gray-700 mb-1">Гражданство*</label>
                            <input
                                id="citizenship"
                                v-model="formModal.form.citizenship"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Укажите гражданство"
                                required
                            >
                            <span v-if="formErrors.citizenship" class="text-sm text-red-600">{{ formErrors.citizenship[0] }}</span>
                        </div>

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
                                <option v-for="group in availableGroups" :key="group.id" :value="group.id">
                                    {{ group.name || 'Группа ' + group.id }}
                                </option>
                            </select>
                            <span v-if="formErrors.group_id" class="text-sm text-red-600">{{ formErrors.group_id[0] }}</span>
                        </div>

                        <!-- Образование -->
                        <div>
                            <label for="education" class="block text-sm font-medium text-gray-700 mb-1">Образование*</label>
                            <select
                                id="education"
                                v-model="formModal.form.education"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                                <option value="" disabled>Выберите уровень образования</option>
                                <option value="общее среднее">Общее среднее</option>
                                <option value="среднее специальное">Среднее специальное</option>
                                <option value="высшее">Высшее</option>
                            </select>
                            <span v-if="formErrors.education" class="text-sm text-red-600">{{ formErrors.education[0] }}</span>
                        </div>

                        <!-- Форма обучения -->
                        <div>
                            <label for="education-form" class="block text-sm font-medium text-gray-700 mb-1">Форма обучения*</label>
                            <select
                                id="education-form"
                                v-model="formModal.form.education_form"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                                <option value="" disabled>Выберите форму обучения</option>
                                <option value="бюджет">Бюджет</option>
                                <option value="целевое">Целевое</option>
                                <option value="платное">Платное</option>
                            </select>
                            <span v-if="formErrors.education_form" class="text-sm text-red-600">{{ formErrors.education_form[0] }}</span>
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
                            Вы уверены, что хотите удалить студента "{{ `${deleteModal.student.surname} ${deleteModal.student.name}` }}"?
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
                            @click="deleteStudent"
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
const students = ref([]);
const availableGroups = ref([]);
const loading = ref(false);
const formErrors = ref({});
// Добавить новое состояние
const groupByGroups = ref(false);

const groupedStudents = computed(() => {
    if (!groupByGroups.value) {
        return { ungrouped: students.value };
    }

    return students.value.reduce((groups, student) => {
        const groupId = student.group_id;
        const groupName = student.group_name || `Группа ${groupId}`;

        if (!groups[groupId]) {
            groups[groupId] = {
                id: groupId,
                name: groupName,
                students: []
            };
        }

        groups[groupId].students.push(student);
        return groups;
    }, {});
});

// Функция обновления количества студентов в группе
const updateGroupStudentsCount = async (groupId, increment = true) => {
    try {
        // Получаем текущие данные группы
        const groupResponse = await axios.get(`/api/v1/groups/${groupId}`);
        const group = groupResponse.data;

        // Вычисляем новое количество студентов
        const currentCount = group.number_of_students || 0;
        const newCount = increment ? currentCount + 1 : Math.max(0, currentCount - 1);

        // Отправляем обновленные данные
        await axios.put(`/api/v1/groups/${groupId}`, {
            number_of_students: newCount
        });

        // Обновляем список групп, чтобы UI отображал актуальные данные
        if (availableGroups.value.length > 0) {
            const groupIndex = availableGroups.value.findIndex(g => g.id === groupId);
            if (groupIndex !== -1) {
                availableGroups.value[groupIndex].number_of_students = newCount;
            }
        }
    } catch (error) {
        console.error(`Ошибка при обновлении количества студентов в группе ${groupId}:`, error);
    }
};

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

// Модальное окно для просмотра студента
const viewModal = reactive({
    show: false,
    student: {}
});

// Модальное окно для формы создания/редактирования
const formModal = reactive({
    show: false,
    isEdit: false,
    loading: false,
    form: {
        surname: '',
        name: '',
        patronymic: '',
        date_of_birth: '',
        phone_number: '',
        citizenship: '',
        group_id: '',
        education: '',
        education_form: ''
    },
    studentId: null
});

// Модальное окно подтверждения удаления
const deleteModal = reactive({
    show: false,
    student: {},
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

// Загрузка списка студентов
const fetchStudents = async (page = 1) => {
    loading.value = true;

    try {
        const response = await axios.get('/api/v1/students', {
            params: {
                page
            }
        });

        students.value = response.data.data;

        // Обновление информации о пагинации
        pagination.current_page = response.data.meta.current_page;
        pagination.last_page = response.data.meta.last_page;
        pagination.per_page = response.data.meta.per_page;
        pagination.total = response.data.meta.total;

    } catch (error) {
        console.error('Ошибка при загрузке списка студентов:', error);
        showToast('error', 'Ошибка', 'Не удалось загрузить список студентов');
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
        console.error('Ошибка при загрузке списка групп:', error);
        showToast('error', 'Ошибка', 'Не удалось загрузить список групп');
    }
};

// Изменение страницы пагинации
const changePage = (page) => {
    if (page < 1 || page > pagination.last_page || page === pagination.current_page) {
        return;
    }

    fetchStudents(page);
};

// Форматирование даты в читаемый вид
const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;

    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
};

// Открытие модального окна для просмотра студента
const viewStudent = (student) => {
    viewModal.student = { ...student };
    viewModal.show = true;
};

// Закрытие модального окна просмотра
const closeViewModal = () => {
    viewModal.show = false;
    viewModal.student = {};
};

// Открытие модального окна для создания нового студента
const openCreateModal = async () => {
    // Сбросить форму и ошибки
    formModal.form = {
        surname: '',
        name: '',
        patronymic: '',
        date_of_birth: '',
        phone_number: '',
        citizenship: '',
        group_id: '',
        education: '',
        education_form: ''
    };
    formModal.isEdit = false;
    formModal.studentId = null;
    formErrors.value = {};

    // Загрузить группы, если они еще не загружены
    if (availableGroups.value.length === 0) {
        await fetchGroups();
    }

    // Показать модальное окно
    formModal.show = true;
};

// Открытие модального окна для редактирования студента
const editStudent = async (student) => {
    // Загрузить группы, если они еще не загружены
    if (availableGroups.value.length === 0) {
        await fetchGroups();
    }

    // Заполнить форму данными студента
    formModal.form = {
        surname: student.surname,
        name: student.name,
        patronymic: student.patronymic,
        date_of_birth: student.date_of_birth,
        phone_number: student.phone_number,
        citizenship: student.citizenship,
        group_id: student.group_id,
        education: student.education,
        education_form: student.education_form
    };

    formModal.isEdit = true;
    formModal.studentId = student.id;
    formErrors.value = {};
    formModal.show = true;

    // Закрыть окно просмотра, если оно открыто
    if (viewModal.show) {
        closeViewModal();
    }
};

// Закрытие модального окна формы
const closeFormModal = () => {
    formModal.show = false;
    formModal.loading = false;
    formErrors.value = {};
};

// Сохранение данных студента (создание или обновление)
// Сохранение данных студента (создание или обновление)
const saveStudent = async () => {
    formModal.loading = true;
    formErrors.value = {};

    try {
        let response;
        const originalGroupId = formModal.isEdit ? formModal.studentId ?
            students.value.find(s => s.id === formModal.studentId)?.group_id : null : null;

        if (formModal.isEdit) {
            // Обновление существующего студента
            response = await axios.put(`/api/v1/students/${formModal.studentId}`, formModal.form);
            showToast('success', 'Успешно', 'Данные студента обновлены');

            // Если изменилась группа, обновляем счетчики в обеих группах
            if (originalGroupId && originalGroupId !== formModal.form.group_id) {
                await updateGroupStudentsCount(originalGroupId, false); // Уменьшаем счетчик в старой группе
                await updateGroupStudentsCount(formModal.form.group_id, true); // Увеличиваем счетчик в новой группе
            }
        } else {
            // Создание нового студента
            response = await axios.post('/api/v1/students', formModal.form);
            showToast('success', 'Успешно', 'Студент успешно создан');

            // Увеличиваем счетчик студентов в группе
            await updateGroupStudentsCount(formModal.form.group_id, true);
        }

        // Закрыть модальное окно и обновить список студентов
        closeFormModal();
        fetchStudents(pagination.current_page);

    } catch (error) {
        console.error('Ошибка при сохранении данных студента:', error);

        // Обработка ошибок валидации
        if (error.response && error.response.status === 422) {
            formErrors.value = error.response.data.errors || {};
        } else {
            showToast('error', 'Ошибка', 'Не удалось сохранить данные студента');
        }
    } finally {
        formModal.loading = false;
    }
};

// Открытие модального окна подтверждения удаления
const confirmDelete = (student) => {
    deleteModal.student = student;
    deleteModal.show = true;
};

// Закрытие модального окна удаления
const closeDeleteModal = () => {
    deleteModal.show = false;
    deleteModal.loading = false;
    deleteModal.student = {};
};

// Удаление студента
// Удаление студента
const deleteStudent = async () => {
    deleteModal.loading = true;

    try {
        const groupId = deleteModal.student.group_id;

        await axios.delete(`/api/v1/students/${deleteModal.student.id}`);
        showToast('success', 'Успешно', `Студент ${deleteModal.student.surname} ${deleteModal.student.name} удален`);

        // Уменьшаем счетчик студентов в группе
        if (groupId) {
            await updateGroupStudentsCount(groupId, false);
        }

        // Закрыть модальное окно и обновить список студентов
        closeDeleteModal();
        fetchStudents(pagination.current_page);

    } catch (error) {
        console.error('Ошибка при удалении студента:', error);
        showToast('error', 'Ошибка', 'Не удалось удалить студента');
    } finally {
        deleteModal.loading = false;
    }
};

// Отображение toast-уведомления
const showToast = (type, title, message, duration = 5000) => {
    // Очистить предыдущий таймер, если он существует
    if (toast.timeout) {
        clearTimeout(toast.timeout);
    }

    // Установить параметры уведомления
    toast.show = true;
    toast.type = type;
    toast.title = title;
    toast.message = message;

    // Автоматическое скрытие после указанного времени
    toast.timeout = setTimeout(() => {
        hideToast();
    }, duration);
};

// Скрытие toast-уведомления
const hideToast = () => {
    toast.show = false;

    // Очистить таймер
    if (toast.timeout) {
        clearTimeout(toast.timeout);
        toast.timeout = null;
    }
};

// Инициализация компонента
onMounted(async () => {
    // Загрузить список студентов и групп при монтировании компонента
    await fetchStudents();
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
