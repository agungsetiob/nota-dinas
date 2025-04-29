<template>
  <transition
    enter-active-class="transition ease-out duration-1200"
    enter-from-class="opacity-0 -translate-y-full"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition ease-in duration-1200"
    leave-from-class="opacity-100 translate-y-0"
    leave-to-class="opacity-0 translate-y-full"
  >
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-3xl p-6 relative">
        <h3 class="text-lg font-semibold mb-4">Daftar Lampiran</h3>

        <ul class="list-disc pl-5 text-sm text-gray-700">
          <template v-if="loading">
            <li class="text-gray-500">Memuat lampiran...</li>
          </template>
          <template v-else-if="error">
            <li class="text-red-500">{{ error }}</li>
          </template>
          <template v-else>
            <li v-if="lampiranList.length === 0" class="text-gray-500">Tidak ada lampiran.</li>
            <li v-for="(file, index) in lampiranList" :key="index" class="mb-2">
              <a :href="file.url" target="_blank" class="text-blue-600 hover:underline">
                {{ file.name }}
              </a>
              <span class="text-gray-500 text-xs"> ({{ formatDate(file.created_at) }})</span>
            </li>
          </template>
        </ul>

        <div class="mt-4 text-right">
          <button @click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "LampiranModal",
  props: {
    isOpen: {
      type: Boolean,
      default: false
    },
    notaId: {
      type: [Number, String],
      required: true
    }
  },
  data() {
    return {
      lampiranList: [],
      loading: false,
      error: null,
      lastFetchedNotaId: null
    };
  },
  watch: {
    isOpen(newVal) {
      if (newVal) {
        this.checkAndFetchLampiran();
      }
    }
  },
  mounted() {
    if (this.isOpen) {
      this.checkAndFetchLampiran();
    }
  },
  methods: {
    async checkAndFetchLampiran() {
      if (!this.notaId) {
        console.warn('Nota ID tidak tersedia!');
        return;
      }

      if (this.lastFetchedNotaId === this.notaId) {
        console.log('Nota ID sudah di-fetch sebelumnya:', this.notaId);
        return;
      }

      await this.fetchLampiran();
      this.lastFetchedNotaId = this.notaId;
    },

    async fetchLampiran() {

      this.loading = true;
      this.error = null;
      this.lampiranList = [];

      try {
        const response = await fetch(`/nota/lampiran/${this.notaId}`);
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }

        const result = await response.json();

        if (result.success && Array.isArray(result.data)) {
          this.lampiranList = result.data;
        } else {
          this.lampiranList = [];
        }
      } catch (error) {
        console.error('Error fetching lampiran:', error);
        this.error = error.message || 'Gagal memuat lampiran';
        this.lampiranList = [];
      } finally {
        this.loading = false;
      }
    },

    formatDate(dateStr) {
      return new Date(dateStr).toLocaleString();
    },

    closeModal() {
      this.$emit('close');
      this.lastFetchedNotaId = null;
    }
  }
};
</script>
