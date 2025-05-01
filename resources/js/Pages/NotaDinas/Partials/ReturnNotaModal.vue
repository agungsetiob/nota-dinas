<template>
  <Modal :show="show" @close="closeModal" maxWidth="2xl">
    <div class="bg-white rounded-lg shadow-xl w-full p-6 relative">
      <h2 class="text-lg font-bold mb-4">Kembalikan Nota Dinas</h2>
      
      <form @submit.prevent="handleSubmit">
        <input type="hidden" v-model="form.nota_dinas_id">
        
        <div class="mb-4">
          <label class="block font-medium mb-1">Alasan Pengembalian</label>
          <textarea
            v-model="form.catatan"
            rows="3"
            required
            class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2"
          ></textarea>
        </div>

        <div class="flex justify-end gap-2">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
          >
            <span v-if="form.processing">
              <font-awesome-icon icon="spinner" spin class="mr-2" /> Mengirim...
            </span>
            <span v-else>
              Kembalikan
            </span>
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  notaId: {
    type: [Number, String],
    required: true
  }
});

const emit = defineEmits(['close']);

const form = useForm({
  catatan: '',
  nota_dinas_id: props.notaId
});

watch(() => props.notaId, (newId) => {
  form.nota_dinas_id = newId;
});

const handleSubmit = () => {
  form.post(route('nota.kembalikan', { nota: props.notaId }), {
    onSuccess: () => {
      form.reset();
      emit('close');
    },
    onError: (errors) => {
      console.error(errors);
    }
  });
};

const closeModal = () => {
  form.reset();
  emit('close');
};
</script>