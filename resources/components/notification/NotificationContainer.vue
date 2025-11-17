<template>
    <teleport to="body">
        <div id="toast-wrapper">
            <Notification
                v-for="(n, index) in notifications"
                :key="index"
                :message="n.message"
                :type="n.type"
            />
        </div>
    </teleport>
</template>

<script>
import Notification from './Notification.vue';

export default {
    name: 'NotificationContainer',
    components: { Notification },
    data() {
        return { notifications: [] };
    },
    methods: {
        addNotification(message, type = 'success') {
            this.notifications.push({ message, type });
            setTimeout(() => this.notifications.shift(), 3500);
        }
    }
};
</script>
