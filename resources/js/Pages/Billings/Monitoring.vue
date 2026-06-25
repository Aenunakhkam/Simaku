<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    students: Object,
    stats: Object,
    classrooms: Array,
    filters: Object,
});

const searchForm = useForm({ 
    search: props.filters?.search || '',
    classroom_id: props.filters?.classroom_id || '',
    per_page: props.filters?.per_page || '25',
    status: props.filters?.status || ''
});

const expandedStudentId = ref(null);

const toggleExpand = (studentId) => {
    if (expandedStudentId.value === studentId) {
        expandedStudentId.value = null;
    } else {
        expandedStudentId.value = studentId;
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(date);
};

const onSearch = () => {
    searchForm.get(route('billings.monitoring'), { preserveState: true });
};

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
};

import { computed } from 'vue';

const totalStudents = computed(() => {
    return (props.stats?.lunas || 0) + (props.stats?.nyicil || 0) + (props.stats?.belum_bayar || 0);
});

const getPercentage = (count) => {
    if (totalStudents.value === 0) return '0%';
    return Math.round((count / totalStudents.value) * 100) + '%';
};
</script>

<template>
    <Head title="Monitoring Tagihan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Monitoring Tagihan & Status Pembayaran</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- 3 Metric Cards for Monitoring -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Lunas -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 overflow-hidden shadow-sm rounded-2xl border border-green-200 p-6 relative group hover:-translate-y-1 transition-all">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="text-sm font-bold text-green-700 uppercase tracking-wider mb-2">Sudah Lunas</div>
                        <div class="text-4xl font-black text-green-600 flex items-center gap-2">
                            {{ stats?.lunas || 0 }} <span class="text-lg font-bold text-green-500">Siswa</span>
                            <span class="ml-2 px-2.5 py-1 bg-green-200 text-green-800 text-sm font-bold rounded-xl shadow-sm">{{ getPercentage(stats?.lunas || 0) }}</span>
                        </div>
                        <p class="text-xs text-green-600 mt-2 font-medium">Tagihan tahun ini telah terbayar penuh.</p>
                    </div>

                    <!-- Nyicil / Belum Lunas -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-100 overflow-hidden shadow-sm rounded-2xl border border-yellow-200 p-6 relative group hover:-translate-y-1 transition-all">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="text-sm font-bold text-orange-700 uppercase tracking-wider mb-2">Mencicil / Belum Lunas</div>
                        <div class="text-4xl font-black text-orange-600 flex items-center gap-2">
                            {{ stats?.nyicil || 0 }} <span class="text-lg font-bold text-orange-500">Siswa</span>
                            <span class="ml-2 px-2.5 py-1 bg-orange-200 text-orange-800 text-sm font-bold rounded-xl shadow-sm">{{ getPercentage(stats?.nyicil || 0) }}</span>
                        </div>
                        <p class="text-xs text-orange-600 mt-2 font-medium">Mempunyai sisa tagihan yang harus dibayar.</p>
                    </div>

                    <!-- Belum Bayar -->
                    <div class="bg-gradient-to-br from-red-50 to-rose-100 overflow-hidden shadow-sm rounded-2xl border border-red-200 p-6 relative group hover:-translate-y-1 transition-all">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="text-sm font-bold text-red-700 uppercase tracking-wider mb-2">Belum Bayar</div>
                        <div class="text-4xl font-black text-red-600 flex items-center gap-2">
                            {{ stats?.belum_bayar || 0 }} <span class="text-lg font-bold text-red-500">Siswa</span>
                            <span class="ml-2 px-2.5 py-1 bg-red-200 text-red-800 text-sm font-bold rounded-xl shadow-sm">{{ getPercentage(stats?.belum_bayar || 0) }}</span>
                        </div>
                        <p class="text-xs text-red-600 mt-2 font-medium">Belum ada pembayaran masuk sama sekali.</p>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <!-- Filters -->
                    <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-center gap-4 bg-gray-50/50">
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                            <span class="text-sm font-bold text-gray-600">Filter:</span>
                            <select v-model="searchForm.classroom_id" @change="onSearch" class="border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm w-full sm:w-48">
                                <option value="">Semua Kelas</option>
                                <option v-for="classroom in classrooms" :key="classroom.id" :value="classroom.id">
                                    {{ classroom.level }} - {{ classroom.name }}
                                </option>
                            </select>
                            
                            <select v-model="searchForm.status" @change="onSearch" class="border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm w-full sm:w-48">
                                <option value="">Semua Status</option>
                                <option value="lunas">Sudah Lunas</option>
                                <option value="belum_lunas">Belum Lunas (Mencicil)</option>
                                <option value="belum_bayar">Belum Bayar</option>
                            </select>

                            <select v-model="searchForm.per_page" @change="onSearch" class="border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                <option value="10">10 Baris</option>
                                <option value="25">25 Baris</option>
                                <option value="50">50 Baris</option>
                                <option value="all">Semua</option>
                            </select>
                        </div>
                        
                        <div class="relative w-full lg:w-80 flex gap-2">
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <TextInput 
                                    v-model="searchForm.search" 
                                    @keyup.enter="onSearch"
                                    type="text" 
                                    class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm" 
                                    placeholder="Cari nama atau NISN..." 
                                />
                            </div>
                            <a 
                                :href="route('billings.monitoring.print', { classroom_id: searchForm.classroom_id, status: searchForm.status, search: searchForm.search })" 
                                target="_blank"
                                class="inline-flex items-center justify-center px-4 py-2 bg-[#4361ee] border border-transparent rounded-xl font-bold text-xs text-white uppercase hover:bg-indigo-700 transition ease-in-out shadow-sm whitespace-nowrap"
                                title="Cetak Massal Slip PDF"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Cetak
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold text-center w-12">No</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Kelas</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-right">Total Tagihan</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-right">Telah Dibayar</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-right">Sisa Tagihan</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Status</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(student, index) in students.data" :key="student.id">
                                    <tr class="bg-white border-b hover:bg-indigo-50/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ (students.current_page - 1) * students.per_page + index + 1 }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ student.name }}</div>
                                            <div class="text-xs text-gray-500">{{ student.nisn }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                                                {{ student.classroom?.name || '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium text-gray-900">
                                            {{ formatRupiah(student.total_bill) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium text-blue-600">
                                            {{ formatRupiah(student.total_paid) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-red-600">
                                            {{ formatRupiah(student.remaining) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span v-if="student.payment_status === 'lunas'" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">
                                                Lunas
                                            </span>
                                            <span v-else-if="student.payment_status === 'nyicil'" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold border border-orange-200">
                                                Mencicil
                                            </span>
                                            <span v-else class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold border border-red-200">
                                                Belum Bayar
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                <!-- Lihat Rincian Button -->
                                                <button 
                                                    @click="toggleExpand(student.id)" 
                                                    class="inline-flex items-center justify-center gap-1 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition-colors text-xs font-bold shadow-sm"
                                                    title="Lihat Rincian Tagihan & Transaksi"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    {{ expandedStudentId === student.id ? 'Tutup' : 'Rincian' }}
                                                </button>

                                                <!-- Cetak Invoice Terbaru Button -->
                                                <a 
                                                    v-if="student.payments && student.payments.length > 0"
                                                    :href="route('payments.invoice.pdf', student.payments[0].id)" 
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center gap-1 text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition-colors text-xs font-bold shadow-sm"
                                                    title="Cetak Kuitansi Terbaru"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak
                                                </a>
                                                <span 
                                                    v-else 
                                                    class="inline-flex items-center justify-center gap-1 text-gray-400 bg-gray-50 border border-gray-100 px-2.5 py-1.5 rounded-lg text-xs font-bold cursor-not-allowed opacity-60"
                                                    title="Belum ada pembayaran"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak
                                                </span>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Expanded Details -->
                                    <tr v-if="expandedStudentId === student.id" class="bg-indigo-50/10 border-b">
                                        <td colspan="8" class="px-6 py-6">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-white p-6 rounded-2xl border border-indigo-100 shadow-sm">
                                                <!-- Rincian Tagihan -->
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-1.5">
                                                        <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                                                        Rincian Tagihan Siswa
                                                    </h4>
                                                    <div class="border border-gray-100 rounded-xl overflow-hidden bg-white shadow-sm">
                                                        <table class="w-full text-xs text-left">
                                                            <thead class="bg-gray-50 text-gray-600 uppercase font-semibold border-b border-gray-100">
                                                                <tr>
                                                                    <th class="px-4 py-3">Nama Tagihan</th>
                                                                    <th class="px-4 py-3 text-right">Nominal</th>
                                                                    <th class="px-4 py-3 text-right">Terbayar</th>
                                                                    <th class="px-4 py-3 text-right">Sisa</th>
                                                                    <th class="px-4 py-3 text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100">
                                                                <tr v-for="bill in student.billings" :key="bill.id" class="hover:bg-gray-50/50">
                                                                    <td class="px-4 py-3 font-medium text-gray-800">
                                                                        {{ bill.category?.name || 'Tagihan Tanpa Kategori' }}
                                                                        <div class="text-[10px] text-gray-500 font-normal">T.A {{ bill.academic_year?.name }}</div>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right text-gray-700 font-medium">
                                                                        {{ formatRupiah(bill.amount) }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right text-green-600 font-medium">
                                                                        {{ formatRupiah(bill.paid_amount) }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right text-red-600 font-bold">
                                                                        {{ formatRupiah(bill.remaining_amount) }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <span v-if="bill.is_paid" class="px-2 py-0.5 rounded bg-green-100 text-green-700 font-bold text-[10px] border border-green-200">
                                                                            Lunas
                                                                        </span>
                                                                        <span v-else-if="bill.paid_amount > 0" class="px-2 py-0.5 rounded bg-orange-100 text-orange-700 font-bold text-[10px] border border-orange-200">
                                                                            Mencicil
                                                                        </span>
                                                                        <span v-else class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-bold text-[10px] border border-red-200">
                                                                            Belum Bayar
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <tr v-if="!student.billings || student.billings.length === 0">
                                                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500 bg-gray-50/30">
                                                                        Tidak ada rincian tagihan untuk siswa ini.
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- Riwayat Pembayaran -->
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-1.5">
                                                        <span class="w-1.5 h-4 bg-emerald-600 rounded-full"></span>
                                                        Riwayat Pembayaran (Kuitansi)
                                                    </h4>
                                                    <div class="border border-gray-100 rounded-xl overflow-hidden bg-white shadow-sm">
                                                        <table class="w-full text-xs text-left">
                                                            <thead class="bg-gray-50 text-gray-600 uppercase font-semibold border-b border-gray-100">
                                                                <tr>
                                                                    <th class="px-4 py-3">No. Invoice / Tanggal</th>
                                                                    <th class="px-4 py-3 text-right">Total Bayar</th>
                                                                    <th class="px-4 py-3">Operator</th>
                                                                    <th class="px-4 py-3 text-center">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100">
                                                                <tr v-for="payment in student.payments" :key="payment.id" class="hover:bg-gray-50/50">
                                                                    <td class="px-4 py-3">
                                                                        <div class="font-bold text-gray-800">{{ payment.invoice_number }}</div>
                                                                        <div class="text-[10px] text-gray-500">{{ formatDate(payment.date) }}</div>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-right text-emerald-600 font-bold">
                                                                        {{ formatRupiah(payment.total_amount) }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-gray-600 font-medium">
                                                                        {{ payment.user?.name || 'Admin' }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-center">
                                                                        <a 
                                                                            :href="route('payments.invoice.pdf', payment.id)" 
                                                                            target="_blank"
                                                                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition-colors text-[11px] font-bold shadow-sm"
                                                                            title="Cetak Kuitansi PDF"
                                                                        >
                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                                            </svg>
                                                                            Cetak
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <tr v-if="!student.payments || student.payments.length === 0">
                                                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500 bg-gray-50/30">
                                                                        Siswa ini belum memiliki riwayat pembayaran.
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-if="students.data.length === 0">
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="font-medium">Tidak ada data tagihan siswa yang ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-6 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/30" v-if="students.links && students.data.length > 0">
                        <span class="text-sm text-gray-500">Menampilkan <span class="font-bold text-gray-900">{{ students.from }}</span> sampai <span class="font-bold text-gray-900">{{ students.to }}</span> dari <span class="font-bold text-gray-900">{{ students.total }}</span> siswa</span>
                        <div class="flex flex-wrap space-x-1">
                            <template v-for="(link, p) in students.links" :key="p">
                                <a 
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1.5 border rounded-lg text-sm font-medium transition-colors"
                                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'"
                                    v-html="link.label"
                                ></a>
                                <span v-else class="px-3 py-1.5 border rounded-lg text-sm text-gray-400 bg-gray-50 border-gray-200" v-html="link.label"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
