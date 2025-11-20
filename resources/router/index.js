import { createRouter, createWebHistory } from 'vue-router';

import Login from '../views/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import Tasks from '../views/Tasks.vue';
import TimeTracking from '../views/TimeTracking.vue';
import Calendar from '../views/Calendar.vue';
import Profile from '../views/Profile.vue';
import Projects from  '../views/Projects.vue';
import TaskDetail from '../views/TaskDetail.vue';
import ProjectDetail from "../views/ProjectDetail.vue";
import UserManagement from "../views/UserManagement.vue";
import ProjectManagement from "../views/ProjectManagement.vue";
import Configuration from "../views/Configuration.vue";


const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: Login },
    { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
    { path: '/tasks', component: Tasks, meta: { requiresAuth: true } },
    { path: '/time-tracking', component: TimeTracking, meta: { requiresAuth: true } },
    { path: '/calendar', component: Calendar, meta: { requiresAuth: true } },
    { path: '/profile', component: Profile, meta: { requiresAuth: true } },
    { path: '/projects', component: Projects, meta: { requiresAuth: true } },
    { path: '/task/:id', component: TaskDetail, meta: { requiresAuth: true } },
    { path: '/project/:id', component: ProjectDetail, meta: { requiresAuth: true } },
    { path: '/users', name: 'UserManagement', component: UserManagement, meta: { requiresAdmin: true }},
    { path: '/project-management', name: 'ProjectManagement', component: ProjectManagement, meta: { requiresAdmin: true }},
    { path: '/configuration', name: 'Configuration', component: Configuration, meta: { requiresAdmin: true }}
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');

    if (to.meta.requiresAuth && !token) {
        next('/login');
    } else if (to.path === '/login' && token) {
        next('/dashboard');
    } else {
        next();
    }
});

export default router;
