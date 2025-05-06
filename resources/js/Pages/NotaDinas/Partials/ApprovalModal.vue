<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: Boolean,
  notaId: [Number, String]
});

const emit = defineEmits(['close', 'submitted']);

const form = useForm({
  status: '',
  catatan: ''
});

const processingStatus = ref(null);

const submit = (status) => {
  processingStatus.value = status;
  form.status = status;

  form.patch(route('nota-dinas.approval', props.notaId), {
    onSuccess: () => {
      emit('submitted');
      form.reset();
      processingStatus.value = null;
      emit('close');
    },
    onError: () => {
      processingStatus.value = null;
    },
    preserveScroll: true,
  });
};

const closeModal = () => {
  if (!processingStatus.value) {
    form.reset();
    processingStatus.value = null;
    emit('close');
  }
};
</script>

<template>
  <Modal :show="show" @close="closeModal" maxWidth="xl">
    <div class="bg-white rounded-lg p-5 w-full">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Konfirmasi Persetujuan</h2>
      
      <div class="mb-4">
        <label class="block font-medium mb-1">Catatan</label>
        <textarea 
          v-model="form.catatan" 
          rows="3" 
          class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2"
          :disabled="processingStatus !== null"
        ></textarea>
      </div>

      <div v-if="form.errors.status" class="mb-4 text-red-500">{{ form.errors.status }}</div>

      <div class="flex justify-end gap-3 mt-4">
        <button 
          @click="submit('disetujui')"
          class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition"
          :disabled="form.processing"
        >
          <span v-if="processingStatus === 'disetujui'">Memproses...</span>
          <span v-else>Setujui</span>
        </button>

        <button 
          @click="submit('ditolak')"
          class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition"
          :disabled="form.processing"
        >
          <span v-if="processingStatus === 'ditolak'">Memproses...</span>
          <span v-else>Tolak</span>
        </button>

        <button 
          @click="closeModal"
          class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition"
          :disabled="form.processing"
        >
          Batal
        </button>
      </div>
    </div>
  </Modal>
</template>
