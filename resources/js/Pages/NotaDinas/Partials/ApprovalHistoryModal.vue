<template>
    <Modal :show="show" @close="closeModal" maxWidth="6xl">
      <div class="rounded-lg shadow-lg p-6 w-full">
        <div class="flex justify-between items-center border-b pb-3">
          <h2 class="text-lg font-semibold text-gray-800">Histori Persetujuan Nota Dinas</h2>
          <button @click="closeModal">✖</button>
        </div>
  
        <div class="overflow-x-auto">
          <table class="w-full table-auto text-left">
            <thead>
              <tr class="bg-gray-200">
                <th class="px-4 py-2">Tanggal Update</th>
                <th class="px-4 py-2">Oleh</th>
                <th class="px-4 py-2">SKPD</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Catatan</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="5" class="px-4 py-2 text-center">Loading...</td>
              </tr>
              <tr v-else-if="histories.length === 0">
                <td colspan="5" class="px-4 py-2 text-center">Tidak ada histori persetujuan.</td>
              </tr>
              <tr v-for="history in histories" :key="history.id" class="hover:bg-gray-100 transition">
                <td class="px-4 py-2">{{ formatDateTime(history.tanggal_update) }}</td>
                <td class="px-4 py-2">{{ history.approver?.name }} ({{ history.role_approver }})</td>
                <td class="px-4 py-2">{{ history.skpd?.nama_skpd }}</td>
                <td class="px-4 py-2">
                  <span class="px-3 py-1 text-sm font-semibold rounded-full"
                        :class="{
                          'bg-green-500 text-white': history.status === 'disetujui',
                          'bg-red-500 text-white': history.status === 'ditolak',
                          'bg-gray-500 text-white': history.status === 'dikembalikan'
                        }">
                    {{ capitalizeFirstLetter(history.status) }}
                  </span>
                </td>
                <td class="px-4 py-2">{{ history.catatan_terakhir || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </Modal>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import Modal from '@/Components/Modal.vue';
  
  const props = defineProps({
    show: {
      type: Boolean,
      required: true
    },
  });
  
  const emit = defineEmits(['close']);
  
  const loading = ref(false);
  const histories = ref([]);
  
  const formatDateTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString('id-ID');
  };
  
  const capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
  };
  
  const closeModal = () => {
    emit('close');
  };
  
  const fetchApprovalHistory = async (notaId) => {
    loading.value = true;
    histories.value = [];
  
    try {
      const response = await fetch(`/api/histori-persetujuan/${notaId}`);
      const data = await response.json();
      
      if (data.success) {
        histories.value = data.data;
      }
    } catch (error) {
      console.error('Error fetching approval history:', error);
    } finally {
      loading.value = false;
    }
  };
  
  defineExpose({
    fetchApprovalHistory
  });
  </script>