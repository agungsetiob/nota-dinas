<template>
  <div>
    <!-- Lampiran Modal -->
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-3xl p-6 relative">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold mb-4">Daftar Lampiran</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-700">
              ✖
          </button>
        </div>
        <ul class="list-disc pl-5 text-sm text-gray-700">
          <template v-if="loading">
            <li class="text-gray-500">Memuat lampiran...</li>
          </template>
          <template v-else-if="error">
            <li class="text-red-500">{{ error }}</li>
          </template>
          <template v-else>
            <li v-if="lampiranList.length === 0" class="text-gray-500">Tidak ada lampiran.</li>
            <li
              v-for="(file, index) in lampiranList"
              :key="index"
              class="mb-2 cursor-pointer text-blue-600 hover:underline"
              @click="openFilePreview(file)"
            >
              {{ file.name }}
              <span class="text-gray-500 text-xs"> ({{ formatDate(file.created_at) }})</span>
            </li>
          </template>
        </ul>
      </div>
    </div>

    <!-- Modal Preview Lampiran -->
    <div v-if="selectedFile" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl w-full h-full p-6 relative flex flex-col">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">{{ selectedFile.name }}</h3>
          <button @click="closeFilePreview" class="text-gray-400 hover:text-gray-700">
            ✖
          </button>
        </div>

        <div class="flex-1 overflow-hidden">
          <iframe :src="selectedFile.url" class="w-full h-full border-0" />
        </div>
      </div>
    </div>
  </div>
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
      lastFetchedNotaId: null,
      selectedFile: null
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
    },

    openFilePreview(file) {
      this.selectedFile = file;
    },

    closeFilePreview() {
      this.selectedFile = null;
    }
  }
};
</script>
