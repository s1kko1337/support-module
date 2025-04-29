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
                        :to="{name:'categories'}"
                        class="py-4 px-6  font-medium text-sm hover:bg-indigo-50 transition duration-200"
                    >
                            <span class="text-xl  text-gray-700">Категории</span>
                    </router-link>
                    <router-link
                        :to="{name:'posts'}"
                        class="py-4 px-6 font-medium text-sm hover:bg-indigo-50 transition duration-200"
                    >
                        <span class="text-xl  text-gray-700">Посты</span>
                    </router-link>
                </nav>
            </div>
                <!-- Карточки со статистикой -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg shadow-md border border-blue-200">
                        <div class="text-blue-700 text-lg font-semibold mb-2">Категории</div>
                        <div class="text-3xl font-bold text-gray-800">{{  }}</div>
                        <div class="text-blue-600 mt-4 text-sm">↑ {{  }}% за последний месяц</div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg shadow-md border border-green-200">
                        <div class="text-green-700 text-lg font-semibold mb-2">Посты</div>
                        <div class="text-3xl font-bold text-gray-800">{{  }}</div>
                        <div class="text-green-600 mt-4 text-sm">↑ {{  }}% за последний месяц</div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg shadow-md border border-purple-200">
                        <div class="text-purple-700 text-lg font-semibold mb-2">Просмотры</div>
                        <div class="text-3xl font-bold text-gray-800">{{  }}</div>
                        <div class="text-purple-600 mt-4 text-sm">↑ {{  }}% за последний месяц</div>
                    </div>
                </div>

                <!-- График активности -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Активность за последний месяц</h3>
                    <div class="h-64 bg-gray-50 rounded border border-gray-200 p-4">
                        <!-- Здесь можно добавить график активности с использованием библиотеки Chart.js или подобной -->
                        <div class="flex h-full items-end space-x-2">
<!--                            <div v-for="(value, index) in activityData" :key="index"-->
<!--                                 class="bg-indigo-500 rounded-t w-full"-->
<!--                                 :style="{ height: `${value}%` }"-->
<!--                                 :title="`День ${index + 1}: ${value}%`">-->
<!--                            </div>-->
                        </div>
                    </div>
                </div>

                <!-- Последние действия -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-700">Последние действия</h3>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        <li v-for="(activity, index) in recentActivities" :key="index" class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full"
                                    >
                                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ }}</p>
                                    <p class="text-sm text-gray-500">{{}}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
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

</script>

