<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg p-5 w-full max-w-xl">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Konfirmasi Persetujuan</h2>
      <div class="mb-4">
        <label class="block font-medium mb-1">Catatan</label>
        <textarea 
          v-model="form.catatan" 
          rows="3" 
          class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2"
        ></textarea>
      </div>
      <div v-if="form.errors.status" class="mb-4 text-red-500">{{ form.errors.status }}</div>
      <div class="flex justify-end gap-3 mt-4">
        <button 
          @click="submit('disetujui')"
          class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
          :disabled="form.processing"
        >
          <span v-if="!form.processing">Setujui</span>
          <span v-else>Memproses...</span>
        </button>
        <button 
          @click="submit('ditolak')"
          class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
          :disabled="form.processing"
        >
          <span v-if="!form.processing">Tolak</span>
          <span v-else>Memproses...</span>
        </button>
        <button 
          @click="closeModal"
          class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
          :disabled="form.processing"
        >
          Batal
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  isOpen: Boolean,
  notaId: [Number, String]
});

const emit = defineEmits(['close', 'submitted']);

const form = useForm({
  status: '',
  catatan: ''
});

const submit = (status) => {
  form.status = status;
  form.patch(route('nota-dinas.approval', props.notaId), {
    onSuccess: () => {
      emit('submitted');
      form.reset();
      emit('close');
    },
    preserveScroll: true,
  });
};

const closeModal = () => {
  if (!form.processing) {
    form.reset();
    emit('close');
  }
};
</script>
