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
                        :to="{name:'categories'}"
                        class="py-4 px-6 font-medium text-sm hover:bg-indigo-50 transition duration-200"
                    >
                        <span class="text-xl  text-gray-700">Категории</span>
                    </router-link>
                </nav>
            </div>
                <!-- Форма создания поста -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Создать новый пост</h3>
                    <form @submit.prevent="createPost" class="space-y-4">
                        <div>
                            <label for="post-title" class="block text-sm font-medium text-gray-700 mb-1">Заголовок</label>
                            <input
                                type="text"
                                id="post-title"
                                v-model="newPost.title"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите заголовок поста"
                                required
                            >
                        </div>
                        <div>
                            <label for="post-category" class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                            <select
                                id="post-category"
                                v-model="newPost.category_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                required
                            >
                                <option value="" disabled>Выберите категорию</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.title }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label for="post-content" class="block text-sm font-medium text-gray-700 mb-1">Содержание</label>
                            <textarea
                                id="post-content"
                                v-model="newPost.content"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Введите содержание поста"
                                rows="6"
                                required
                            ></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
                                :disabled="postLoading"
                            >
                                <span v-if="postLoading">Создание...</span>
                                <span v-else>Опубликовать пост</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Список постов -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-700">Посты</h3>
                        <div class="flex items-center space-x-2">
                            <select
                                v-model="postFilter.category"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                                <option value="">Все категории</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.title }}
                                </option>
                            </select>
                            <input
                                type="text"
                                v-model="postFilter.search"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Поиск постов"
                            >
                        </div>
                    </div>

                    <!-- Индикатор загрузки постов -->
                    <div v-if="postLoading" class="flex justify-center my-8">
                        <svg class="animate-spin h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <!-- Список постов -->
                    <div v-else-if="filteredPosts.length > 0">
                        <div class="divide-y divide-gray-200">
                            <div v-for="post in filteredPosts" :key="post.id" class="p-6">
                                <div class="flex justify-between">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900 mb-1">{{ post.title }}</h4>
                                        <div class="flex items-center text-sm text-gray-500 mb-3 space-x-3">
                                            <span>{{ getcategoryTitle(post.category_id) }}</span>
                                            <span>{{ formatDate(post.created_at) }}</span>
                                            <span>{{ post.views }} просмотров</span>
                                        </div>
                                        <p class="text-gray-700 mb-4">{{ truncateText(post.content, 200) }}</p>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <button @click="editPost(post)" class="text-indigo-600 hover:text-indigo-900">Редактировать</button>
                                        <button @click="deletePost(post.id)" class="text-red-600 hover:text-red-900">Удалить</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Пагинация -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                            <button
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                :disabled="postPage === 1"
                                @click="changePostPage(postPage - 1)"
                            >
                                Предыдущая
                            </button>
                            <div class="text-sm text-gray-700">
                                Страница {{ postPage }} из {{ postTotalPages }}
                            </div>
                            <button
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                :disabled="postPage === postTotalPages"
                                @click="changePostPage(postPage + 1)"
                            >
                                Следующая
                            </button>
                        </div>
                    </div>

                    <!-- Нет постов -->
                    <div v-else class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5M7 14l2-2 2 2M7 14v5"></path>
                        </svg>
                        <p class="mt-2 text-gray-600">
                            {{ postFilter.search || postFilter.category ? 'Посты не найдены' : 'Нет опубликованных постов' }}
                        </p>
                    </div>

                    <!-- Модальное окно редактирования поста -->
                    <div v-if="showPostModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Редактирование поста</h3>
                            <form @submit.prevent="updatePost" class="space-y-4">
                                <div>
                                    <label for="edit-post-title" class="block text-sm font-medium text-gray-700 mb-1">Заголовок</label>
                                    <input
                                        type="text"
                                        id="edit-post-title"
                                        v-model="editingPost.title"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        required
                                    >
                                </div>
                                <div>
                                    <label for="edit-post-category" class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                                    <select
                                        id="edit-post-category"
                                        v-model="editingPost.category_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        required
                                    >
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.title }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit-post-content" class="block text-sm font-medium text-gray-700 mb-1">Содержание</label>
                                    <textarea
                                        id="edit-post-content"
                                        v-model="editingPost.content"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        rows="8"
                                        required
                                    ></textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button
                                        type="button"
                                        @click="showPostModal = false"
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"
                                    >
                                        Отмена
                                    </button>
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
                                        :disabled="postLoading"
                                    >
                                        <span v-if="postLoading">Сохранение...</span>
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
const activeTab = ref('dashboard');
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

// Данные для графика активности (последние 30 дней)
const activityData = ref(Array.from({ length: 30 }, () => Math.floor(Math.random() * 100)));

// Последние действия пользователей
const recentActivities = ref([
    {
        type: 'post',
        description: 'Опубликован новый пост "Добро пожаловать в блог"',
        date: new Date(Date.now() - 1000 * 60 * 60 * 5)
    },
]);

// Иконки для действий
const getActivityIcon = (type) => {
    switch (type) {
        case 'category':
            return 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10';
        case 'post':
            return 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';
        case 'edit':
            return 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z';
        case 'delete':
            return 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16';
        default:
            return 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }
};

// Классы для иконок действий
const getActivityIconClass = (type) => {
    switch (type) {
        case 'category':
            return 'bg-blue-500';
        case 'post':
            return 'bg-green-500';
        case 'edit':
            return 'bg-yellow-500';
        case 'delete':
            return 'bg-red-500';
        default:
            return 'bg-gray-500';
    }
};

// Функция для инициализации подписок
const initEchoChannels = () => {
    if (!authStore.isAuthenticated) return;

    // Закрываем существующие подписки, если они есть
    closeEchoChannels();

    echoChannels.value.posts = window.Echo.channel('store-post')
        // Слушаем событие PostCreated (или используйте имя в соответствии с вашим серверным кодом)
        .listen('.store-post', (data) => {
            console.log('Новый пост получен:', data);
            // Обработка события создания поста
            handlePostCreated(data);
        })
};

// Закрытие подписок при размонтировании компонента
const closeEchoChannels = () => {
    // Отписываемся от всех каналов
    if (echoChannels.value.posts) {
        window.Echo.leave('store-post');
        echoChannels.value.posts = null;
    }
};

// Обработчики событий
const handlePostCreated = (data) => {
    // Предполагаем, что сервер отправляет данные поста в корне объекта или в свойстве data.post
    const post = data.post || data;

    if (!post || !post.id) {
        console.error('Получены некорректные данные поста:', data);
        return;
    }

    // Проверяем, не существует ли уже такой пост (избегаем дубликатов)
    if (posts.value.some(p => p.id === post.id)) {
        return;
    }

    // Вставляем новый пост в начало списка
    posts.value.unshift(post);

    // Если у нас активна фильтрация по категории, и пост не соответствует фильтру,
    // он автоматически будет скрыт благодаря computed-свойству filteredPosts

    // Обновляем счетчик
    stats.posts++;

    // Обновляем счетчик постов в категории, если категория существует
    const categoryIndex = categories.value.findIndex(c => c.id == post.category_id);
    if (categoryIndex !== -1) {
        categories.value[categoryIndex].postCount++;
    }

    // Добавляем в историю активности
    addActivity('post', `Получен новый пост: "${post.title}"`);
};

const handlePostUpdated = (post) => {
    // Находим и обновляем измененный пост
    const index = posts.value.findIndex(p => p.id === post.id);
    if (index !== -1) {
        posts.value[index] = post;
        addActivity('edit', `Обновлен пост: "${post.title}"`);
    }
};

const handlePostDeleted = (postId) => {
    // Находим и удаляем пост
    const index = posts.value.findIndex(p => p.id === postId);
    if (index !== -1) {
        const postTitle = posts.value[index].title;
        posts.value.splice(index, 1);
        stats.posts--;
        addActivity('delete', `Удален пост: "${postTitle}"`);
    }
};




//  Категории
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
        } else {
            // Демо-данные для тестирования
            categories.value = [
                {
                    id: 1,
                    title: 'Новости',
                    description: 'Актуальные новости и обновления',
                    postCount: 5,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 24 * 30)
                },
                {
                    id: 2,
                    title: 'Руководства',
                    description: 'Обучающие материалы и инструкции',
                    postCount: 3,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 24 * 20)
                },
                {
                    id: 3,
                    title: 'Обсуждения',
                    description: 'Темы для обсуждения с сообществом',
                    postCount: 8,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 24 * 10)
                }
            ];
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

        if (response.data && response.data.data) {
            // Добавляем новую категорию в список
            const newCat = response.data.data;
            newCat.postCount = 0;
            categories.value.push(newCat);

            // Обновляем статистику
            stats.categories = categories.value.length;

            // Очищаем форму
            newCategory.title = '';
            newCategory.description = '';

            // Добавляем действие в историю
            addActivity('category', `Создана новая категория "${newCat.title}"`);
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

        if (response.data && response.data.data) {
            // Обновляем категорию в списке
            const index = categories.value.findIndex(c => c.id === editingCategory.id);
            if (index !== -1) {
                const updatedCategory = {
                    ...categories.value[index],
                    title: editingCategory.title,
                    description: editingCategory.description
                };
                categories.value.splice(index, 1, updatedCategory);

                // Добавляем действие в историю
                addActivity('edit', `Отредактирована категория "${editingCategory.title}"`);
            }

            // Закрываем модальное окно
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

            // Добавляем действие в историю
            addActivity('delete', `Удалена категория "${categoryTitle}"`);
        }
    } catch (err) {
        console.error('Error deleting category:', err);
        error.value = err.response?.data?.message || 'Ошибка при удалении категории';
    } finally {
        categoryLoading.value = false;
    }
};

// Посты
const posts = ref([]);
const postLoading = ref(false);
const postPage = ref(1);
const postTotalPages = ref(1);
const postFilter = reactive({
    search: '',
    category: ''
});
const newPost = reactive({
    title: '',
    content: '',
    category_id: ''
});
const editingPost = reactive({
    id: null,
    title: '',
    content: '',
    category_id: ''
});
const showPostModal = ref(false);

// Отфильтрованные посты
const filteredPosts = computed(() => {
    let filtered = posts.value;

    // Фильтр по категории
    if (postFilter.category) {
        filtered = filtered.filter(post => post.category_id == postFilter.category);
    }

    // Фильтр по поисковому запросу
    if (postFilter.search) {
        const search = postFilter.search.toLowerCase();
        filtered = filtered.filter(
            post => post.title.toLowerCase().includes(search) ||
                post.content.toLowerCase().includes(search)
        );
    }

    return filtered;
});

// Загрузка постов
const loadPosts = async (page = 1) => {
    if (!authStore.isAuthenticated) return;

    postLoading.value = true;

    try {
        // Убедимся, что токен установлен в заголовках
        if (authStore.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        }

        // Получаем CSRF-cookie и делаем запрос
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.get('/api/v1/posts', {
            params: {
                page,
                per_page: 10,
                category_id: postFilter.category || undefined
            }
        });

        if (response.data && response.data.data) {
            posts.value = response.data.data;
            postPage.value = response.data.current_page || page;
            postTotalPages.value = response.data.last_page || 1;
            stats.posts = response.data.total || posts.value.length;
        } else {
            posts.value = Array.from({ length: 10 }, (_, i) => ({
                id: i + 1,
                title: `Пост ${i + 1}`,
                content: `Это содержимое поста ${i + 1}. Здесь может быть длинный текст с описанием.`,
                category_id: [1, 2, 3][Math.floor(Math.random() * 3)],
                views: Math.floor(Math.random() * 100),
                created_at: new Date(Date.now() - Math.floor(Math.random() * 30) * 1000 * 60 * 60 * 24)
            }));
            postPage.value = page;
            postTotalPages.value = 5;
            stats.posts = 50; // Общее количество постов
        }

        // Суммируем просмотры
        stats.views = posts.value.reduce((sum, post) => sum + (post.views || 0), 0);
    } catch (err) {
        console.error('Error loading posts:', err);
        error.value = err.response?.data?.message || 'Ошибка при загрузке постов';

        // Если ошибка авторизации, редиректим на логин
        if (err.response?.status === 401) {
            authStore.clearAuthState();
        }
    } finally {
        postLoading.value = false;
    }
};

// Изменение страницы постов
const changePostPage = (page) => {
    if (page > 0 && page <= postTotalPages.value) {
        postPage.value = page;
        loadPosts(page);
    }
};

// Создание нового поста
const createPost = async () => {
    if (!authStore.isAuthenticated) return;

    postLoading.value = true;

    try {
        if (authStore.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        }

        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/api/v1/posts', {
            title: newPost.title,
            content: newPost.content,
            category_id: newPost.category_id
        });

        if (response.data && response.data.data) {
            // Добавляем новый пост в список, если мы на первой странице
            if (postPage.value === 1) {
                const newPostData = response.data.data;
                newPostData.views = 0;
                posts.value.unshift(newPostData);

                // Если у нас больше постов, чем нужно на странице, удаляем последний
                if (posts.value.length > 10) {
                    posts.value.pop();
                }
            }

            // Обновляем статистику
            stats.posts++;

            // Обновляем счетчик постов в категории
            const categoryIndex = categories.value.findIndex(c => c.id == newPost.category_id);
            if (categoryIndex !== -1) {
                categories.value[categoryIndex].postCount++;
            }

            // Очищаем форму
            newPost.title = '';
            newPost.content = '';
            newPost.category_id = '';

            // Добавляем действие в историю
            const categoryTitle = getcategoryTitle(newPost.category_id);
            addActivity('post', `Опубликован новый пост "${response.data.data.title}" в категории "${categoryTitle}"`);
        }
    } catch (err) {
        console.error('Error creating post:', err);
        error.value = err.response?.data?.message || 'Ошибка при создании поста';
    } finally {
        postLoading.value = false;
    }
};

// Редактирование поста
const editPost = (post) => {
    editingPost.id = post.id;
    editingPost.title = post.title;
    editingPost.content = post.content;
    editingPost.category_id = post.category_id;
    showPostModal.value = true;
};

// Обновление поста
const updatePost = async () => {
    if (!authStore.isAuthenticated || !editingPost.id) return;

    postLoading.value = true;

    try {
        if (authStore.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        }

        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.put(`/api/v1/posts/${editingPost.id}`, {
            title: editingPost.title,
            content: editingPost.content,
            category_id: editingPost.category_id
        });

        if (response.data && response.data.data) {
            // Обновляем пост в списке
            const index = posts.value.findIndex(p => p.id === editingPost.id);
            if (index !== -1) {
                // Запоминаем старую категорию для обновления счетчиков
                const oldCategoryId = posts.value[index].category_id;
                const newCategoryId = editingPost.category_id;

                // Обновляем пост
                const updatedPost = {
                    ...posts.value[index],
                    title: editingPost.title,
                    content: editingPost.content,
                    category_id: editingPost.category_id
                };
                posts.value.splice(index, 1, updatedPost);

                // Если категория изменилась, обновляем счетчики постов в категориях
                if (oldCategoryId != newCategoryId) {
                    // Уменьшаем счетчик в старой категории
                    const oldCategoryIndex = categories.value.findIndex(c => c.id == oldCategoryId);
                    if (oldCategoryIndex !== -1) {
                        categories.value[oldCategoryIndex].postCount--;
                    }

                    // Увеличиваем счетчик в новой категории
                    const newCategoryIndex = categories.value.findIndex(c => c.id == newCategoryId);
                    if (newCategoryIndex !== -1) {
                        categories.value[newCategoryIndex].postCount++;
                    }
                }

                // Добавляем действие в историю
                addActivity('edit', `Отредактирован пост "${editingPost.title}"`);
            }

            // Закрываем модальное окно
            showPostModal.value = false;
        }
    } catch (err) {
        console.error('Error updating post:', err);
        error.value = err.response?.data?.message || 'Ошибка при обновлении поста';
    } finally {
        postLoading.value = false;
    }
};

// Удаление поста
const deletePost = async (id) => {
    if (!authStore.isAuthenticated) return;

    if (!confirm('Вы действительно хотите удалить этот пост?')) {
        return;
    }

    postLoading.value = true;

    try {
        if (authStore.token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`;
        }

        await axios.get('/sanctum/csrf-cookie');
        await axios.delete(`/api/v1/posts/${id}`);

        // Удаляем пост из списка
        const index = posts.value.findIndex(p => p.id === id);
        if (index !== -1) {
            const postTitle = posts.value[index].title;
            const categoryId = posts.value[index].category_id;

            // Удаляем пост
            posts.value.splice(index, 1);

            // Обновляем статистику
            stats.posts--;

            // Уменьшаем счетчик постов в категории
            const categoryIndex = categories.value.findIndex(c => c.id == categoryId);
            if (categoryIndex !== -1) {
                categories.value[categoryIndex].postCount--;
            }

            // Добавляем действие в историю
            addActivity('delete', `Удален пост "${postTitle}"`);
        }
    } catch (err) {
        console.error('Error deleting post:', err);
        error.value = err.response?.data?.message || 'Ошибка при удалении поста';
    } finally {
        postLoading.value = false;
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

// Добавление записи активности
const addActivity = (type, description) => {
    recentActivities.value.unshift({
        type,
        description,
        date: new Date()
    });

    // Ограничиваем количество записей
    if (recentActivities.value.length > 10) {
        recentActivities.value.pop();
    }

    // Обновляем данные графика активности
    // В реальном приложении здесь бы обновлялись данные с сервера
    activityData.value = activityData.value.slice(1);
    activityData.value.push(Math.floor(Math.random() * 100));
};

// ============= Загрузка данных при монтировании =============
onMounted(async () => {
    if (authStore.isAuthenticated) {
        await Promise.all([
            initEchoChannels(),
            loadCategories(),
            loadPosts()
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
        loadPosts();
    } else {
        closeEchoChannels();
        categories.value = [];
        posts.value = [];
        error.value = '';
    }
});

// Следим за изменениями фильтров постов
watch(postFilter, () => {
    loadPosts(1);
}, { deep: true });
</script>
