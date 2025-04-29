<template>
  <transition
    enter-active-class="transition ease-out duration-300"
    enter-from-class="opacity-0 transform scale-90"
    enter-to-class="opacity-100 transform scale-100"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100 transform scale-100"
    leave-to-class="opacity-0 transform scale-95"
  >
    <div v-if="isOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-3xl p-6 relative">
        <h2 class="text-lg font-bold mb-4">Kirim Nota</h2>

        <p class="mb-4 text-sm text-gray-600">{{ roleMessage }}</p>

        <form @submit.prevent="handleSubmit">
          <div class="mb-4">
            <label class="block font-medium mb-1">Catatan</label>
            <textarea
              v-model="form.catatan"
              rows="3"
              class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2"
            ></textarea>
          </div>

          <div v-if="showLampiran" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Lampiran bisa lebih dari 1 (opsional)
            </label>
            <input
              type="file"
              accept="application/pdf"
              multiple
              @change="handleFileUpload"
              class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
            >
          </div>

          <div class="flex justify-end gap-2">
            <button
              type="button"
              @click="closeSendModal"
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50"
            >
              <span v-if="form.processing">
                <font-awesome-icon icon="spinner" spin class="mr-2" /> Mengirim..
              </span>
              <span v-else>
                Kirim
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </transition>
</template>

<script>
import { useForm } from '@inertiajs/vue3';

export default {
  name: "SendNotaModal",
  props: {
    isOpen: {
      type: Boolean,
      default: false
    },
    notaId: {
      type: Number,
      required: true
    },
    userRole: {
      type: String,
      default: ''
    }
  },
  setup(props, { emit }) {
    const form = useForm({
      catatan: '',
      lampiran: []
    });

    const handleFileUpload = (event) => {
      form.lampiran = Array.from(event.target.files);
    };

    const handleSubmit = () => {
      form.post(route('nota.pengiriman.store', { nota: props.notaId }), {
        onSuccess: () => {
          form.reset();
          emit('close');
        },
        //onError jika diperlukan
      });
    };

    const closeSendModal = () => {
      form.reset();
      emit('close');
    };

    return { form, handleFileUpload, handleSubmit, closeSendModal };
  },
  computed: {
    roleMessage() {
      switch (this.userRole) {
        case 'skpd': return 'Akan dikirim otomatis ke Asisten terkait SKPD.';
        case 'asisten': return 'Akan dikirim otomatis ke Sekda.';
        case 'sekda': return 'Akan dikirim otomatis ke Bupati.';
        default: return '';
      }
    },
    showLampiran() {
      return this.userRole === 'skpd';
    }
  }
};
</script>
