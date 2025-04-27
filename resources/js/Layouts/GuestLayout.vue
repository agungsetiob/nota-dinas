<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const isLoading = ref(false);
document.addEventListener('inertia:start', () => {
  isLoading.value = true;
});

document.addEventListener('inertia:finish', () => {
  isLoading.value = false;
});
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-pattern pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center">
                <font-awesome-icon icon="spinner" spin class="text-6xl text-green-400" />
            </div>
            <slot />
        </div>
    </div>
</template>
