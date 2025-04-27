<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  show: Boolean,
  isEdit: Boolean,
  notaData: Object,
});

const emit = defineEmits(['close']);

const form = useForm({
  id: props.notaData?.id || '',
  nomor_nota: props.notaData?.nomor_nota || '',
  perihal: props.notaData?.perihal || '',
  anggaran: props.notaData?.anggaran || '',
  tanggal_pengajuan: props.notaData?.tanggal_pengajuan || '',
});

const handleSubmit = () => {
  if (props.isEdit) {
    form.put(route('nota-dinas.update', form.id), {
      onSuccess: () => emit('close'),
    });
  } else {
    form.post(route('nota-dinas.store'), {
      onSuccess: () => emit('close'),
    });
  }
};

</script>

<template>
  <div v-if="show" class="fixed z-50 inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-4 sm:p-6 rounded-lg w-11/12 sm:w-full max-w-5xl">
      <h3 class="text-lg font-semibold mb-4">{{ isEdit ? 'Edit Nota Dinas' : 'Tambah Nota Dinas' }}</h3>

      <form @submit.prevent="handleSubmit">
        <input type="hidden" v-model="form.method">
        <input type="hidden" v-model="form.id">

        <div class="mb-4">
          <label for="nomor_nota" class="block text-sm font-medium text-gray-700">Nomor Nota</label>
          <input type="text" v-model="form.nomor_nota" required
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base">
        </div>

        <div class="mb-4">
          <label for="perihal" class="block text-sm font-medium text-gray-700">Perihal</label>
          <input type="text" v-model="form.perihal" required
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base">
        </div>

        <div class="mb-4">
          <label for="anggaran" class="block text-sm font-medium text-gray-700">Anggaran</label>
          <input type="text" v-model="form.anggaran" required
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base">
        </div>

        <div class="mb-4">
          <label for="tanggal_pengajuan" class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
          <input type="date" v-model="form.tanggal_pengajuan" required
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm sm:text-base">
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" @click="emit('close')"
            class="px-3 py-2 bg-gray-300 text-gray-700 text-sm sm:text-base rounded hover:bg-gray-400">
            Batal
          </button>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50"
          >
            <span v-if="form.processing">
              <font-awesome-icon icon="spinner" spin class="mr-2" /> Menyimpan..
            </span>
            <span v-else>
              Simpan
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
