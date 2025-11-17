<template>
    <div>
        <router-view />
        <NotificationContainer ref="notifier" />
    </div>
</template>

<script>
import NotificationContainer from '../components/notification/NotificationContainer.vue';

export default {
    name: 'App',
    components: { NotificationContainer },
    mounted() {
        console.log('✅ App.vue mounted');
    },
    methods: {
        notify(message, type = 'success') {
            if (this.$refs.notifier && this.$refs.notifier.addNotification) {
                this.$refs.notifier.addNotification(message, type);
            } else {
                console.warn('Notifier nie je pripravený');
            }
        }
    },
    provide() {
        return {
            notify: this.notify
        };
    }
};
</script>
