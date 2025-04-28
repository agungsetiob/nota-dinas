<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  show: Boolean,
  isEdit: Boolean,
  notaData: Object,
  errors: Object,
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
      preserveScroll: true,
    });
  } else {
    form.post(route('nota-dinas.store'), {
      onSuccess: () => emit('close'),
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <div v-if="show" class="fixed z-50 inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-4 sm:p-6 rounded-lg w-11/12 sm:w-full max-w-5xl">
      <h3 class="text-lg font-semibold mb-4">{{ isEdit ? 'Edit Nota Dinas' : 'Tambah Nota Dinas' }}</h3>

      <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">
              Terdapat {{ Object.keys(form.errors).length }} kesalahan yang harus diperbaiki
            </h3>
            <div class="mt-2 text-sm text-red-700">
              <ul class="list-disc pl-5 space-y-1">
                <li v-for="(error, field) in form.errors" :key="field">
                  {{ error }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <form @submit.prevent="handleSubmit">
        <input type="hidden" v-model="form.method">
        <input type="hidden" v-model="form.id">

        <div class="mb-4">
          <label for="nomor_nota" class="block text-sm font-medium text-gray-700">Nomor Nota</label>
          <input 
            type="text" 
            v-model="form.nomor_nota" 
            required
            :class="{
              'mt-1 block w-full border rounded-md px-3 py-2 text-sm sm:text-base': true,
              'border-gray-300': !form.errors.nomor_nota,
              'border-red-500': form.errors.nomor_nota
            }"
          >
          <p v-if="form.errors.nomor_nota" class="mt-1 text-sm text-red-600">
            {{ form.errors.nomor_nota }}
          </p>
        </div>

        <div class="mb-4">
          <label for="perihal" class="block text-sm font-medium text-gray-700">Perihal</label>
          <input 
            type="text" 
            v-model="form.perihal" 
            required
            :class="{
              'mt-1 block w-full border rounded-md px-3 py-2 text-sm sm:text-base': true,
              'border-gray-300': !form.errors.perihal,
              'border-red-500': form.errors.perihal
            }"
          >
          <p v-if="form.errors.perihal" class="mt-1 text-sm text-red-600">
            {{ form.errors.perihal }}
          </p>
        </div>

        <div class="mb-4">
          <label for="anggaran" class="block text-sm font-medium text-gray-700">Anggaran</label>
          <input 
            type="number" 
            v-model="form.anggaran"
            :class="{
              'mt-1 block w-full border rounded-md px-3 py-2 text-sm sm:text-base': true,
              'border-gray-300': !form.errors.anggaran,
              'border-red-500': form.errors.anggaran
            }"
          >
          <p v-if="form.errors.anggaran" class="mt-1 text-sm text-red-600">
            {{ form.errors.anggaran }}
          </p>
        </div>

        <div class="mb-4">
          <label for="tanggal_pengajuan" class="block text-sm font-medium text-gray-700">Tanggal Pengajuan</label>
          <input 
            type="date" 
            v-model="form.tanggal_pengajuan" 
            required
            :class="{
              'mt-1 block w-full border rounded-md px-3 py-2 text-sm sm:text-base': true,
              'border-gray-300': !form.errors.tanggal_pengajuan,
              'border-red-500': form.errors.tanggal_pengajuan
            }"
          >
          <p v-if="form.errors.tanggal_pengajuan" class="mt-1 text-sm text-red-600">
            {{ form.errors.tanggal_pengajuan }}
          </p>
        </div>

        <div class="flex justify-end gap-2">
          <button 
            type="button" 
            @click="emit('close')"
            class="px-3 py-2 bg-gray-300 text-gray-700 text-sm sm:text-base rounded hover:bg-gray-400"
          >
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