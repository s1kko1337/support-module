
<template>
    <div class="bg-gradient-to-br from-indigo-50 via-white to-blue-50 min-h-screen">
        <!-- Хедер с анимированным фоном -->
        <header class="relative py-16 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-600 opacity-90"></div>
            <div class="absolute inset-0">
                <div class="wave-pattern"></div>
            </div>
            <div class="container mx-auto px-6 relative z-10">
                <h1 class="text-4xl md:text-5xl font-bold text-white text-center mb-4">
                    Условия пользования
                </h1>
                <p class="text-xl text-indigo-100 text-center max-w-3xl mx-auto">
                    Пожалуйста, внимательно ознакомьтесь с правилами использования нашего сервиса
                </p>
            </div>
        </header>

        <!-- Основное содержимое -->
        <main class="container mx-auto px-6 py-12">
            <!-- Навигация по разделам -->
            <div class="mb-12 sticky top-0 pt-4 pb-4 bg-white bg-opacity-90 backdrop-blur-sm rounded-xl shadow-sm z-20 transition-all duration-300"
                 :class="{ 'shadow-md': isScrolled }">
                <nav class="flex flex-wrap justify-center gap-2 md:gap-6">
                    <button v-for="(section, index) in sections"
                            :key="index"
                            @click="scrollToSection(section.id)"
                            class="px-4 py-2 rounded-lg text-sm md:text-base transition-all duration-300"
                            :class="activeSection === section.id ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-indigo-100'">
                        {{ section.title }}
                    </button>
                </nav>
            </div>

            <!-- Содержимое разделов -->
            <div class="space-y-20 mb-20">
                <section v-for="(section, index) in sections"
                         :id="section.id"
                         :key="index"
                         class="scroll-mt-32">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="h-2 bg-gradient-to-r" :class="section.gradient"></div>
                        <div class="p-8 md:p-10">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="w-10 h-10 flex items-center justify-center rounded-full mr-4 flex-shrink-0"
                      :class="section.iconBg">
                  <i :class="section.icon + ' text-white'"></i>
                </span>
                                {{ section.title }}
                            </h2>

                            <!-- Здесь будет содержимое раздела -->
                            <div class="prose prose-indigo max-w-none">
                                <p class="text-gray-500 italic mb-8">{{ section.description }}</p>

                                <!-- Плейсхолдер для контента -->
                                <div class="space-y-4 relative">
                                    <div v-for="i in 3" :key="i" class="h-6 bg-gray-100 rounded animate-pulse"></div>
                                    <div class="h-6 bg-gray-100 rounded w-3/4 animate-pulse"></div>
                                    <div v-for="i in 2" :key="i+3" class="h-6 bg-gray-100 rounded animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- FAQ секция -->
            <section class="bg-indigo-50 rounded-3xl p-8 md:p-12 mb-20">
                <h2 class="text-3xl font-bold text-center mb-12">Часто задаваемые вопросы</h2>

                <div class="space-y-6 max-w-4xl mx-auto">
                    <div v-for="(faq, index) in faqs"
                         :key="index"
                         class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-500"
                         :class="{ 'shadow-md': faq.isOpen }">
                        <button @click="toggleFaq(index)"
                                class="w-full px-6 py-5 text-left flex justify-between items-center hover:bg-gray-50 transition-colors duration-300">
                            <span class="font-medium text-lg text-gray-800">{{ faq.question }}</span>
                            <span class="transform transition-transform duration-300" :class="{ 'rotate-180': faq.isOpen }">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
                        </button>
                        <div v-show="faq.isOpen"
                             class="px-6 pb-6 text-gray-600">
                            <p>{{ faq.answer }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Секция согласия -->
            <section class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl p-8 text-center mb-20">
                <h2 class="text-2xl font-bold mb-6">Согласны ли вы с условиями использования?</h2>
                <p class="text-gray-600 mb-8">Нажимая "Принимаю", вы подтверждаете, что прочитали и согласны со всеми условиями использования нашего сервиса.</p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                            @click="acceptTerms">
                        Принимаю условия
                    </button>
                    <button class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-all duration-300"
                            @click="declineTerms">
                        Отклонить
                    </button>
                </div>
            </section>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isScrolled = ref(false);
const activeSection = ref('overview');

// Разделы условий использования
const sections = [
    {
        id: 'overview',
        title: 'Общие положения',
        description: 'Основные принципы и определения, описывающие взаимоотношения между пользователем и сервисом.',
        icon: 'fas fa-info-circle',
        iconBg: 'bg-blue-500',
        gradient: 'from-blue-400 to-blue-600'
    },
    {
        id: 'privacy',
        title: 'Конфиденциальность',
        description: 'Информация о том, как мы собираем, храним и используем ваши персональные данные.',
        icon: 'fas fa-shield-alt',
        iconBg: 'bg-green-500',
        gradient: 'from-green-400 to-green-600'
    },
    {
        id: 'usage',
        title: 'Правила использования',
        description: 'Разрешенные и запрещенные действия при использовании нашего сервиса.',
        icon: 'fas fa-check-circle',
        iconBg: 'bg-yellow-500',
        gradient: 'from-yellow-400 to-yellow-600'
    },
    {
        id: 'rights',
        title: 'Права и обязанности',
        description: 'Права и обязанности пользователей и администрации сервиса.',
        icon: 'fas fa-balance-scale',
        iconBg: 'bg-red-500',
        gradient: 'from-red-400 to-red-600'
    },
    {
        id: 'termination',
        title: 'Прекращение обслуживания',
        description: 'Условия, при которых доступ к сервису может быть ограничен или прекращен.',
        icon: 'fas fa-times-circle',
        iconBg: 'bg-purple-500',
        gradient: 'from-purple-400 to-purple-600'
    }
];

// FAQ
const faqs = ref([
    {
        question: 'Как я могу изменить свои персональные данные?',
        answer: 'Вы можете изменить свои персональные данные в разделе "Профиль" после входа в систему. Все изменения вступят в силу немедленно.',
        isOpen: false
    },
    {
        question: 'Как долго хранятся мои данные?',
        answer: 'Мы храним ваши данные до тех пор, пока вы пользуетесь нашим сервисом. После удаления аккаунта все персональные данные удаляются в течение 30 дней.',
        isOpen: false
    },
    {
        question: 'Могу ли я отозвать свое согласие?',
        answer: 'Да, вы можете отозвать свое согласие в любой момент, обратившись в службу поддержки или удалив свой аккаунт.',
        isOpen: false
    },
    {
        question: 'Что произойдет при нарушении правил?',
        answer: 'При нарушении правил использования сервиса мы оставляем за собой право ограничить или прекратить предоставление услуг без предварительного уведомления.',
        isOpen: false
    },
    {
        question: 'Как часто обновляются условия пользования?',
        answer: 'Мы регулярно обновляем наши условия пользования для соответствия текущему законодательству и улучшения сервиса. Обо всех существенных изменениях мы уведомляем пользователей.',
        isOpen: false
    }
]);

// Методы
const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};

const toggleFaq = (index) => {
    faqs.value[index].isOpen = !faqs.value[index].isOpen;
};

const handleScroll = () => {
    // Проверка прокрутки для изменения стиля навигации
    isScrolled.value = window.scrollY > 100;

    // Определение активного раздела на основе позиции прокрутки
    for (const section of sections) {
        const element = document.getElementById(section.id);
        if (element) {
            const rect = element.getBoundingClientRect();
            if (rect.top <= 150 && rect.bottom >= 150) {
                activeSection.value = section.id;
                break;
            }
        }
    }
};

// Обработка согласия с условиями
const acceptTerms = () => {
    localStorage.setItem('termsAccepted', 'true');
    router.back();
};

const declineTerms = () => {
    alert('Для использования сервиса необходимо принять условия пользования.');
};

// Жизненный цикл компонента
onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
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

/* Улучшение внешнего вида прокрутки */
html {
    scroll-behavior: smooth;
}

::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: #818cf8;
}

/* Анимация появления при прокрутке */
.scroll-fade-in {
    opacity: 0;
    transform: translateY(50px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.scroll-fade-in.visible {
    opacity: 1;
    transform: translateY(0);
}
</style>
