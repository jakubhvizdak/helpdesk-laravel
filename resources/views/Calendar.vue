<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar />

        <div class="max-w-7xl mx-auto p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Kalendár & Gantt</h1>

            <div class="flex gap-4 mb-4">
                <button
                    @click="view = 'calendar'"
                    :class="view === 'calendar' ? activeBtn : inactiveBtn"
                >
                    Kalendár
                </button>
                <button
                    @click="view = 'gantt'"
                    :class="view === 'gantt' ? activeBtn : inactiveBtn"
                >
                    Gantt
                </button>

                <button
                    @click="exportICS"
                    class="ml-auto bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
                >
                    Exportovať do Google (ICS)
                </button>
            </div>

            <div v-if="view === 'calendar'">
                <FullCalendar :options="calendarOptions" />
            </div>

            <div v-else>
                <div v-if="tasks.length > 0">
                    <Gantt :tasks="tasks" />
                </div>
                <div v-else class="text-gray-500 italic">
                    Žiadne úlohy na zobrazenie.
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import api from '../js/api.js';
import { formatDate } from '../js/composables/date';
import { downloadBlob } from '../js/composables/file';
import Gantt from '../components/Gantt.vue';
import axios from 'axios'

export default {
    name: 'CalendarView',
    components: { Navbar, FullCalendar, Gantt },
    data() {
        return {
            view: 'calendar',
            tasks: [],
            calendarOptions: {
                plugins: [dayGridPlugin],
                initialView: 'dayGridMonth',
                height: 700,
                events: [],
            },
            activeBtn:
                'bg-indigo-600 text-white px-4 py-2 rounded-md shadow transition',
            inactiveBtn:
                'bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition',
        };
    },
    async mounted() {
        try {
            const me = await axios.get('http://localhost:8000/api/me', {
                headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
                withCredentials: true
            });
            this.user = me.data;
        } catch (err) {
            console.error('Chyba pri načítaní info o používateľovi', err);
        }

        await this.loadTasks();
    },
    methods: {
        async loadTasks() {
            try {
                const res = await api.get('/api/tasks');
                const taskData = res.data?.data ?? res.data ?? [];

                const filteredTasks = taskData.filter(task => {
                    return !(this.user?.role === 'customer' && task.private === 1);
                });


                this.tasks = filteredTasks.map(task => ({
                    id: String(task.id),
                    text: task.title,
                    start: formatDate(task.created_at),
                    end: formatDate(task.end_date || task.due_date || task.created_at),
                }));

                const events = this.tasks.flatMap(task => {
                    const evs = [];
                    if (task.start) {
                        evs.push({
                            title: `🆕 ${task.text}`,
                            start: task.start,
                            color: '#3b82f6',
                        });
                    }
                    if (task.end && task.end !== task.start) {
                        evs.push({
                            title: `✅ Deadline: ${task.text}`,
                            start: task.end,
                            color: '#f97316',
                        });
                    }
                    return evs;
                });
                this.calendarOptions.events = events;
            } catch (e) {
                console.error('Chyba pri načítaní úloh:', e);
            }
        },
        async exportICS() {
            try {
                const response = await api.get('/api/tasks/export-ics', { responseType: 'blob' });
                if (response.data) downloadBlob(response.data, 'tasks.ics');
            } catch (error) {
                console.error('Chyba pri exporte ICS:', error);
            }
        }
    },
};
</script>
