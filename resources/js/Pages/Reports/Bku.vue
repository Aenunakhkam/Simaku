<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    academicYear: Object,
});

const filterType = ref('academic_year'); // 'academic_year', 'all', 'monthly'
const selectedMonth = ref(new Date().getMonth() + 1);
const selectedYear = ref(new Date().getFullYear());

const months = [
    { value: 1, name: 'Januari' },
    { value: 2, name: 'Februari' },
    { value: 3, name: 'Maret' },
    { value: 4, name: 'April' },
    { value: 5, name: 'Mei' },
    { value: 6, name: 'Juni' },
    { value: 7, name: 'Juli' },
    { value: 8, name: 'Agustus' },
    { value: 9, name: 'September' },
    { value: 10, name: 'Oktober' },
    { value: 11, name: 'November' },
    { value: 12, name: 'Desember' }
];

// Generate years from current year down to 2020
const years = computed(() => {
    const currentYear = new Date().getFullYear();
    const list = [];
    for (let y = currentYear; y >= 2020; y--) {
        list.push(y);
    }
    return list;
});

// Compute the PDF download URL based on options
const downloadUrl = computed(() => {
    if (filterType.value === 'all') {
        return route('reports.bku.pdf', { all: 'true' });
    } else if (filterType.value === 'monthly') {
        return route('reports.bku.pdf', { month: selectedMonth.value, year: selectedYear.value });
    }
    return route('reports.bku.pdf');
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Cetak BKU" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Buku Kas Umum (BKU)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Cetak Buku Kas Umum</h3>
                            <p class="text-sm text-gray-500 mt-1">Pilih metode penyaringan periode transaksi Buku Kas Umum sekolah.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Option: Academic Year -->
                        <label class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors" :class="{'border-indigo-500 bg-indigo-50/10': filterType === 'academic_year'}">
                            <input type="radio" v-model="filterType" value="academic_year" class="mt-1 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <div>
                                <span class="block font-bold text-gray-800">Tahun Ajaran Aktif</span>
                                <span class="block text-xs text-gray-500 mt-1">
                                    Hanya mencetak transaksi dalam tahun ajaran aktif saat ini:
                                    <strong class="text-indigo-600">{{ academicYear ? academicYear.name : 'Belum ditentukan' }}</strong>
                                </span>
                            </div>
                        </label>

                        <!-- Option: All Time -->
                        <label class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors" :class="{'border-indigo-500 bg-indigo-50/10': filterType === 'all'}">
                            <input type="radio" v-model="filterType" value="all" class="mt-1 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <div>
                                <span class="block font-bold text-gray-800">Semua Periode (All-Time)</span>
                                <span class="block text-xs text-gray-500 mt-1">Mencetak seluruh transaksi pemasukan dan pengeluaran kas sejak awal tanpa batasan waktu.</span>
                            </div>
                        </label>

                        <!-- Option: Monthly -->
                        <label class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors" :class="{'border-indigo-500 bg-indigo-50/10': filterType === 'monthly'}">
                            <input type="radio" v-model="filterType" value="monthly" class="mt-1 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <div class="flex-1">
                                <span class="block font-bold text-gray-800">Berdasarkan Bulan</span>
                                <span class="block text-xs text-gray-500 mt-1 mb-3">Mencetak rekap transaksi BKU khusus untuk bulan dan tahun tertentu.</span>
                                
                                <div v-if="filterType === 'monthly'" class="grid grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Bulan</label>
                                        <select v-model="selectedMonth" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.name }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Tahun</label>
                                        <select v-model="selectedYear" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-8 flex justify-end">
                        <a 
                            :href="downloadUrl" 
                            target="_blank" 
                            class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all transform active:scale-98"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Cetak BKU PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
