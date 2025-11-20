<template>
    <header class="sticky top-0 z-50 bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <router-link
                    to="/dashboard"
                    class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent hover:from-blue-700 hover:to-blue-800 tracking-wide transition-all duration-300"
                >
                    {{ appName }}
                </router-link>

                <div class="flex items-center gap-3">
                    <nav class="flex items-center gap-1">
                        <router-link
                            to="/projects"
                            :class="linkClass('/projects')"
                        >
                            <Folder class="w-5 h-5" />
                            <span class="hidden md:inline">Projekty</span>
                        </router-link>

                        <router-link
                            to="/tasks"
                            :class="linkClass('/tasks')"
                        >
                            <CheckSquare class="w-5 h-5" />
                            <span class="hidden md:inline">Úlohy</span>
                        </router-link>

                        <router-link
                            to="/time-tracking"
                            :class="linkClass('/time-tracking')"
                        >
                            <Clock class="w-5 h-5" />
                            <span class="hidden lg:inline">Čas</span>
                        </router-link>

                        <router-link
                            to="/calendar"
                            :class="linkClass('/calendar')"
                        >
                            <Calendar class="w-5 h-5" />
                            <span class="hidden lg:inline">Kalendár</span>
                        </router-link>
                    </nav>

                    <div class="relative ml-2">
                        <button
                            @click.stop="toggleDropdown"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200 focus:outline-none"
                        >
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold shadow-sm">
                                {{ userInitials }}
                            </div>
                            <div class="hidden lg:block text-left">
                                <p class="text-sm font-medium text-gray-900">{{ userName }}</p>
                                <p class="text-xs text-gray-500">{{ userRole }}</p>
                            </div>
                            <ChevronDown
                                class="w-4 h-4 text-gray-500 transform transition-transform duration-200 hidden md:block"
                                :class="{ 'rotate-180': dropdownOpen }"
                            />
                        </button>

                        <transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 scale-95 translate-y-1"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 translate-y-1"
                        >
                            <div
                                v-if="dropdownOpen"
                                @click.stop
                                class="absolute right-0 mt-2 w-64 bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden"
                            >
                                <div class="px-4 py-3 bg-gradient-to-br from-gray-50 to-white border-b border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                            {{ userInitials }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ userName }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-2">
                                    <router-link
                                        to="/profile"
                                        class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors"
                                    >
                                        <User class="w-5 h-5 text-gray-400" />
                                        <span class="text-sm font-medium">Profil</span>
                                    </router-link>

                                    <router-link
                                        v-if="user.role === 'admin'"
                                        to="/users"
                                        class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors"
                                    >
                                        <Users class="w-5 h-5 text-gray-400" />
                                        <span class="text-sm font-medium">Správa používateľov</span>
                                    </router-link>
                                </div>

                                <router-link
                                    v-if="user.role === 'admin'"
                                    to="/project-management"
                                    class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    <Folder class="w-5 h-5 text-gray-400" />
                                    <span class="text-sm font-medium">Správa projektov</span>
                                </router-link>

                                <router-link
                                    v-if="user.role === 'admin'"
                                    to="/configuration"
                                    class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    <Settings class="w-5 h-5 text-gray-400" />
                                    <span class="text-sm font-medium">Konfigurácia</span>
                                </router-link>

                                <div class="border-t border-gray-100">
                                    <button
                                        @click.prevent="logout"
                                        class="flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors w-full text-left"
                                    >
                                        <LogOut class="w-5 h-5" />
                                        <span class="text-sm font-medium">Odhlásiť sa</span>
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { getInitials } from '../js/composables/string';
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import {
    Folder,
    CheckSquare,
    Clock,
    Calendar,
    ChevronDown,
    User,
    Users,
    LogOut,
    Settings
} from 'lucide-vue-next'

const dropdownOpen = ref(false)
const route = useRoute()
const router = useRouter()
const appName = import.meta.env.VITE_APP_NAME || 'Helpdesk'
const user = ref(JSON.parse(localStorage.getItem('user')) || {})

const userInitials = computed(() => getInitials(user.value.name, user.value.surname))

const userName = computed(() => user.value.name || 'Užívateľ')

const userRole = computed(() => {
    if (user.value.role === 'admin') return 'Administrátor'
    if (user.value.role === 'customer') return 'Zákazník'
    return 'Používateľ'
})

watch(route, () => {
    dropdownOpen.value = false
})

onMounted(() => {
    document.addEventListener('click', closeDropdown)
})
onBeforeUnmount(() => {
    document.removeEventListener('click', closeDropdown)
})

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
    dropdownOpen.value = false
}

const linkClass = (path) => {
    const isActive = route.path.startsWith(path)
    return [
        'flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 font-medium',
        isActive ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
    ]
}

const logout = async () => {
    try {
        await axios.post('/logout')
    } catch (err) {
        if (err.response?.status !== 401) {
            console.error('Logout error:', err)
        }
    } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        user.value = {}

        dropdownOpen.value = false

        router.replace({ path: '/login' })
    }
}
</script>
