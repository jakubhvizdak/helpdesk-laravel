<template>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-xl sm:text-2xl font-bold text-white">Gantt Diagram</h2>

                <div class="flex items-center gap-2 sm:gap-4">
                    <button
                        @click="zoomOut"
                        class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-md transition-colors text-sm"
                    >
                        <span class="hidden sm:inline"></span> -
                    </button>
                    <button
                        @click="zoomIn"
                        class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-md transition-colors text-sm"
                    >
                        <span class="hidden sm:inline"></span> +
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-max">
                <div class="flex border-b-2 border-gray-300 bg-gray-50">
                    <div class="w-32 sm:w-48 flex-shrink-0 border-r-2 border-gray-300 p-3">
                        <span class="text-xs sm:text-sm font-semibold text-gray-700">Úloha</span>
                    </div>
                    <div class="flex">
                        <div
                            v-for="day in totalDays"
                            :key="day"
                            :style="{ width: cellWidth + 'px' }"
                            class="flex-shrink-0 text-xs text-center border-r border-gray-200 p-2 font-medium text-gray-600"
                        >
                            {{ formatDateLabel(day - 1) }}
                        </div>
                    </div>
                </div>

                <div
                    v-for="(task, index) in tasks"
                    :key="task.id"
                    class="flex border-b border-gray-200 hover:bg-gray-50 transition-colors"
                    :class="{ 'bg-white': index % 2 === 0, 'bg-gray-50/50': index % 2 !== 0 }"
                >
                    <div class="w-32 sm:w-48 flex-shrink-0 px-3 py-4 text-xs sm:text-sm font-medium text-gray-800 border-r-2 border-gray-300 flex items-center">
                        <span class="truncate" :title="task.text">{{ task.text }}</span>
                    </div>

                    <div class="flex-1 relative py-2">
                        <div class="absolute inset-0 flex">
                            <div
                                v-for="day in totalDays"
                                :key="'grid-' + day"
                                :style="{ width: cellWidth + 'px' }"
                                class="border-r border-gray-100"
                            ></div>
                        </div>

                        <div
                            v-if="task.start && task.end"
                            class="absolute h-8 rounded-lg shadow-md flex items-center justify-center transition-all hover:shadow-lg cursor-pointer group"
                            :class="getTaskColor(index)"
                            :style="getTaskStyle(task)"
                        >
                            <span class="text-white text-xs font-medium px-2 truncate group-hover:scale-105 transition-transform">
                                {{ task.text }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="!tasks || tasks.length === 0" class="text-center py-12 text-gray-500">
                    <p class="text-sm sm:text-base">Žiadne úlohy na zobrazenie</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { formatDateLabel as formatLabel } from '../js/composables/date';

export default {
    name: "Gantt",
    props: {
        tasks: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            startDate: null,
            endDate: null,
            totalDays: 0,
            cellWidth: 50,
        };
    },
    watch: {
        tasks: {
            handler() {
                this.calculateDateRange();
            },
            immediate: true,
        },
    },
    mounted() {
        this.updateCellWidth();
        window.addEventListener('resize', this.updateCellWidth);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.updateCellWidth);
    },

    methods: {
        updateCellWidth() {
            if (window.innerWidth < 640) {
                this.cellWidth = 40;
            } else if (window.innerWidth < 1024) {
                this.cellWidth = 50;
            } else {
                this.cellWidth = 60;
            }
        },
        zoomIn() {
            this.cellWidth = Math.min(this.cellWidth + 10, 100);
        },
        zoomOut() {
            this.cellWidth = Math.max(this.cellWidth - 10, 30);
        },
        calculateDateRange() {
            if (!this.tasks || this.tasks.length === 0) return;

            const startDates = this.tasks.map(t => new Date(t.start));
            const endDates = this.tasks.map(t => new Date(t.end));

            this.startDate = new Date(Math.min(...startDates));
            this.endDate = new Date(Math.max(...endDates));

            const diffTime = this.endDate - this.startDate;
            this.totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        },
        formatDateLabel(dayIndex) {
            const date = new Date(this.startDate);
            date.setDate(this.startDate.getDate() + dayIndex);
            return `${date.getDate()}/${date.getMonth() + 1}`;
        },
        getTaskStyle(task) {
            const taskStart = new Date(task.start);
            const taskEnd = new Date(task.end);
            const offsetDays = Math.floor((taskStart - this.startDate) / (1000 * 60 * 60 * 24));
            const durationDays = Math.floor((taskEnd - taskStart) / (1000 * 60 * 60 * 24)) + 1;

            return {
                left: `${offsetDays * this.cellWidth}px`,
                width: `${durationDays * this.cellWidth}px`,
            };
        },
        getTaskColor(index) {
            const colors = [
                'bg-blue-500 hover:bg-blue-600',
                'bg-green-500 hover:bg-green-600',
                'bg-purple-500 hover:bg-purple-600',
                'bg-orange-500 hover:bg-orange-600',
                'bg-pink-500 hover:bg-pink-600',
                'bg-indigo-500 hover:bg-indigo-600',
            ];
            return colors[index % colors.length];
        },
    },
};
</script>
