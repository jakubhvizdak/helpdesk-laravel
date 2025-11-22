<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">
        <Navbar />

        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <button
                @click="$router.push(`/project/${projectId}`)"
                class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors mb-4"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="font-medium">Späť</span>
            </button>
            <div v-if="loading" class="flex justify-center items-center h-64">
                <svg class="w-12 h-12 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span class="ml-2 text-blue-600 font-medium"></span>
            </div>
            <div v-else>
            <div class="flex flex-col lg:flex-row gap-6">
                <aside class="lg:w-80 bg-white shadow-lg rounded-xl border border-gray-200 p-5 lg:sticky lg:top-6 lg:h-[calc(100vh-8rem)]">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Sekcie
                        </h2>
                        <button
                            v-if="canEdit"
                            @click="addSection"
                            class="text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 px-3 py-1.5 rounded-lg text-sm font-medium shadow-sm transition-all flex items-center gap-1"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Pridať
                        </button>
                    </div>

                    <ConfirmModal
                        v-if="confirmModalVisible"
                        :message="confirmModalMessage"
                        @confirm="onConfirm"
                        @cancel="onCancel"
                    />
                    <div class="space-y-2 overflow-y-auto max-h-[calc(100vh-16rem)]">
                        <button
                            v-for="(s, index) in sections"
                            :key="s.id"
                            @click="selectSection(s)"
                            :class="[
                                'w-full text-left px-4 py-3 rounded-lg text-sm transition-all group',
                                selectedSection?.id === s.id
                                    ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md'
                                    : 'text-gray-700 hover:bg-gray-100 border border-gray-200'
                            ]"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ index + 1 }}.</span>
                                    <span :class="selectedSection?.id === s.id ? 'font-semibold' : ''">
                                        {{ s.title || 'Bez názvu' }}
                                    </span>
                                </div>
                                <svg
                                    v-if="selectedSection?.id === s.id"
                                    class="w-4 h-4"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </button>

                        <div v-if="sections.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm">Žiadne sekcie</p>
                        </div>
                    </div>
                </aside>

                <div class="flex-1 bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Dokumentácia projektu
                            </h1>
                            <p v-if="saving" class="text-blue-600 text-sm mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Ukladám...
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                v-if="!editMode && canEdit"
                                @click="startEdit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Upraviť
                            </button>
                            <button
                                v-if="editMode"
                                @click="saveAndExit"
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Uložiť
                            </button>
                            <button
                                v-if="editMode"
                                @click="cancelEdit"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg font-medium transition-all"
                            >
                                Zrušiť
                            </button>
                            <button
                                v-if="canEdit && selectedSection && editMode"
                                @click="deleteSection(selectedSection)"
                                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition-all flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Zmazať
                            </button>
                        </div>
                    </div>

                    <div class="p-6 min-h-[500px] overflow-y-auto break-words">
                        <div v-if="sections.length === 0" class="text-center py-20">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">Žiadna dokumentácia</h3>
                            <p class="text-gray-500 mb-6">Začnite pridaním prvej sekcie</p>
                            <button
                                v-if="canEdit"
                                @click="addSection"
                                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition-all inline-flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Vytvoriť prvú sekciu
                            </button>
                        </div>

                        <div v-else-if="editMode && selectedSection" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Názov sekcie</label>
                                <input
                                    v-model="selectedSection.title"
                                    class="w-full text-2xl font-bold border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                    placeholder="Názov sekcie..."
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Obsah (podporuje Markdown)
                                </label>
                                <textarea
                                    v-model="selectedSection.content"
                                    class="w-full min-h-[400px] p-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm transition-all"
                                    placeholder="# Nadpis
## Podnadpis

**Tučný text** alebo *kurzíva*

- Položka 1
- Položka 2

```
Kód
```

[Link](https://example.com)"
                                ></textarea>
                                <p class="text-xs text-gray-500 mt-2">
                                    💡 Tip: Použite Markdown syntax pre formátovanie textu
                                </p>
                            </div>
                        </div>

                        <div v-else-if="selectedSection" class="prose prose-blue max-w-none">
                            <h1 class="text-3xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-blue-200">
                                {{ selectedSection.title }}
                            </h1>
                            <div
                            v-html="renderHtml(selectedSection.content)"
                            class="prose prose-blue max-w-none"
                            ></div>
                            <div v-if="!selectedSection.content" class="text-gray-400 italic py-8">
                                Táto sekcia zatiaľ nemá žiadny obsah
                            </div>
                        </div>

                        <div v-else class="prose prose-blue max-w-none space-y-12">
                            <div v-for="section in sections" :key="section.id" class="border-b border-gray-200 pb-8 last:border-b-0">
                                <h1 class="text-3xl font-bold text-gray-900 mb-6 pb-3 border-b-2 border-blue-200">
                                    {{ section.title }}
                                </h1>
                                <div
                                    v-html="renderHtml(section.content)"
                                    class="text-gray-700 leading-relaxed"
                                ></div>
                                <div v-if="!section.content" class="text-gray-400 italic py-4">
                                    Táto sekcia zatiaľ nemá žiadny obsah
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <NotificationContainer ref="notifier" />
    </div>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import NotificationContainer from '../components/notification/NotificationContainer.vue';
import { marked } from 'marked';
import ConfirmModal from '../components/modals/ConfirmModal.vue';

export default {
    name: 'ProjectDocumentation',
    components: { Navbar, NotificationContainer, ConfirmModal },
    data() {
        return {
            loading: true,
            saving: false,
            projectId: null,
            sections: [],
            confirmModalVisible: false,
            selectedSection: null,
            originalContent: null,
            editMode: false,
            user: null,
            timer: null,
        };
    },
    computed: {
        canEdit() {
            return this.user?.role !== 'customer';
        }
    },
    async mounted() {
        this.projectId = this.$route.params.id;

        const userRes = await this.$axios.get('/me');
        this.user = userRes.data;

        try {
            const res = await this.$axios.get(`/project/${this.projectId}/documentation`);
            this.sections = res.data;

            if (this.sections.length > 0) {
                this.selectedSection = this.sections[0];
                this.editMode = false;
            }
        } catch (err) {
            console.error(err);
            this.showNotification({ message: 'Chyba pri načítaní dokumentácie', type: 'error' });
        } finally {
            this.loading = false;
        }
    },
    methods: {
        deleteSection(section) {
            this.confirmModalMessage = `Naozaj chcete zmazať sekciu "${section.title}"?`;
            this.confirmModalCallback = async () => {
                try {
                    await this.$axios.delete(`/project/${this.projectId}/documentation/${section.id}`);
                    this.sections = this.sections.filter(s => s.id !== section.id);

                    if (this.selectedSection?.id === section.id) {
                        this.selectedSection = this.sections.length > 0 ? this.sections[0] : null;
                        this.editMode = false;
                    }

                    this.showNotification({ message: 'Sekcia bola zmazaná', type: 'success' });
                } catch (err) {
                    console.error(err);
                    this.showNotification({ message: 'Chyba pri mazaní sekcie', type: 'error' });
                }
            };
            this.confirmModalVisible = true;
        },

        onConfirm() {
            if (this.confirmModalCallback) this.confirmModalCallback();
            this.confirmModalVisible = false;
            this.confirmModalCallback = null;
        },

        onCancel() {
            this.confirmModalVisible = false;
            this.confirmModalCallback = null;
        },

        renderHtml(md) {
            if (!md) return '';
            return marked.parse(md);
        },

        selectSection(section) {
            if (this.editMode) {
                if (confirm('Máte neuložené zmeny. Chcete pokračovať?')) {
                    this.editMode = false;
                    this.selectedSection = section;
                    this.originalContent = null;
                }
            } else {
                this.selectedSection = section;
            }
        },

        startEdit() {
            if (!this.selectedSection) {
                this.showNotification({ message: 'Vyberte sekciu, ktorú chcete upraviť', type: 'info' });
                return;
            }            this.originalContent = {
                title: this.selectedSection.title,
                content: this.selectedSection.content
            };
            this.editMode = true;
        },

        async saveAndExit() {
            if (!this.selectedSection || !this.selectedSection.id) {
                console.error('Nie je vybraná sekcia alebo ID je undefined!', this.selectedSection);
                this.showNotification({ message: 'Vyberte sekciu pred uložením', type: 'error' });
                return;
            }

            clearTimeout(this.timer);
            this.saving = true;

            try {
                await this.$axios.put(`/project/${this.projectId}/documentation/${this.selectedSection.id}`, {
                    title: this.selectedSection.title,
                    content: this.selectedSection.content
                });

                this.editMode = false;
                this.originalContent = null;
                this.saving = false;
                this.showNotification({ message: 'Zmeny boli uložené', type: 'success' });
            } catch (err) {
                console.error(err);
                this.saving = false;
                this.showNotification({ message: 'Chyba pri ukladaní', type: 'error' });
            }
        },

        async addSection() {
            try {
                const res = await this.$axios.post(`/project/${this.projectId}/documentation`, {
                    title: 'Nová sekcia',
                    content: ''
                });

                const newSection = res.data;

                if (!newSection.id) {
                    console.error('Nová sekcia nemá ID!', newSection);
                    this.showNotification({ message: 'Chyba: Sekcia nemá ID', type: 'error' });
                    return;
                }

                this.sections.push(newSection);
                this.selectedSection = newSection;
                this.originalContent = {
                    title: newSection.title,
                    content: newSection.content
                };
                this.editMode = true;
                this.showNotification({ message: 'Sekcia bola vytvorená', type: 'success' });
            } catch (err) {
                console.error(err);
                this.showNotification({ message: 'Chyba pri vytváraní sekcie', type: 'error' });
            }
        },

        cancelEdit() {
            if (this.originalContent && this.selectedSection) {
                this.selectedSection.title = this.originalContent.title;
                this.selectedSection.content = this.originalContent.content;
            }

            this.editMode = false;
            this.originalContent = null;

            this.selectedSection = null;

            this.showNotification({ message: 'Zmeny boli zrušené', type: 'success' });
        },

        showNotification({ message, type }) {
            this.$refs.notifier.addNotification(message, type);
        }
    }
};
</script>
