<template>
  <Head title="Persetujuan" />
    <AuthenticatedLayout>
      <div class="pt-6 sm:pt-24 mx-2 sm:px-2">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
          <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg sm:text-xl font-semibold text-gray-800">List Persetujuan Nota Dinas</h2>
            </div>
            <div class="space-y-3">
              <div v-for="nota in notas.data" :key="nota.id" class="border rounded-lg p-4 hover:shadow-md transition">
                <div class="grid grid-cols-2 md:grid-cols-12 gap-4">
                  <div class="md:col-span-2">
                    <div class="text-xs text-gray-500">Nomor</div>
                    <div class="font-medium">{{ nota.nomor_nota }}</div>
                  </div>
                  <div class="md:col-span-4">
                    <div class="text-xs text-gray-500">Perihal</div>
                    <div class="font-medium">{{ nota.perihal }}</div>
                  </div>
                  <div class="md:col-span-2">
                    <div class="text-xs text-gray-500">Tahapan</div>
                    <div>
                      <span class="px-2 py-1 text-sm font-semibold rounded-full bg-cyan-400 text-white">
                        {{ capitalizeFirstLetter(nota.tahap_saat_ini) }}
                      </span>
                    </div>
                  </div>                  
                  <div class="md:col-span-1">
                    <div class="text-xs text-gray-500">Tanggal</div>
                    <div>{{ formatDate(nota.tanggal_pengajuan) }}</div>
                  </div>                  
                  <div class="md:col-span-1">
                    <div class="text-xs text-gray-500">Status</div>
                    <div>
                      <span class="px-3 py-1 text-sm font-semibold rounded-full" 
                            :class="{
                              'bg-blue-500 text-white': nota.status === 'proses',
                              'bg-green-500 text-white': nota.status === 'disetujui',
                              'bg-red-500 text-white': nota.status === 'ditolak'
                            }">
                        {{ capitalizeFirstLetter(nota.status) }}
                      </span>
                    </div>
                  </div>                  
                  <div class="md:col-span-1 flex justify-end gap-2 items-center">
                    <Tooltip text="Riwayat Pengiriman" bgColor="bg-green-500">
                      <Link
                        :href="route('nota.pengiriman.history', nota.id)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-red-500 text-green-400 hover:bg-red-100"
                      >
                        <font-awesome-icon icon="paper-plane" />
                      </Link>
                    </Tooltip>
                    <Tooltip text="Riwayat Persetujuan" bgColor="bg-red-500">
                      <button
                        @click="openHistoriModal(nota.id)"
                        class="px-2 py-1 text-sm font-semibold rounded border border-green-500 text-red-400 hover:bg-green-100"
                      >
                        <font-awesome-icon icon="check" />
                      </button>
                    </Tooltip>
                  </div>
                </div>
              </div>
              
              <div v-if="notas.data.length === 0" class="border rounded-lg p-4 text-center">
                Belum ada nota dinas disetujui
              </div>
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
import Tooltip from '@/Components/Tooltip.vue';
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