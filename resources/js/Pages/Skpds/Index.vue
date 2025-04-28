<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import SkpdModal from "@/Pages/Skpds/Partials/SkpdModal.vue";
import { computed, ref } from 'vue';

const props = defineProps({
    skpds: Object,
    asistens: Array
});

const page = usePage();
const authUser = computed(() => page.props.auth.user);

// modal control
const isModalOpen = ref(false);
const selectedSkpd = ref(null);

function openModal(skpd = null) {
    selectedSkpd.value = skpd;
    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
    selectedSkpd.value = null;
}

function toggleStatus(skpdId, currentStatus) {
    const form = useForm({
        status: currentStatus ? 0 : 1,
    });

    form.patch(route('skpds.toggle-status', skpdId), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="SKPD" />

    <AuthenticatedLayout>

        <div class="pt-6 sm:pt-24 mx-2 sm:px-2">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Daftar SKPD</h2>
                        <button
                            @click="openModal()"
                            class="inline-flex items-center px-3 sm:px-4 py-2 bg-indigo-500 text-white text-sm sm:text-base font-medium rounded hover:bg-indigo-700"
                        >
                            + Tambah SKPD
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
                            <thead>
                                <tr class="bg-gray-300 text-left">
                                    <th class="px-4 py-2">Nama SKPD</th>
                                    <th class="px-4 py-2">Asisten</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="skpd in skpds.data" :key="skpd.id" class="hover:bg-red-50 transition even:bg-gray-100">
                                    <td class="px-4 py-2">
                                        <button class="text-blue-600 hover:text-green-600" @click="openModal(skpd)">
                                            {{ skpd.nama_skpd }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-2">{{ skpd.asisten?.name ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <button
                                            @click="toggleStatus(skpd.id, skpd.status)"
                                            type="button"
                                            :class="[
                                                'relative z-0 inline-flex items-center h-8 rounded-full focus:outline-none transition-colors duration-300',
                                                skpd.status ? 'bg-green-500 w-16' : 'bg-red-500 w-24'
                                            ]"
                                        >
                                            <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white">
                                                <span :class="skpd.status ? 'ml-5' : 'mr-4'">
                                                    {{ skpd.status ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </span>
                                            <span
                                                class="absolute left-1 top-1 h-6 w-6 bg-white rounded-full shadow transform transition duration-300"
                                                :class="skpd.status ? 'translate-x-15' : 'translate-x-16'"
                                            ></span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <button
                                            @click="openModal(skpd)"
                                            class="px-2 py-1 text-xs sm:text-sm font-semibold rounded border transition border-blue-500 text-blue-400 w-20 hover:bg-blue-100"
                                        >
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="skpds.data.length === 0">
                                    <td colspan="4" class="px-4 py-2 text-center">Belum ada SKPD</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <Pagination
                        :links="skpds.links"
                        :meta="{ from: skpds.from, to: skpds.to, total: skpds.total }"
                    />
                </div>
            </div>
        </div>

        <SkpdModal
            v-if="isModalOpen"
            :show="isModalOpen"
            :skpd="selectedSkpd"
            :asistens="asistens"
            @close="closeModal"
        />
    </AuthenticatedLayout>
</template>
