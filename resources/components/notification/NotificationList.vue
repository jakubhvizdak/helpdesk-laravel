<template>
    <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 scale-95 translate-y-1"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-1"
    >
        <div
            v-if="visible"
            @click.stop
            class="absolute right-0 mt-2 w-96 bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden max-h-[32rem] z-50"
        >
            <div class="px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Notifikácie</h3>
                <button
                    v-if="unreadCount > 0"
                    @click="markAllAsRead"
                    class="text-xs text-white font-medium transition-colors"
                >
                    Označiť všetko ako prečítané
                </button>
            </div>

            <div v-if="loading" class="p-8 text-center">
                <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto"></div>
                <p class="text-sm text-gray-500 mt-2">Načítavam notifikácie...</p>
            </div>

            <div v-else-if="notifications.length > 0" class="max-h-96 overflow-y-auto custom-scrollbar">
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    @click="handleNotificationClick(notification)"
                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
                    :class="{ 'bg-blue-50/50': !notification.read_at }"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="w-2 h-2 rounded-full mt-2 flex-shrink-0"
                            :class="notification.read_at ? 'bg-gray-300' : 'bg-blue-500'"
                        ></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ notification.data.title || 'Notifikácia' }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ notification.data.message || notification.data.body }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ formatDateTime(notification.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-500">Žiadne notifikácie</p>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import {formatDateTime} from "../../js/composables/date.js";

const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close', 'update-count'])

const router = useRouter()
const notifications = ref([])
const loading = ref(false)

const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.read_at).length
})

watch(() => props.visible, (newVal) => {
    if (newVal) {
        fetchNotifications()
    }
})

watch(unreadCount, (newVal) => {
    emit('update-count', newVal)
})

const fetchNotifications = async () => {
    try {
        loading.value = true
        const res = await axios.get('/notifications')
        notifications.value = res.data
        emit('update-count', res.data.filter(n => !n.read_at).length)
    } catch (err) {
        console.error('Error fetching notifications:', err)
    } finally {
        loading.value = false
    }
}

const markAllAsRead = async () => {
    try {
        await axios.post('/notifications/mark-all-read')
        notifications.value = notifications.value.map(n => ({
            ...n,
            read_at: new Date().toISOString()
        }))
    } catch (err) {
        console.error('Error marking notifications as read:', err)
    }
}

const handleNotificationClick = async (notification) => {
    if (!notification.read_at) {
        try {
            await axios.patch(`/notifications/${notification.id}`)
            notification.read_at = new Date().toISOString()
        } catch (err) {
            console.error('Error marking notification as read:', err)
        }
    }

    if (notification.data.link) {
        emit('close')

        const sameTask = router.currentRoute.value.fullPath === notification.data.link

        if (sameTask) {
            const taskDetailComponent = router.currentRoute.value.matched[0].instances.default
            if (taskDetailComponent?.fetchTask) {
                taskDetailComponent.fetchTask()
                taskDetailComponent.fetchComments()
                taskDetailComponent.fetchTimes()
                taskDetailComponent.fetchEditLogs()
            }
        } else {
            router.push(notification.data.link)
        }
    }
}

defineExpose({
    fetchNotifications
})
</script>
