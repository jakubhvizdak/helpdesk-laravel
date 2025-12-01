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

import { getGreeting, getTodayDate } from '../js/composables/date';

const user = ref(JSON.parse(localStorage.getItem("user")) || {});
const role = ref(user.value.role ? user.value.role.toLowerCase() : "user");

const loading = ref(false);
const error = ref("");

const stats = ref([]);
const requests = ref([]);
const userTasks = ref([]);
const timeData = ref({});

const fetchDashboard = async () => {
    loading.value = true;
    error.value = "";

    try {
        const res = await axios.get("/dashboard");
        const data = res.data;

        if (role.value === "customer") {
            stats.value = [
                { title: "Vytvorené požiadavky", value: data.stats.total, icon: "MessageSquare", color: "blue" },
                { title: "V riešení", value: data.stats.inProgress, icon: "Clock", color: "orange" },
                { title: "Dokončené", value: data.stats.completed, icon: "CheckCircle", color: "green" },
                { title: "Priemerný čas dokončenia", value: data.stats.avgTime, icon: "Timer", color: "purple" }
            ];

            requests.value = data.requests || [];
        } else {
            stats.value = [
                { title: "Otvorené úlohy", value: data.stats.openTasks, icon: "ClipboardList", color: "blue" },
                { title: "Dokončené úlohy", value: data.stats.completed, icon: "CheckCircle", color: "green" },
                { title: "Projekty", value: data.stats.projects, icon: "Folder", color: "orange" },
                { title: "Odpracované hodiny", value: data.stats.totalHours + "h", icon: "Clock", color: "purple" }
            ];

            userTasks.value = data.tasks || [];
            timeData.value = data.timeData || { total_hours: 0 };
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
