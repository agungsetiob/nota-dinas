<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

// Terima data SKPD dari controller
const props = defineProps({
    skpds: Array,
});

const form = useForm({
    name: '',
    email: '',
    jabatan: '',
    skpd_id: '',
    role: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Register" />

        <div class="flex justify-center pt-8 sm:pt-24">
            <div class="w-full bg-white p-8 shadow-md rounded-lg max-w-3xl mx-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autocomplete="username"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="jabatan" value="Jabatan" />
                            <TextInput
                                id="jabatan"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.jabatan"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.jabatan" />
                        </div>

                        <div>
                            <InputLabel for="skpd_id" value="Pilih SKPD" />
                            <select
                                id="skpd_id"
                                v-model="form.skpd_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >
                                <option value="">-- Pilih SKPD --</option>
                                <option
                                    v-for="skpd in props.skpds"
                                    :key="skpd.id"
                                    :value="skpd.id"
                                >
                                    {{ skpd.nama_skpd }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.skpd_id" />
                        </div>

                        <div>
                            <InputLabel for="role" value="Peran / Role" />
                            <select
                                id="role"
                                v-model="form.role"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >
                                <option value="">-- Pilih Role --</option>
                                <option value="skpd">SKPD</option>
                                <option value="asisten">Asisten</option>
                                <option value="sekda">Sekda</option>
                                <option value="bupati">Bupati</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.role" />
                        </div>

                        <div>
                            <InputLabel for="password" value="Password" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Confirm Password" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Register
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
