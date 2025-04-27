<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

import NotaModal from '@/Pages/NotaDinas/Partials/NotaModal.vue';
import SendNotaModal from '@/Pages/NotaDinas/Partials/SendNotaModal.vue';
import LampiranModal from '@/Pages/NotaDinas/Partials/LampiranModal.vue';
import ApprovalModal from '@/Pages/NotaDinas/Partials/ApprovalModal.vue';
import ReturnNotaModal from '@/Pages/NotaDinas/Partials/ReturnNotaModal.vue';

const props = defineProps({
  notas: Object,
  userRole: String,
});

const page = usePage();
const authUser = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash || {});

const isNotaModalOpen = ref(false);
const isEditMode = ref(false);
const selectedNota = ref(null);

const isSendModalOpen = ref(false);
const isLampiranModalOpen = ref(false);
const isApprovalModalOpen = ref(false);
const isReturnModalOpen = ref(false);

function formatDate(dateStr) {
  const date = new Date(dateStr);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}-${month}-${year}`;
}

function capitalize(str) {
  return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
}

function statusClass(status) {
  switch (status) {
    case 'draft':
      return 'bg-gray-500 text-white hover:bg-gray-600';
    case 'proses':
      return 'bg-blue-500 text-white hover:bg-blue-600';
    case 'disetujui':
      return 'bg-green-500 text-white hover:bg-green-600';
    case 'ditolak':
      return 'bg-red-500 text-white hover:bg-red-600';
    case 'dikembalikan':
      return 'bg-yellow-500 text-white hover:bg-yellow-600';
    default:
      return '';
  }
}

function openCreateModal() {
  isEditMode.value = false;
  selectedNota.value = {};
  isNotaModalOpen.value = true;
}

function openEditModal(nota) {
  isEditMode.value = true;
  selectedNota.value = nota;
  isNotaModalOpen.value = true;
}
function closeNotaModal() {
  isNotaModalOpen.value = false;
}

function openSendModal(nota) {
  selectedNota.value = nota;
  isSendModalOpen.value = true;
}

function closeSendModal() {
  isSendModalOpen.value = false;
}

function openLampiranModal(nota) {
  selectedNota.value = nota;
  isLampiranModalOpen.value = true;
}

function closeLampiranModal() {
  isLampiranModalOpen.value = false;
}

function openApprovalModal(nota) {
  selectedNota.value = nota;
  isApprovalModalOpen.value = true;
}

function closeApprovalModal() {
  isApprovalModalOpen.value = false;
}

function openReturnModal(nota) {
  selectedNota.value = nota;
  isReturnModalOpen.value = true;
}

function closeReturnModal() {
  isReturnModalOpen.value = false;
}

function deleteNota(nota) {
  if (confirm('Yakin ingin menghapus?')) {
    Inertia.delete(route('nota-dinas.destroy', nota.id));
  }
}
</script>

<template>

  <Head title="Daftar Nota Dinas" />
  <AuthenticatedLayout>
    <div class="pb-6 pt-6 sm:pt-24 mx-2 sm:px-2">
      <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Nota Dinas</h2>
            <template v-if="authUser.role === 'skpd'">
              <button @click="openCreateModal"
                class="inline-flex items-center px-3 sm:px-4 py-2 bg-indigo-500 text-white text-sm sm:text-base font-medium rounded hover:bg-indigo-700">
                + Tambah Nota
              </button>
            </template>
          </div>
          <div v-if="flash.success" class="mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
              <span class="block sm:inline">{{ flash.success }}</span>
              <button @click="flash.success = null" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-700" role="button" xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20">
                  <title>Close</title>
                  <path
                    d="M14.348 5.652a1 1 0 00-1.414-1.414L10 7.172 7.066 4.238a1 1 0 10-1.414 1.414L8.586 8.586 5.652 11.52a1 1 0 101.414 1.414L10 10.828l2.934 2.934a1 1 0 001.414-1.414L11.414 8.586l2.934-2.934z" />
                </svg>
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="table-auto w-full">
              <thead>
                <tr class="bg-gray-300 text-left">
                  <th class="px-3 py-2">Nomor</th>
                  <th class="px-3 py-2">Perihal</th>
                  <th class="px-3 py-2">Posisi</th>
                  <th class="px-3 py-2">Tanggal</th>
                  <th class="px-3 py-2">Status</th>
                  <th class="px-3 py-2"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="nota in notas.data" :key="nota.id" class="hover:bg-red-50 transition even:bg-gray-100">
                  <td class="px-3 py-2">{{ nota.nomor_nota }}</td>
                  <td class="px-3 py-2">{{ nota.perihal }}</td>
                  <td class="px-3 py-2">
                    <span
                      class="px-2 py-1 text-sm font-semibold rounded-full transition bg-cyan-400 text-white hover:bg-cyan-600">
                      {{ nota.tahap_saat_ini }}
                    </span>
                  </td>
                  <td class="px-3 py-2">{{ formatDate(nota.tanggal_pengajuan) }}</td>
                  <td class="px-3 py-2">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full transition"
                      :class="statusClass(nota.status)">
                      {{ capitalize(nota.status) }}
                    </span>
                  </td>
                  <td class="px-3 py-4 flex gap-2">
                    <template
                      v-if="authUser.role === 'skpd' && (nota.status === 'draft' || nota.status === 'dikembalikan')">
                      <button @click="openEditModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-blue-500 text-blue-400 hover:bg-blue-100">
                        <font-awesome-icon icon="edit" />
                      </button>
                      <button @click="deleteNota(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-red-500 text-red-500 hover:bg-red-100">
                        <font-awesome-icon icon="trash" />
                      </button>
                      <button @click="openSendModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-green-500 text-green-600 hover:bg-green-100">
                        <font-awesome-icon icon="paper-plane" />
                      </button>
                    </template>
                    <template
                      v-if="(authUser.role === 'asisten' && nota.tahap_saat_ini === 'asisten') || (authUser.role === 'sekda' && nota.tahap_saat_ini === 'sekda')">
                      <button @click="openSendModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-green-500 text-green-600 hover:bg-green-100">
                        <font-awesome-icon icon="paper-plane" />
                      </button>
                      <button @click="openReturnModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-red-500 text-red-600 hover:bg-red-100">
                        <font-awesome-icon icon="undo" />
                      </button>
                    </template>
                    <template v-else-if="authUser.role === 'bupati' && nota.tahap_saat_ini === 'bupati'">
                      <button @click="openReturnModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-red-500 text-red-600 hover:bg-red-100">
                        <font-awesome-icon icon="undo" />
                      </button>
                      <button @click="openApprovalModal(nota)"
                        class="px-3 py-1 text-sm font-semibold rounded border transition border-blue-500 text-gray-600 hover:bg-blue-200">
                        <font-awesome-icon icon="check" class="text-green-500" /> or <font-awesome-icon icon="x"
                          class="text-red-500" />
                      </button>
                    </template>
                    <button @click="openLampiranModal(nota)"
                      class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-gray-500 text-gray-600 hover:bg-gray-200">
                      <font-awesome-icon icon="paperclip" />
                    </button>
                    <Link :href="route('nota.pengiriman.history', nota.id)"
                      class="px-3 py-1 text-xs sm:text-sm font-semibold rounded border transition border-yellow-500 text-yellow-400 hover:bg-yellow-100">
                    Histori
                    </Link>
                  </td>
                </tr>
                <tr v-if="notas.data.length === 0">
                  <td colspan="6" class="px-3 py-2 text-center">Belum ada nota dinas</td>
                </tr>
              </tbody>
            </table>
          </div>
          <Pagination :links="notas.links" :meta="{ from: notas.from, to: notas.to, total: notas.total }" />
        </div>
      </div>
    </div>

    <NotaModal v-if="isNotaModalOpen" :show="isNotaModalOpen" :isEdit="isEditMode" :notaData="selectedNota"
      @close="closeNotaModal" />
    <SendNotaModal v-if="isSendModalOpen" :isOpen="isSendModalOpen" :notaId="selectedNota?.id" :userRole="authUser.role"
      @close="closeSendModal" />
    <LampiranModal v-if="isLampiranModalOpen" :isOpen="isLampiranModalOpen" :notaId="selectedNota?.id"
      @close="closeLampiranModal" />
    <ApprovalModal v-if="isApprovalModalOpen" :isOpen="isApprovalModalOpen" :notaId="selectedNota?.id"
      @close="closeApprovalModal" />
    <ReturnNotaModal v-if="isReturnModalOpen" :isOpen="isReturnModalOpen" :notaId="selectedNota?.id"
      @close="closeReturnModal" />
  </AuthenticatedLayout>
</template>
