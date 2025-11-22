<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                    {{ getTodayDate() }}
                </h1>
                <p class="text-gray-600 text-sm sm:text-base">
                    {{ getGreeting() }}, vitajte späť!
                </p>
            </div>

            <div v-if="error" class="mb-6 p-4 rounded-lg bg-red-50 border border-red-100 text-red-700">
                <p class="font-medium">Chyba pri načítaní:</p>
                <p class="text-sm break-words">{{ error }}</p>
                <button @click="retry" class="mt-3 px-3 py-1 bg-red-600 text-white rounded">Skúsiť znova</button>
            </div>

            <div v-if="loading" class="flex justify-center py-12">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
            </div>

            <template v-if="!loading && !error">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                    <StatCard
                        v-for="(stat, i) in stats"
                        :key="i"
                        :title="stat.title"
                        :value="stat.value"
                        :icon="stat.icon"
                        :color="stat.color"
                    />
                </div>

                <template v-if="role === 'customer'">
                    <div class="space-y-6">
                        <SectionCard title="Vytvorené požiadavky" icon="MessageSquare" :count="requests.length">
                            <div class="space-y-3">
                                <RequestItem v-for="r in requests" :key="r.id" :request="r" />
                            </div>
                        </SectionCard>
                    </div>
                </template>

                <template v-else>
                    <div class="space-y-6">
                        <SectionCard title="Odpracované hodiny" icon="Clock">
                            <TimeTracker :hours="timeData" />
                        </SectionCard>
                    </div>
                </template>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import Navbar from "../components/Navbar.vue";
import StatCard from "../components/dashboard/StatCard.vue";
import SectionCard from "../components/dashboard/SectionCard.vue";
import TaskItem from "../components/dashboard/TaskItem.vue";
import TimeTracker from "../components/dashboard/TimeTracker.vue";
import RequestItem from "../components/dashboard/RequestItem.vue";
import ResponseItem from "../components/dashboard/ResponseItem.vue";
import { getGreeting, getTodayDate } from '../js/composables/date';

const user = ref(JSON.parse(localStorage.getItem("user")) || {});
const role = ref(user.value.role ? user.value.role.toLowerCase() : "user");

const loading = ref(false);
const error = ref("");
const stats = ref([]);
const userTasks = ref([]);
const timeData = ref({});
const requests = ref([]);
const responses = ref([]);

const safeRequest = async (fn) => {
    try {
        const res = await fn();
        return res.data;
    } catch (e) {
        console.warn("Request failed:", e.message);
        return [];
    }
};

const fetchDashboard = async () => {
    loading.value = true;
    error.value = "";

    try {
        if (role.value === "customer") {

            const [allTasks, inProgress, completed] = await Promise.all([
                safeRequest(() => axios.get("/tasks/my-requests")),
                safeRequest(() => axios.get("/tasks/in-progress")),
                safeRequest(() => axios.get("/tasks/completed"))
            ]);

            const filterPrivate = (tasks) => Array.isArray(tasks) ? tasks.filter(t => t.private !== 1) : [];

            requests.value = filterPrivate(allTasks);
            const inProgressList = filterPrivate(inProgress);
            const completedList = filterPrivate(completed);

            responses.value = [];

            let avgTime = "N/A";

            const finished = completedList.filter(t => {
                if (!t.end_date) return false;

                const end = new Date(t.end_date);
                return !isNaN(end.getTime());
            });

            if (finished.length > 0) {
                const totalMs = finished.reduce((sum, t) => {
                    const created = new Date(t.created_at);
                    const end = new Date(t.end_date);
                    return sum + (end - created);
                }, 0);

                const avgMs = totalMs / finished.length;
                const hours = Math.floor(avgMs / 1000 / 60 / 60);
                const minutes = Math.floor((avgMs / 1000 / 60) % 60);
                avgTime = `${hours}h ${minutes}m`;
            }

            stats.value = [
                { title: "Vytvorené požiadavky", value: requests.value.length, icon: "MessageSquare", color: "blue" },
                { title: "V riešení", value: inProgressList.length, icon: "Clock", color: "orange" },
                { title: "Dokončené", value: completedList.length, icon: "CheckCircle", color: "green" },
                { title: "Priemerný čas dokončenia", value: avgTime, icon: "Timer", color: "purple" }
            ];
    } else {
            const [tasks, projects, timeSummary, completedTasks] = await Promise.all([
                safeRequest(() => axios.get("/my-tasks")),
                safeRequest(() => axios.get("/my-projects")),
                safeRequest(() => axios.get("/time-tracking/summary")),
                safeRequest(() => axios.get("/tasks/completed"))
            ]);

            userTasks.value = Array.isArray(tasks) ? tasks.filter(t => !(role.value === 'customer' && t.private === 1)) : [];
            const completedList = Array.isArray(completedTasks) ? completedTasks : [];
            const totalHours = timeSummary?.total_hours || 0;
            timeData.value = timeSummary || {};

            stats.value = [
                { title: "Otvorené úlohy", value: userTasks.value.length, icon: "ClipboardList", color: "blue" },
                { title: "Dokončené úlohy", value: completedList.length, icon: "CheckCircle", color: "green" },
                { title: "Odpracované hodiny", value: totalHours + "h", icon: "Clock", color: "purple" },
                { title: "Projekty", value: Array.isArray(projects) ? projects.length : 0, icon: "Folder", color: "orange" }
            ];
        }
    } catch (e) {
        error.value = e.message || "Nepodarilo sa načítať dashboard.";
    } finally {
        loading.value = false;
    }
};

const retry = () => fetchDashboard();

fetchDashboard();
</script>
