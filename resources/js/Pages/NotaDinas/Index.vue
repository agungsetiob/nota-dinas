<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Tooltip from '@/Components/Tooltip.vue';
import NotaModal from '@/Pages/NotaDinas/Partials/NotaModal.vue';
import SendNotaModal from '@/Pages/NotaDinas/Partials/SendNotaModal.vue';
import LampiranModal from '@/Pages/NotaDinas/Partials/LampiranModal.vue';
import DeleteModal from '@/Pages/NotaDinas/Partials/DeleteModal.vue';
import ApprovalModal from '@/Pages/NotaDinas/Partials/ApprovalModal.vue';
import ReturnNotaModal from '@/Pages/NotaDinas/Partials/ReturnNotaModal.vue';
import SearchInput from '@/Components/SearchInput.vue';
import SuccessFlash from '@/Components/SuccessFlash.vue';

const search = ref('');
watch(search, (val) => {
  router.get(route('nota-dinas.index'), { search: val }, { preserveState: true, replace: true });
});

const props = defineProps({
  notas: Object,
  userRole: String,
});

const flash = computed(() => page.props.flash || {});
const clearFlash = () => {
  flash.value.success = null;
};

const page = usePage();
const authUser = computed(() => page.props.auth.user);

const isNotaModalOpen = ref(false);
const isEditMode = ref(false);
const selectedNota = ref(null);

const isSendModalOpen = ref(false);
const isLampiranModalOpen = ref(false);
const isApprovalModalOpen = ref(false);
const isReturnModalOpen = ref(false);
const showDeleteModal = ref(false);

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

const openReturnModal = (nota) => {
  selectedNota.value = nota;
  isReturnModalOpen.value = true;
};

const closeReturnModal = () => {
  isReturnModalOpen.value = false;
  selectedNota.value = null;
};

const openDeleteModal = (nota) => {
  selectedNota.value = nota;
  showDeleteModal.value = true;
}
const closeDeleteModal = () => {
  showDeleteModal.value = false;
  selectedNota.value = null;
}
</script>

<template>

  <Head title="Daftar Nota Dinas" />
  <AuthenticatedLayout>
    <SuccessFlash :flash="flash" @clearFlash="clearFlash" />
    <div class="pt-6 sm:pt-24 mx-2 sm:px-2">
      <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Nota Dinas</h2>
            <template v-if="authUser.role === 'skpd'">
              <button @click="openCreateModal"
                class="inline-flex items-center px-3 sm:px-4 py-2 bg-indigo-500 text-white text-sm sm:text-base font-medium rounded hover:bg-indigo-700">
                + Tambah Nota
              </button>
            </template>
          </div>
          <div class="space-y-3">
            <SearchInput v-model:search="search" />
            <div v-for="nota in notas.data" :key="nota.id" class="border rounded-lg p-4 hover:shadow-md transition">
              <div class="grid grid-cols-2 md:grid-cols-12 gap-4">
                <div class="md:col-span-2">
                  <div class="text-xs text-gray-500">Nomor</div>
                  <p class="font-medium break-all">{{ nota.nomor_nota }}</p>
                </div>
                <div class="md:col-span-3">
                  <div class="text-xs text-gray-500">Perihal</div>
                  <div class="font-medium">{{ nota.perihal }}</div>
                </div>
                <div class="md:col-span-1">
                  <div class="text-xs text-gray-500">Posisi</div>
                  <div>
                    <span class="px-2 py-1 text-sm font-semibold rounded-full transition bg-cyan-400 text-white hover:bg-cyan-600">
                      {{ nota.tahap_saat_ini }}
                    </span>
                  </div>
                </div>
                <div class="md:col-span-1">
                  <div class="text-xs text-gray-500">Tanggal</div>
                  <div>{{ formatDate(nota.tanggal_pengajuan) }}</div>
                </div>
                <div class="md:col-span-2">
                  <div class="text-xs text-gray-500">Status</div>
                  <div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full transition" :class="statusClass(nota.status)">
                      {{ capitalize(nota.status) }}
                    </span>
                  </div>
                </div>
                <div class="flex justify-end gap-2 md:col-span-2 items-center">
                  <template v-if="authUser.role === 'skpd' && (nota.status === 'draft' || nota.status === 'dikembalikan')">
                    <Tooltip text="Edit Nota Dinas" bgColor="bg-blue-500">
                      <button @click="openEditModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-blue-500 text-blue-400 hover:bg-blue-100">
                        <font-awesome-icon icon="edit" />
                      </button>
                    </Tooltip>
                    <Tooltip text="Delete Nota Dinas" bgColor="bg-red-500">
                      <button @click="openDeleteModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-red-500 text-red-500 hover:bg-red-100">
                        <font-awesome-icon icon="trash" />
                      </button>
                    </Tooltip>
                    <Tooltip text="Kirim ke Asisten" bgColor="bg-green-500">
                      <button @click="openSendModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-green-500 text-green-600 hover:bg-green-100">
                        <font-awesome-icon icon="paper-plane" />
                      </button>
                    </Tooltip>
                  </template>
                  <template v-if="(authUser.role === 'asisten' && nota.tahap_saat_ini === 'asisten') || (authUser.role === 'sekda' && nota.tahap_saat_ini === 'sekda')">
                    <Tooltip text="Kirim Nota Dinas" bgColor="bg-green-500">
                      <button @click="openSendModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-green-500 text-green-600 hover:bg-green-100">
                        <font-awesome-icon icon="paper-plane" />
                      </button>
                    </Tooltip>
                    <Tooltip text="Pengembalian Nota Dinas" bgColor="bg-red-500">
                      <button 
                        @click="openReturnModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-red-500 text-red-600 hover:bg-red-100"
                      >
                        <font-awesome-icon icon="undo" />
                      </button>
                    </Tooltip>
                  </template>
                  <template v-else-if="authUser.role === 'bupati' && nota.tahap_saat_ini === 'bupati'">
                    <Tooltip text="Pengembalian Nota Dinas" bgColor="bg-red-500">
                      <button @click="openReturnModal(nota)"
                        class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-red-500 text-red-600 hover:bg-red-100">
                        <font-awesome-icon icon="undo" />
                      </button>
                    </Tooltip>
                    <Tooltip text="Setujui atau Tolak" bgColor="bg-violet-500">
                      <button @click="openApprovalModal(nota)"
                        class="px-3 py-1 text-sm font-semibold rounded border transition border-blue-500 text-gray-600 hover:bg-blue-200">
                        <font-awesome-icon icon="check" class="text-green-500" /> or <font-awesome-icon icon="x"
                          class="text-red-500" />
                      </button>
                    </Tooltip>
                  </template>
                  <Tooltip text="List Lampiran" bgColor="bg-gray-500">
                    <button @click="openLampiranModal(nota)"
                      class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-gray-500 text-gray-600 hover:bg-gray-200">
                      <font-awesome-icon icon="paperclip" />
                    </button>
                  </Tooltip>
                  <Tooltip text="Riwayat Pengiriman" bgColor="bg-yellow-500">
                    <Link
                      :href="route('nota.pengiriman.history', nota.id)"
                      class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-yellow-500 text-yellow-400 hover:bg-yellow-100"
                    >
                      <font-awesome-icon icon="clock-rotate-left" />
                    </Link>
                  </Tooltip>
                </div>
              </div>
            </div>
            <div v-if="notas.data.length === 0" class="border rounded-lg p-4 text-center text-red-500">
              Tidak ada nota dinas
            </div>
          </div>
          <Pagination :links="notas.links" :meta="{ from: notas.from, to: notas.to, total: notas.total }" />
        </div>
      </div>
    </div>
    <NotaModal
      :show="isNotaModalOpen" :isEdit="isEditMode" :notaData="selectedNota"
      @close="closeNotaModal"/>
    <SendNotaModal 
      :show="isSendModalOpen" 
      :notaId="selectedNota?.id" 
      :userRole="authUser.role"
      @close="closeSendModal" />
    <LampiranModal 
      :show="isLampiranModalOpen" 
      :notaId="selectedNota?.id" 
      @close="closeLampiranModal" />
    <ApprovalModal 
      :show="isApprovalModalOpen" 
      :notaId="selectedNota?.id"
      @close="closeApprovalModal" />
      <ReturnNotaModal
        :show="isReturnModalOpen" 
        :notaId="selectedNota?.id"
        @close="closeReturnModal" 
      />
    <DeleteModal 
      :show="showDeleteModal"
      :nota="selectedNota"
      @close="closeDeleteModal"
    />
  </AuthenticatedLayout>
</template>
