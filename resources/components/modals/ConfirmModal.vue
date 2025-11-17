<template>
    <div class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-96 p-6 text-center">
            <h2 class="text-xl font-bold mb-4 text-gray-800">{{ title }}</h2>
            <p class="text-gray-600 mb-6">{{ message }}</p>

            <div class="flex justify-center gap-4">
                <button
                    @click="$emit('cancel')"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded"
                >
                    Zrušiť
                </button>
                <button
                    @click="$emit('confirm')"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
                >
                    Odstrániť
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ConfirmModal',
    props: {
        title: { type: String, default: 'Potvrdenie akcie' },
        message: { type: String, default: 'Naozaj chcete vykonať túto akciu?' },
    },
};
</script>
