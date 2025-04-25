<script setup>
import { Head, usePage, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    users: Object,
    totalUsers: Number,
    totalSkpds: Number,
    notaDinas: Number,
    notaSelesai: Number
});

const page = usePage();
const authUser = computed(() => page.props.auth.user);

function toggleStatus(userId, currentStatus) {
    const form = useForm({
        status: currentStatus ? 0 : 1,
    });

    form.patch(route('users.toggle-status', userId), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header></template>

        <div class="py-6 mx-2 sm:px-2">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Daftar Pengguna</h2>
                        <Link
                           :href="route('register')"
                           preserve-scroll
                           class="inline-flex items-center px-3 sm:px-4 py-2 bg-indigo-500 text-white text-sm sm:text-base font-medium rounded hover:bg-indigo-700">
                            + Tambah Pengguna
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
                            <thead>
                                <tr class="bg-gray-300 text-left">
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Jabatan</th>
                                    <th class="px-4 py-2">SKPD</th>
                                    <th class="px-4 py-2">Role</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-red-50 transition even:bg-gray-100">
                                    <td class="px-4 py-2">{{ user.name }}</td>
                                    <td class="px-4 py-2">{{ user.email }}</td>
                                    <td class="px-4 py-2">{{ user.jabatan }}</td>
                                    <td class="px-4 py-2">{{ user.skpd?.nama_skpd || '-' }}</td>
                                    <td class="px-4 py-2">{{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}</td>
                                    <td class="px-4 py-2">
                                        <button
                                            @click="toggleStatus(user.id, user.status)"
                                            type="button"
                                            :class="[
                                            'relative inline-flex items-center h-8 rounded-full focus:outline-none transition-colors duration-300',
                                            user.status ? 'bg-green-500 w-16' : 'bg-red-500 w-24'
                                            ]"
                                        >
                                            <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white">
                                                <span :class="user.status ? 'ml-5' : 'mr-4'">
                                                    {{ user.status ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </span>
                                            <span
                                            class="absolute left-1 top-1 h-6 w-6 bg-white rounded-full shadow transform transition duration-300"
                                            :class="user.status ? 'translate-x-15' : 'translate-x-16'"
                                            ></span>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="5" class="px-4 py-2 text-center">Belum ada pengguna</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-end mt-4">
                        <nav class="inline-flex overflow-hidden rounded-lg border border-gray-200 shadow-sm" aria-label="Pagination">
                        <template v-for="(link, index) in users.links" :key="index">
                            <template v-if="link.url">
                            <Link
                                :href="link.url"
                                preserve-scroll
                                v-html="link.label"
                                :class="[
                                'px-4 py-2 text-sm font-medium transition-colors duration-200',
                                link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50'
                                ]"
                            />
                            </template>
                            <template v-else>
                            <span
                                v-html="link.label"
                                class="px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100"
                            />
                            </template>
                        </template>
                        </nav>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
