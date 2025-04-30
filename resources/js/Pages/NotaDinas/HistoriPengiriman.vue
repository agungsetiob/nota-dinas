<template>
    <Head title="Histori Pengiriman" />
    <AuthenticatedLayout>
        <div class="pt-6 sm:pt-24 mx-2 sm:px-2">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700">Perihal:</h3>
                        <p class="text-gray-900 text-base mt-1">{{ nota.perihal }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm">
                            <thead>
                                <tr class="bg-gray-200 text-left">
                                    <th class="px-4 py-2 border">Tanggal</th>
                                    <th class="px-4 py-2 border">Jenis</th>
                                    <th class="px-4 py-2 border">Dari / Oleh</th>
                                    <th class="px-4 py-2 border">Ke</th>
                                    <th class="px-4 py-2 border">Catatan</th>
                                    <th class="px-4 py-2 border">Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in pengiriman" :key="item.id" class="even:bg-gray-100 hover:bg-gray-50 transition">
                                    <td class="px-4 py-2">{{ formatDate(item.tanggal_kirim) }}</td>
                                    <td class="px-4 py-2">Pengiriman</td>
                                    <td class="px-4 py-2 capitalize">{{ item.dikirim_dari }} - {{ item.pengirim?.name || '-' }}</td>
                                    <td class="px-4 py-2 capitalize">{{ item.dikirim_ke }}</td>
                                    <td class="px-4 py-2">{{ item.catatan || '-' }}</td>
                                    <td class="px-4 py-2">
                                        <button @click="openLampiranModal(item.id, 'pengiriman')" class="px-3 py-1 text-xs sm:text-sm font-semibold rounded border transition border-gray-500 text-blue-400 hover:bg-yellow-200">Lihat</button>
                                    </td>
                                </tr>

                                <tr v-if="pengiriman.length === 0">
                                    <td colspan="6" class="text-center py-4 text-gray-500">
                                        Belum ada histori.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        <button @click="goBack" class="text-blue-600 hover:underline text-sm">← Kembali</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lampiran Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-3xl p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Daftar Lampiran</h3>
                    <button @click="closeLampiranModal" class="text-gray-400 hover:text-gray-700">
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
                        <li v-if="lampiran.length === 0" class="text-gray-500">Tidak ada lampiran.</li>
                        <li
                            v-for="(file, index) in lampiran"
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    nota: Object,
    pengiriman: Array,
});

const showModal = ref(false);
const loading = ref(false);
const error = ref(null);
const lampiran = ref([]);
const selectedFile = ref(null);

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString();
};

const openLampiranModal = async (id, tipe) => {
    showModal.value = true;
    loading.value = true;
    error.value = null;
    lampiran.value = [];

    try {
        const response = await axios.get(`/nota/lampiran/${tipe}/${id}`);
        if (response.data.success) {
            lampiran.value = response.data.data;
        }
    } catch (err) {
        console.error('Error fetching lampiran:', err);
        error.value = err.message || 'Gagal memuat lampiran';
    } finally {
        loading.value = false;
    }
};

const closeLampiranModal = () => {
    showModal.value = false;
};

const openFilePreview = (file) => {
    selectedFile.value = file;
};

const closeFilePreview = () => {
    selectedFile.value = null;
};

const goBack = () => {
    window.history.back();
};
</script>