<template>
  <Head title="Persetujuan" />
    <AuthenticatedLayout>
      <div class="pt-6 sm:pt-24 mx-2 sm:px-2">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
          <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg sm:text-xl font-semibold text-gray-800">List Persetujuan Nota Dinas</h2>
            </div>
  
            <div v-if="$page.props.flash.error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-600 rounded">
              <p>{{ $page.props.flash.error }}</p>
            </div>
  
            <div class="overflow-x-auto">
              <table class="table-auto w-full">
                <thead>
                  <tr class="bg-gray-300 text-left">
                    <th class="px-4 py-2">Nomor</th>
                    <th class="px-4 py-2">Perihal</th>
                    <th class="px-4 py-2">Tahapan</th>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="nota in notas.data" :key="nota.id" class="hover:bg-gray-100 transition">
                    <td class="px-4 py-2">{{ nota.nomor_nota }}</td>
                    <td class="px-4 py-2">{{ nota.perihal }}</td>
                    <td class="px-4 py-2">
                      <span class="px-2 py-1 text-sm font-semibold rounded-full bg-cyan-400 text-white">
                        {{ capitalizeFirstLetter(nota.tahap_saat_ini) }}
                      </span>
                    </td>
                    <td class="px-4 py-2">{{ formatDate(nota.tanggal_pengajuan) }}</td>
                    <td class="px-4 py-2">
                      <span class="px-3 py-1 text-sm font-semibold rounded-full" 
                            :class="{
                              'bg-blue-500 text-white': nota.status === 'proses',
                              'bg-green-500 text-white': nota.status === 'disetujui',
                              'bg-red-500 text-white': nota.status === 'ditolak'
                            }">
                        {{ capitalizeFirstLetter(nota.status) }}
                      </span>
                    </td>
                    <td class="px-4 py-2 flex gap-2">
                      <Link :href="route('nota.pengiriman.history', nota.id)"
                        class="px-3 py-1 text-xs sm:text-sm font-semibold rounded border transition border-red-500 text-red-400 hover:bg-red-100">
                      Histori
                      </Link>
                      <button @click="openHistoriModal(nota.id)" 
                              class="px-3 py-1 text-sm font-semibold rounded border border-yellow-500 text-yellow-400 hover:bg-yellow-100">
                        Histori
                      </button>
                    </td>
                  </tr>
                  <tr v-if="notas.data.length === 0">
                    <td colspan="6" class="px-4 py-2 text-center">Belum ada nota dinas disetujui</td>
                  </tr>
                </tbody>
              </table>
            </div>
  
            <div class="mt-4">
              <Pagination :links="notas.links" />
            </div>
          </div>
        </div>
      </div>
  
      <ApprovalHistoryModal 
        :show="showHistoriModal" 
        @close="closeHistoriModal"
        ref="approvalHistoryModal"
        />
    </AuthenticatedLayout>
</template>
  
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ApprovalHistoryModal from '@/Pages/NotaDinas/Partials/ApprovalHistoryModal.vue';
import { ref } from 'vue';

const props = defineProps({
  notas: Object,
});

const showHistoriModal = ref(false);
const approvalHistoryModal = ref(null);

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
};
const capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
  };

const openHistoriModal = async (notaId) => {
  showHistoriModal.value = true;
  approvalHistoryModal.value?.fetchApprovalHistory(notaId);
};

const closeHistoriModal = () => {
  showHistoriModal.value = false;
};
</script>