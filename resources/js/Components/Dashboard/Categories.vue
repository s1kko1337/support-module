<template>
    <div>

        <!-- Предупреждение о необходимости авторизации -->
        <div v-if="!authStore.isAuthenticated" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
            <p class="font-bold">Доступ ограничен</p>
            <p>Для доступа к этой странице необходимо <router-link :to="{name:'login'}" class="underline text-indigo-600">войти в систему</router-link>.</p>
        </div>

        <template v-else>
            <!-- Вкладки для переключения между разделами -->
            <div class="border-b border-gray-200 mb-8">
                <nav class="flex -mb-px">
                    <router-link
                        :to="{name:'dashboard'}"
                        class="py-4 px-6  font-medium text-sm hover:bg-indigo-50 transition duration-200"
                    >
                        <span class="text-xl  text-gray-700">На главную</span>
                    </router-link>
                    <router-link
                        :to="{name:'posts'}"
                        class="py-4 px-6 font-medium text-sm hover:bg-indigo-50 transition duration-200"
                    >
                        <span class="text-xl  text-gray-700">Посты</span>
                    </router-link>
                </nav>
            </div>
            <!-- Вкладка с категориями -->

                <!-- Форма создания категории -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Создать новую категорию</h3>
                    <form @submit.prevent="createCategory" class="space-y-4">
                        <div>
                            <label for="category-title" class="block text-sm font-medium text-gray-700 mb-1">Название категории</label>
                            <input
                                type="text"
                                id="category-title"
                                v-model="newCategory.title"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите название категории"
                                required
                            >
                        </div>
                        <div>
                            <label for="category-description" class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                            <textarea
                                id="category-description"
                                v-model="newCategory.description"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите описание категории"
                                rows="3"
                            ></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
                                :disabled="categoryLoading"
                            >
                                <span v-if="categoryLoading">Создание...</span>
                                <span v-else>Создать категорию</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Список категорий -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-700">Категории</h3>
                        <div class="flex items-center">
                            <input
                                type="text"
                                v-model="categorySearch"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Поиск категорий"
                            >
                        </div>
                    </div>

                    <!-- Индикатор загрузки категорий -->
                    <div v-if="categoryLoading" class="flex justify-center my-8">
                        <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <!-- Таблица категорий -->
                    <div v-else-if="filteredCategories.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Кол-во постов</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата создания</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="category in filteredCategories" :key="category.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ category.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ category.title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="max-w-xs truncate">{{ category.description || 'Нет описания' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ category.postCount }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ formatDate(category.created_at) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <button @click="editCategory(category)" class="text-indigo-600 hover:text-indigo-900 mr-3">Редактировать</button>
                                    <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900">Удалить</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Нет категорий -->
                    <div v-else class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="mt-2 text-gray-600">{{ categorySearch ? 'Категории не найдены' : 'Нет категорий' }}</p>
                    </div>

                    <!-- Модальное окно редактирования категории -->
                    <div v-if="showCategoryModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Редактирование категории</h3>
                            <form @submit.prevent="updateCategory" class="space-y-4">
                                <div>
                                    <label for="edit-category-title" class="block text-sm font-medium text-gray-700 mb-1">Название категории</label>
                                    <input
                                        type="text"
                                        id="edit-category-title"
                                        v-model="editingCategory.title"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        required
                                    >
                                </div>
                                <div>
                                    <label for="edit-category-description" class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                                    <textarea
                                        id="edit-category-description"
                                        v-model="editingCategory.description"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        rows="3"
                                    ></textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button
                                        type="button"
                                        @click="showCategoryModal = false"
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"
                                    >
                                        Отмена
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
                                        :disabled="categoryLoading"
                                    >
                                        <span v-if="categoryLoading">Сохранение...</span>
                                        <span v-else>Сохранить</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </template>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted,onUnmounted, watch, computed } from 'vue';
import { useAuthStore } from '../../Stores/auth.js';
import axios from 'axios';

// Состояние аутентификации
const authStore = useAuthStore();

const echoChannels = ref({
    posts: null,
    categories: null
});

// Общее состояние компонента
const loading = ref(false);
const error = ref('');

// Статистика
const stats = reactive({
    categories: 0,
    categoriesGrowth: 12,
    posts: 0,
    postsGrowth: 8,
    views: 0,
    viewsGrowth: 24
});
const categories = ref([]);
const categorySearch = ref('');
const categoryLoading = ref(false);
const newCategory = reactive({
    title: '',
    description: ''
});
const editingCategory = reactive({
    id: null,
    title: '',
    description: ''
});
const showCategoryModal = ref(false);

// Отфильтрованные категории
const filteredCategories = computed(() => {
    if (!categorySearch.value) return categories.value;

    const search = categorySearch.value.toLowerCase();
    return categories.value.filter(
        category => category.title.toLowerCase().includes(search) ||
            (category.description && category.description.toLowerCase().includes(search))
    );
});


// Функция для инициализации подписок
const initEchoChannels = () => {
    if (!authStore.isAuthenticated) return;

    // Закрываем существующие подписки, если они есть
    closeEchoChannels();

    // Правильно подписываемся на приватный канал
    echoChannels.value.categories = window.Echo.channel('store-category')
        .listen('.store-category', (data) => {
            console.log('Новая категория получена:', data);
            handleCategoryCreated(data);
        });
};

const closeEchoChannels = () => {
    // Отписываемся от всех каналов
    if (echoChannels.value.categories) {
        window.Echo.leave('store-category');
        echoChannels.value.categories = null;
    }
};


const handleCategoryCreated = (event) => {
    console.log('Получены данные события:', event);

    try {
        if (event) {
            const newCategory = event;

            // Проверяем, нет ли уже такой категории в массиве (чтобы избежать дублирования)
            const exists = categories.value.some(cat => cat.id === newCategory.id);

            if (!exists) {
                // Добавляем postCount, если его нет
                if (newCategory.postCount === undefined) {
                    newCategory.postCount = 0;
                }

                // Добавляем новую категорию в список
                categories.value.push(newCategory);
                console.log('Категория добавлена в список', newCategory);

                // Обновляем статистику
                stats.categories = categories.value.length;
            }
        }
    } catch (err) {
        console.error('Ошибка при обработке данных категории:', err);
    }
};


// Загрузка категорий
const loadCategories = async () => {
    if (!authStore.isAuthenticated) return;

    categoryLoading.value = true;

    try {
        // Убедимся, что токен установлен в заголовках
        if (authStore.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        }

        // Получаем CSRF-cookie и делаем запрос
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.get('/api/v1/categories');

        if (response.data && response.data.data) {
            categories.value = response.data.data;
            stats.categories = categories.value.length;
        }
    } catch (err) {
        console.error('Error loading categories:', err);
        error.value = err.response?.data?.message || 'Ошибка при загрузке категорий';

        // Если ошибка авторизации, редиректим на логин
        if (err.response?.status === 401) {
            authStore.clearAuthState();
        }
    } finally {
        categoryLoading.value = false;
    }
};

// Создание новой категории
const createCategory = async () => {
    if (!authStore.isAuthenticated) return;

    categoryLoading.value = true;

    try {
        if (authStore.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        }

        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/api/v1/categories', {
            title: newCategory.title,
            description: newCategory.description
        });

        if (response) {
            // Добавляем новую категорию в список
            const newCat = response.data;
            newCat.postCount = 0;
            categories.value.push(newCat);

            // Обновляем статистику
            stats.categories = categories.value.length;

            // Очищаем форму
            newCategory.title = '';
            newCategory.description = '';
        }
    } catch (err) {
        console.error('Error creating category:', err);
        error.value = err.response?.data?.message || 'Ошибка при создании категории';
    } finally {
        categoryLoading.value = false;
    }
};

// Редактирование категории
const editCategory = (category) => {
    editingCategory.id = category.id;
    editingCategory.title = category.title;
    editingCategory.description = category.description || '';
    showCategoryModal.value = true;
};

// Обновление категории
// Обновление категории
const updateCategory = async () => {
    if (!authStore.isAuthenticated || !editingCategory.id) return;

    categoryLoading.value = true;

    try {
        if (authStore.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        }

        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.put(`/api/v1/categories/${editingCategory.id}`, {
            title: editingCategory.title,
            description: editingCategory.description
        });

        // Обновляем категорию в списке независимо от структуры ответа
        const index = categories.value.findIndex(c => c.id === editingCategory.id);
        if (index !== -1) {
            // Сохраняем существующие поля и обновляем только title и description
            categories.value[index] = {
                ...categories.value[index],
                title: editingCategory.title,
                description: editingCategory.description
            };

            // Закрываем модальное окно только при успешном обновлении
            showCategoryModal.value = false;
        }
    } catch (err) {
        console.error('Error updating category:', err);
        error.value = err.response?.data?.message || 'Ошибка при обновлении категории';
    } finally {
        categoryLoading.value = false;
    }
};

// Удаление категории
const deleteCategory = async (id) => {
    if (!authStore.isAuthenticated) return;

    if (!confirm('Вы действительно хотите удалить эту категорию? Все посты в этой категории также будут удалены.')) {
        return;
    }

    categoryLoading.value = true;

    try {
        if (authStore.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        }

        await axios.get('/sanctum/csrf-cookie');
        await axios.delete(`/api/v1/categories/${id}`);

        // Удаляем категорию из списка
        const index = categories.value.findIndex(c => c.id === id);
        if (index !== -1) {
            const categoryTitle = categories.value[index].title;
            categories.value.splice(index, 1);

            // Обновляем статистику
            stats.categories = categories.value.length;
        }
    } catch (err) {
        console.error('Error deleting category:', err);
        error.value = err.response?.data?.message || 'Ошибка при удалении категории';
    } finally {
        categoryLoading.value = false;
    }
};


// ============= Вспомогательные функции =============

// Получить имя категории по ID
const getcategoryTitle = (categoryId) => {
    const category = categories.value.find(c => c.id == categoryId);
    return category ? category.title : 'Без категории';
};

// Форматирование даты
const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    return new Intl.DateTimeFormat('ru-RU', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
};

// Обрезка текста
const truncateText = (text, maxLength) => {
    if (!text || text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};


// ============= Загрузка данных при монтировании =============
onMounted(async () => {
    if (authStore.isAuthenticated) {
        await Promise.all([
            initEchoChannels(),
            loadCategories(),
        ]);
    }
});


onUnmounted(() => {
    closeEchoChannels();
});

// Следим за изменением статуса аутентификации
watch(() => authStore.isAuthenticated, (isAuthenticated) => {
    if (isAuthenticated) {
        initEchoChannels();
        loadCategories();
    } else {
        closeEchoChannels();
        categories.value = [];
        error.value = '';
    }
});

</script>
