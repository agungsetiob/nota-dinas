<template>
  <div v-if="isOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-3xl p-6 relative">
      <h2 class="text-lg font-bold mb-4">Kembalikan Nota Dinas</h2>
      <form @submit.prevent="handleSubmit">
        <input type="hidden" v-model="form.nota_dinas_id" name="nota_dinas_id">

        <div class="mb-4">
          <label class="block font-medium mb-1">Alasan Pengembalian</label>
          <textarea
            name="catatan"
            rows="3"
            required
            v-model="form.catatan"
            class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2"
          ></textarea>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="closeReturnModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
            Batal
          </button>
          <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Kembalikan
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import { useForm } from '@inertiajs/vue3';

  export default {
    props: {
      isOpen: {
        type: Boolean,
        required: true
      },
      notaId: {
        type: [Number, String],
        required: true
      }
    },
    setup(props, { emit }) {
      const form = useForm({
        catatan: '',
        nota_dinas_id: props.notaId
      });

      const handleSubmit = () => {
        console.log("Data yang dikirim:", form);
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

      const closeReturnModal = () => {
        form.reset();
        emit('close');
      };

      return { form, handleSubmit, closeReturnModal };
    }
  };
</script>
