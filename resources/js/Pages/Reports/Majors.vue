<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    majors: Array,
});

const selectedMajor = ref('all');
const selectedStatus = ref('all');

const getExportUrl = (format) => {
    return route('reports.majors.export', {
        major_id: selectedMajor.value,
        status: selectedStatus.value,
        format: format
    });
};

const getProgressColor = (percentage) => {
    if (percentage >= 80) return 'bg-green-500';
    if (percentage >= 50) return 'bg-yellow-400';
    return 'bg-red-500';
};

const getProgressTextClass = (percentage) => {
    if (percentage >= 80) return 'text-green-700';
    if (percentage >= 50) return 'text-yellow-700';
    return 'text-red-700';
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Statistik Jurusan" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Statistik Pembayaran per Jurusan</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Statistik Tagihan Berdasarkan Jurusan</h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih jurusan di bawah ini untuk melihat detail masing-masing siswa.</p>
                    </div>
                </div>

                <!-- Panel Ekspor -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Ekspor Laporan Siswa</h3>
                            <p class="text-sm text-gray-500">Cetak PDF atau Excel dengan filter jurusan dan status pembayaran.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Jurusan</label>
                            <select v-model="selectedMajor" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">Semua Jurusan</option>
                                <option v-for="m in majors" :key="m.id" :value="m.id">{{ m.code }} - {{ m.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pembayaran</label>
                            <select v-model="selectedStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">Semua Status</option>
                                <option value="lunas">Lunas</option>
                                <option value="belum_lunas">Belum Lunas (Nyicil)</option>
                                <option value="belum_bayar">Belum Bayar</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 justify-end border-t border-gray-50 pt-4">
                        <a :href="getExportUrl('excel')" target="_blank" class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Unduh Excel
                        </a>
                        <a :href="getExportUrl('pdf')" target="_blank" class="flex items-center gap-2 px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Cetak PDF
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <div 
                        v-for="major in majors" 
                        :key="major.id"
                        class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl p-6 shadow-sm border border-indigo-100 flex flex-col"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-xl font-black text-indigo-900">{{ major.code }}</h4>
                                <p class="text-sm font-medium text-indigo-700 mt-0.5">{{ major.name }}</p>
                                <div class="mt-2 text-xs font-bold bg-indigo-100 text-indigo-800 px-2.5 py-1 rounded-md inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Total: {{ major.total }} Siswa
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold" :class="getProgressTextClass(major.percentage)">{{ major.percentage }}%</span>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Lunas</p>
                            </div>
                        </div>

                        <div class="w-full bg-indigo-100 rounded-full h-2.5 mb-5 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all duration-1000 ease-out" 
                                 :class="getProgressColor(major.percentage)" 
                                 :style="`width: ${major.percentage}%`">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 text-center mt-auto">
                            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-50">
                                <p class="text-xs text-gray-500 font-bold mb-1">Lunas</p>
                                <p class="text-lg font-black text-green-600">{{ major.lunas }}</p>
                            </div>
                            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-50">
                                <p class="text-xs text-gray-500 font-bold mb-1">Belum Lunas</p>
                                <p class="text-lg font-black text-yellow-500">{{ major.belum_lunas }}</p>
                            </div>
                            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-50">
                                <p class="text-xs text-gray-500 font-bold mb-1">Belum Bayar</p>
                                <p class="text-lg font-black text-red-500">{{ major.belum_bayar }}</p>
                            </div>
                        </div>
                        <div class="mt-auto pt-4 border-t border-gray-100 flex justify-end">
                            <Link :href="route('reports.major.detail', major.id)" class="text-center py-2 px-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-sm font-bold transition-colors w-full">
                                Detail Siswa
                            </Link>
                        </div>
                    </div>

                    <div v-if="!majors || majors.length === 0" class="col-span-full bg-white rounded-2xl p-8 text-center border border-gray-100">
                        <p class="text-gray-500">Belum ada data jurusan.</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
