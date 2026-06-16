<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    students: Object,
    filters: Object,
});

const searchForm = useForm({
    search: props.filters?.search || '',
    per_page: props.filters?.per_page || '10'
});

const onSearch = () => {
    searchForm.get(route('payments.index'), { preserveState: true });
};
</script>

<template>
    <Head title="Kasir Pembayaran" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kasir Pembayaran (Cari Siswa)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100 p-6 md:p-8 mb-8 text-center">
                    <h3 class="text-2xl font-bold text-[#1a237e] mb-2">Sistem Pembayaran Siswa</h3>
                    <p class="text-gray-500 mb-6">Silakan cari siswa berdasarkan Nama atau NISN untuk memproses pembayaran tagihan.</p>
                    
                    <div class="max-w-2xl mx-auto flex gap-3">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <TextInput 
                                v-model="searchForm.search" 
                                @keyup.enter="onSearch"
                                type="text" 
                                class="w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors text-base" 
                                placeholder="Masukkan Nama atau NISN..." 
                            />
                        </div>
                        <PrimaryButton @click="onSearch" class="py-3 px-6 rounded-xl text-base shadow-md hover:shadow-lg transition-all">
                            Cari Siswa
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Hasil Pencarian -->
                <div v-if="searchForm.search || students.data.length > 0" class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 font-bold w-16 text-center">No</th>
                                    <th class="px-6 py-4 font-bold">Data Siswa</th>
                                    <th class="px-6 py-4 font-bold">Kelas & Jurusan</th>
                                    <th class="px-6 py-4 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(student, index) in students.data" :key="student.id" class="bg-white border-b last:border-0 hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 text-center font-medium">{{ (students.current_page - 1) * students.per_page + index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 text-base">{{ student.name }}</div>
                                        <div class="text-sm text-gray-500">NISN: {{ student.nisn }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ student.classroom?.level }} {{ student.classroom?.name }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">{{ student.classroom?.major?.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <Link :href="route('payments.process', student.id)" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:from-green-600 hover:to-emerald-700 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            Pilih & Bayar
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="students.data.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        Data siswa tidak ditemukan. Coba kata kunci pencarian yang lain.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-6 md:px-8 border-t border-gray-100 flex justify-between items-center" v-if="students.links && students.data.length > 0">
                        <span class="text-sm text-gray-500">Hal {{ students.current_page }} dari {{ students.last_page }}</span>
                        <div class="flex space-x-1">
                            <template v-for="(link, p) in students.links" :key="p">
                                <a v-if="link.url" :href="link.url" class="px-3 py-1.5 border rounded-md text-sm transition-colors" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'" v-html="link.label"></a>
                                <span v-else class="px-3 py-1.5 border rounded-md text-sm text-gray-400 bg-gray-50" v-html="link.label"></span>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
