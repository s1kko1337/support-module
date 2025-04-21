
<template>
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Todo / Заметки</h1>

        <!-- Секция загрузки файла -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Загрузка Markdown файла</h2>
            <div class="flex items-center">
                <label for="markdown-file-input" class="cursor-pointer bg-indigo-100 hover:bg-indigo-200 text-indigo-700 py-2 px-4 rounded-lg transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Выбрать файл
                </label>
                <input
                    id="markdown-file-input"
                    type="file"
                    accept=".md, .markdown, .txt"
                    class="hidden"
                    @change="handleFileUpload"
                >
                <span class="ml-4 text-gray-600">{{ fileName || 'Файл не выбран' }}</span>
            </div>

            <div class="mt-4">
                <p class="text-sm text-gray-600">или вставьте Markdown текст вручную:</p>
                <textarea
                    v-model="markdownText"
                    class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    rows="5"
                    placeholder="# Заголовок&#10;&#10;Текст на **Markdown**&#10;&#10;* Список&#10;* Элементов&#10;&#10;```javascript&#10;// Код с подсветкой синтаксиса&#10;function hello() {&#10;  console.log('Hello world');&#10;}&#10;```"
                ></textarea>
                <div class="flex justify-between mt-2">
                    <button
                        @click="clearMarkdown"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"
                    >
                        Очистить
                    </button>
                    <button
                        @click="renderMarkdown"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
                    >
                        Отобразить
                    </button>
                </div>
            </div>
        </div>

        <!-- Пример карточки Todo / Предпросмотр -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" v-if="previewMode">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-700">Предпросмотр</h3>
                <button
                    @click="previewMode = false"
                    class="text-gray-500 hover:text-gray-700"
                    title="Закрыть предпросмотр"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="p-6 markdown-body" v-html="renderedMarkdown"></div>
        </div>

        <!-- Сохраненные заметки -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden" v-if="savedNotes.length > 0">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-700">Сохраненные заметки</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <div v-for="(note, index) in savedNotes" :key="index" class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-medium text-gray-900">{{ note.title || `Заметка ${index + 1}` }}</h4>
                        <div class="flex space-x-2">
                            <button
                                @click="editNote(index)"
                                class="text-indigo-600 hover:text-indigo-800"
                                title="Редактировать"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                @click="deleteNote(index)"
                                class="text-red-600 hover:text-red-800"
                                title="Удалить"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="markdown-body" v-html="note.rendered"></div>
                </div>
            </div>
        </div>

        <!-- Сохранение заметки (диалог) -->
        <div
            v-if="showSaveDialog"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Сохранить заметку</h3>
                <div class="mb-4">
                    <label for="note-title" class="block text-sm font-medium text-gray-700 mb-1">Название заметки</label>
                    <input
                        id="note-title"
                        v-model="noteTitle"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Введите название заметки"
                    >
                </div>
                <div class="flex justify-end space-x-2">
                    <button
                        @click="showSaveDialog = false"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"
                    >
                        Отмена
                    </button>
                    <button
                        @click="saveNote"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

// Состояния
const markdownText = ref('');
const renderedMarkdown = ref('');
const fileName = ref('');
const previewMode = ref(false);
const savedNotes = ref([]);
const showSaveDialog = ref(false);
const noteTitle = ref('');
const editingNoteIndex = ref(-1);

// Загрузка сохраненных заметок из localStorage
onMounted(() => {
    try {
        const notes = localStorage.getItem('markdown-notes');
        if (notes) {
            savedNotes.value = JSON.parse(notes);
        }
    } catch (error) {
        console.error('Ошибка при загрузке заметок:', error);
    }

    // Настройка marked для подсветки синтаксиса
    setupMarked();
});

// Настройка библиотеки marked
const setupMarked = () => {
    // Для более продвинутой настройки можно добавить различные опции
    marked.setOptions({
        breaks: true, // Переносы строк как в исходнике
        gfm: true,    // GitHub Flavored Markdown
    });
};

// Обработчик загрузки файла
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    fileName.value = file.name;
    const reader = new FileReader();

    reader.onload = (e) => {
        markdownText.value = e.target.result;
        renderMarkdown();
    };

    reader.readAsText(file);
};

// Рендеринг markdown в HTML
const renderMarkdown = () => {
    if (!markdownText.value.trim()) {
        renderedMarkdown.value = '<p class="text-gray-500">Нет содержимого для отображения.</p>';
        return;
    }

    try {
        // Преобразуем markdown в HTML и очищаем от потенциально опасных тегов
        const html = marked(markdownText.value);
        renderedMarkdown.value = DOMPurify.sanitize(html);
        previewMode.value = true;

        // Показать диалог для сохранения заметки
        showSaveDialog.value = true;
    } catch (error) {
        console.error('Ошибка при рендеринге markdown:', error);
        renderedMarkdown.value = `<p class="text-red-500">Ошибка при обработке Markdown: ${error.message}</p>`;
    }
};

// Очистка поля с markdown
const clearMarkdown = () => {
    markdownText.value = '';
    fileName.value = '';
    previewMode.value = false;
};

// Сохранение заметки
const saveNote = () => {
    const note = {
        title: noteTitle.value || fileName.value || `Заметка ${savedNotes.value.length + 1}`,
        content: markdownText.value,
        rendered: renderedMarkdown.value,
        created: new Date().toISOString()
    };

    if (editingNoteIndex.value >= 0) {
        // Редактирование существующей заметки
        savedNotes.value[editingNoteIndex.value] = note;
    } else {
        // Создание новой заметки
        savedNotes.value.push(note);
    }

    // Сохраняем в localStorage
    localStorage.setItem('markdown-notes', JSON.stringify(savedNotes.value));

    // Сбрасываем состояния
    showSaveDialog.value = false;
    noteTitle.value = '';
    editingNoteIndex.value = -1;
};

// Редактирование заметки
const editNote = (index) => {
    const note = savedNotes.value[index];
    markdownText.value = note.content;
    noteTitle.value = note.title;
    renderedMarkdown.value = note.rendered;
    previewMode.value = true;
    editingNoteIndex.value = index;
    showSaveDialog.value = true;
};

// Удаление заметки
const deleteNote = (index) => {
    if (confirm('Вы уверены, что хотите удалить эту заметку?')) {
        savedNotes.value.splice(index, 1);
        localStorage.setItem('markdown-notes', JSON.stringify(savedNotes.value));
    }
};
</script>

<style>
/* Стили для отображения Markdown, базирующиеся на GitHub Markdown стилях */
.markdown-body {
    font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Helvetica, Arial, sans-serif;
    font-size: 16px;
    line-height: 1.6;
    word-wrap: break-word;
}

.markdown-body h1 {
    font-size: 2em;
    margin-top: 0.67em;
    margin-bottom: 0.67em;
    font-weight: 600;
    padding-bottom: 0.3em;
    border-bottom: 1px solid #eaecef;
}

.markdown-body h2 {
    font-size: 1.5em;
    margin-top: 1em;
    margin-bottom: 0.5em;
    font-weight: 600;
    padding-bottom: 0.3em;
    border-bottom: 1px solid #eaecef;
}

.markdown-body h3 {
    font-size: 1.25em;
    margin-top: 1em;
    margin-bottom: 0.5em;
    font-weight: 600;
}

.markdown-body h4 {
    font-size: 1em;
    margin-top: 1em;
    margin-bottom: 0.5em;
    font-weight: 600;
}

.markdown-body p {
    margin-top: 0;
    margin-bottom: 1em;
}

.markdown-body a {
    color: #0366d6;
    text-decoration: none;
}

.markdown-body a:hover {
    text-decoration: underline;
}

.markdown-body strong {
    font-weight: 600;
}

.markdown-body em {
    font-style: italic;
}

.markdown-body code {
    font-family: SFMono-Regular, Consolas, Liberation Mono, Menlo, monospace;
    font-size: 0.85em;
    padding: 0.2em 0.4em;
    margin: 0;
    background-color: rgba(27, 31, 35, 0.05);
    border-radius: 3px;
}

.markdown-body pre {
    font-family: SFMono-Regular, Consolas, Liberation Mono, Menlo, monospace;
    font-size: 0.85em;
    padding: 1em;
    overflow: auto;
    background-color: #f6f8fa;
    border-radius: 6px;
    margin: 0.5em 0 1em;
}

.markdown-body pre code {
    background-color: transparent;
    padding: 0;
}

.markdown-body blockquote {
    padding: 0 1em;
    color: #6a737d;
    border-left: 0.25em solid #dfe2e5;
    margin: 0 0 1em;
}

.markdown-body ul,
.markdown-body ol {
    padding-left: 2em;
    margin-top: 0;
    margin-bottom: 1em;
}

.markdown-body li {
    margin-top: 0.25em;
}

.markdown-body table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 1em;
}

.markdown-body table th,
.markdown-body table td {
    padding: 0.5em 1em;
    border: 1px solid #dfe2e5;
}

.markdown-body table th {
    font-weight: 600;
    background-color: #f6f8fa;
}

.markdown-body hr {
    height: 0.25em;
    padding: 0;
    margin: 24px 0;
    background-color: #e1e4e8;
    border: 0;
}

.markdown-body img {
    max-width: 100%;
    box-sizing: content-box;
}
</style>
