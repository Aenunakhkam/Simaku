<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    expense: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const printVoucher = () => {
    window.print();
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Voucher ${expense.voucher_number}`" />

        <div class="py-12 print:py-0 print:bg-white">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 print:max-w-full print:px-0">
                
                <div class="flex justify-between items-center mb-6 print:hidden">
                    <h2 class="text-2xl font-bold text-gray-800">Detail Pengeluaran</h2>
                    <div class="flex gap-2">
                        <button @click="printVoucher" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 shadow flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak Bukti
                        </button>
                        <Link :href="route('expenses.index')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 shadow-sm flex items-center gap-2">
                            Kembali
                        </Link>
                    </div>
                </div>

                <!-- Voucher Area -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg print:shadow-none print:rounded-none">
                    <div class="p-8 bg-white border-b border-gray-200">
                        
                        <!-- Header -->
                        <div class="flex justify-between items-start border-b-2 border-gray-800 pb-6 mb-6">
                            <div>
                                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">SIMAKU</h1>
                                <p class="text-sm text-gray-500 font-medium mt-1">Sistem Manajemen Keuangan Sekolah</p>
                            </div>
                            <div class="text-right">
                                <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wider">Bukti Kas Keluar</h2>
                                <p class="text-sm text-gray-600 mt-1 font-mono">{{ expense.voucher_number }}</p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="grid grid-cols-2 gap-8 mb-8">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Informasi Transaksi</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tanggal</span>
                                        <span class="font-medium">{{ new Date(expense.date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Kategori</span>
                                        <span class="font-medium text-blue-600">{{ expense.category?.name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Dicatat Oleh</span>
                                        <span class="font-medium">{{ expense.user?.name }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex flex-col justify-center items-center">
                                <span class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Pengeluaran</span>
                                <span class="text-3xl font-extrabold text-gray-900">{{ formatCurrency(expense.amount) }}</span>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Keterangan / Rincian</h3>
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 min-h-[100px]">
                                <p class="text-gray-800 whitespace-pre-wrap">{{ expense.note || '-' }}</p>
                            </div>
                        </div>

                        <!-- Signatures -->
                        <div class="grid grid-cols-3 gap-8 mt-12 pt-8 border-t border-gray-200 text-center">
                            <div>
                                <p class="text-sm text-gray-600 mb-16">Disetujui Oleh,</p>
                                <div class="border-b border-gray-400 w-3/4 mx-auto mb-1"></div>
                                <p class="text-xs text-gray-500">Kepala Sekolah</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-16">Dibayarkan Oleh,</p>
                                <div class="border-b border-gray-400 w-3/4 mx-auto mb-1"></div>
                                <p class="text-xs text-gray-500">Bendahara</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-16">Diterima Oleh,</p>
                                <div class="border-b border-gray-400 w-3/4 mx-auto mb-1"></div>
                                <p class="text-xs text-gray-500">Penerima Dana</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Proof Attachment (Not printed) -->
                <div v-if="expense.proof_file_path" class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg print:hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            Lampiran Bukti Transaksi
                        </h3>
                        <div class="border border-gray-200 rounded-lg p-2 bg-gray-50 max-w-2xl">
                            <img v-if="expense.proof_file_path.match(/\.(jpg|jpeg|png)$/i)" :src="`/storage/${expense.proof_file_path}`" alt="Bukti Transaksi" class="w-full rounded shadow-sm object-contain max-h-[600px]">
                            <iframe v-else-if="expense.proof_file_path.match(/\.pdf$/i)" :src="`/storage/${expense.proof_file_path}`" class="w-full h-[600px] rounded shadow-sm"></iframe>
                            <div v-else class="p-4 text-center">
                                <a :href="`/storage/${expense.proof_file_path}`" target="_blank" class="text-blue-600 hover:underline font-medium">Unduh File Lampiran</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print\:bg-white, .print\:bg-white * {
        visibility: visible;
    }
    .print\:bg-white {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    @page { margin: 0; }
    body { margin: 1.6cm; }
}
</style>
