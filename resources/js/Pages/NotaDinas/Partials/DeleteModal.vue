<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    nota: Object,
    show: Boolean
});

const emit = defineEmits(['close']);

const form = useForm({});

function deleteNota() {
    form.delete(route('nota-dinas.destroy', props.nota.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        },
        onError: () => {
            // Handle error
        }
    });
}
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold">Konfirmasi Hapus</h3>
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p class="mb-6">Anda yakin ingin menghapus nota dinas no <span class="font-semibold text-red-400">{{ nota.nomor_nota }}</span>?
                <br>Data yang sudah dihapus tidak dapat dikembalikan.</p>
            
            <div class="flex justify-end space-x-3">
                <button @click="$emit('close')"
                        class="px-3 py-2 bg-gray-300 text-gray-700 text-sm sm:text-base rounded hover:bg-gray-400">
                    Batal
                </button>
                <button @click="deleteNota"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 disabled:opacity-50">
                    <span v-if="form.processing">
                        <font-awesome-icon icon="spinner" spin class="mr-2" />
                        Menghapus...
                    </span>
                    <span v-else>
                        Ya, Hapus
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>