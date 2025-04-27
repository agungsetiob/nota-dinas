<template>
    <div v-if="isOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white rounded-lg p-5 w-full max-w-xl">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Konfirmasi Persetujuan</h2>
        <div class="mb-4">
          <label class="block font-medium mb-1">Catatan</label>
          <textarea v-model="catatan" rows="3" class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2"></textarea>
        </div>
        <div class="flex justify-end gap-3 mt-4">
          <button type="button" @click="submit('disetujui')" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
            Setujui
          </button>
          <button type="button" @click="submit('ditolak')" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
            Tolak
          </button>
          <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Batal
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "ApprovalModal",
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
    data() {
      return {
        catatan: ""
      };
    },
    methods: {
      submit(status) {
        this.$emit("submit-approval", {
          notaId: this.notaId,
          status,
          catatan: this.catatan
        });
        this.catatan = "";
      },
      closeModal() {
        this.$emit("close");
        this.catatan = "";
      }
    }
  };
  </script>
  