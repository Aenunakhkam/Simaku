<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    student: Object,
    billings: Array,
    payments: Array,
});

const form = useForm({
    billings: props.billings.map(b => ({
        id: b.id,
        pay_amount: 0,
    }))
});

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
};

const getMonthName = (month) => {
    if (!month) return '-';
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return months[month - 1];
};

const setLunas = (index, remaining) => {
    form.billings[index].pay_amount = remaining;
};

const clearAmount = (index) => {
    form.billings[index].pay_amount = 0;
};

const totalPayAmount = computed(() => {
    return form.billings.reduce((sum, item) => sum + Number(item.pay_amount || 0), 0);
});

const unpaidBillings = computed(() => props.billings.filter(b => !b.is_paid));
const paidBillings = computed(() => props.billings.filter(b => b.is_paid));

const processPayment = () => {
    if (totalPayAmount.value <= 0) {
        Swal.fire('Error', 'Nominal bayar tidak boleh nol!', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Proses Pembayaran?',
        text: `Total yang akan dibayar: ${formatRupiah(totalPayAmount.value)}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Bayar Sekarang!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route('payments.store', props.student.id), {
                preserveScroll: true,
                onSuccess: () => {
                    form.billings.forEach(item => item.pay_amount = 0);
                }
            });
        }
    });
};

const deleteBilling = (id) => {
    Swal.fire({
        title: 'Hapus Tagihan?',
        text: "Anda tidak dapat menghapus tagihan jika sudah ada cicilan/pembayaran yang masuk.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            useForm({}).delete(route('billings.destroy', id), {
                preserveScroll: true
            });
        }
    });
};

const deletePayment = (id) => {
    Swal.fire({
        title: 'Batalkan Transaksi?',
        text: "Membatalkan transaksi akan mengembalikan sisa tagihan seperti semula. Data transaksi akan dihapus permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Batalkan Transaksi!',
        cancelButtonText: 'Tutup'
    }).then((result) => {
        if (result.isConfirmed) {
            useForm({}).delete(route('payments.destroy', id), {
                preserveScroll: true
            });
        }
    });
};
</script>

<template>
    <Head :title="`Proses Kasir - ${student.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span class="text-gray-400 font-normal">Kasir /</span> Pembayaran Tagihan
                </h2>
                <Link :href="route('payments.index')" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Identitas Siswa -->
                <div class="bg-gradient-to-r from-[#1a237e] to-[#0d47a1] rounded-2xl p-6 md:p-8 shadow-lg flex flex-col md:flex-row justify-between items-center md:items-start text-white gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-inner shrink-0">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-extrabold">{{ student.name }}</h3>
                            <p class="text-blue-200 mt-1">
                                NISN: {{ student.nisn }} | Kelas: 
                                <template v-if="student.classroom">
                                    {{ student.classroom.level }} {{ student.classroom.name }} ({{ student.classroom.major?.name || 'Umum' }})
                                </template>
                                <template v-else>
                                    Tanpa Kelas
                                </template>
                            </p>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 min-w-[200px] text-center border border-white/20">
                        <p class="text-blue-200 text-sm font-medium">Total Akan Dibayar</p>
                        <p class="text-3xl font-black mt-1 text-yellow-400">{{ formatRupiah(totalPayAmount) }}</p>
                    </div>
                </div>

                <!-- Form Tagihan Belum Lunas -->
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <div class="p-6 md:px-8 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tagihan Aktif (Belum Lunas)
                        </h3>
                    </div>

                    <form @submit.prevent="processPayment">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 font-bold">Jenis Tagihan</th>
                                        <th class="px-6 py-4 font-bold text-right">Total Tagihan</th>
                                        <th class="px-6 py-4 font-bold text-right">Sudah Dibayar</th>
                                        <th class="px-6 py-4 font-bold text-right">Sisa Tagihan</th>
                                        <th class="px-6 py-4 font-bold w-64">Input Bayar (Rp)</th>
                                        <th class="px-6 py-4 font-bold text-center">Aksi Cepat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(billing, index) in billings" :key="billing.id" v-show="!billing.is_paid" class="bg-white border-b last:border-0 hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ billing.category?.name }}</div>
                                            <div class="text-xs text-gray-500">{{ billing.academic_year?.name }} {{ billing.month ? ' - ' + getMonthName(billing.month) : '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">{{ formatRupiah(billing.amount) }}</td>
                                        <td class="px-6 py-4 text-right font-medium text-green-600">{{ formatRupiah(billing.paid_amount) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-red-600">{{ formatRupiah(billing.remaining_amount) }}</td>
                                        <td class="px-6 py-4">
                                            <TextInput 
                                                v-model="form.billings[index].pay_amount" 
                                                type="number" 
                                                min="0"
                                                :max="billing.remaining_amount"
                                                class="w-full text-right font-bold focus:ring-green-500 focus:border-green-500" 
                                                :class="{'border-green-300 bg-green-50': form.billings[index].pay_amount > 0}"
                                            />
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-2 flex items-center justify-center">
                                            <button type="button" @click="setLunas(index, billing.remaining_amount)" class="px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-bold transition-colors">Bayar Lunas</button>
                                            <button type="button" @click="clearAmount(index)" v-if="form.billings[index].pay_amount > 0" class="px-3 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg text-xs font-bold transition-colors">Batal</button>
                                            <button type="button" @click="deleteBilling(billing.id)" class="inline-flex items-center justify-center text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition-colors ml-1" title="Hapus Tagihan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="unpaidBillings.length === 0">
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                            <svg class="w-12 h-12 text-green-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Hebat! Siswa ini tidak memiliki tagihan aktif.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="p-6 bg-gray-50/80 border-t border-gray-100 flex justify-end" v-if="unpaidBillings.length > 0">
                            <PrimaryButton type="submit" :disabled="totalPayAmount <= 0 || form.processing" class="py-3 px-8 text-base shadow-md hover:shadow-lg rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 border-0">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Proses Pembayaran ({{ formatRupiah(totalPayAmount) }})
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Riwayat Tagihan Lunas -->
                <div v-if="paidBillings.length > 0" class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100 opacity-75 hover:opacity-100 transition-opacity">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-base font-bold text-gray-700 flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tagihan Sudah Lunas
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3 font-bold">Jenis Tagihan</th>
                                    <th class="px-6 py-3 font-bold text-right">Total Tagihan</th>
                                    <th class="px-6 py-3 font-bold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="billing in paidBillings" :key="billing.id" class="border-b last:border-0">
                                    <td class="px-6 py-3">
                                        <div class="font-bold text-gray-700">{{ billing.category?.name }}</div>
                                        <div class="text-xs">{{ billing.academic_year?.name }} {{ billing.month ? ' - ' + getMonthName(billing.month) : '' }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-right font-medium">{{ formatRupiah(billing.amount) }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-green-50 text-green-700 border border-green-200">Lunas</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Transaksi (Invoice) -->
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-base font-bold text-gray-700 flex items-center">
                            <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Riwayat Transaksi (Kuitansi)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3 font-bold">No. Invoice & Tanggal</th>
                                    <th class="px-6 py-3 font-bold">Rincian Pembayaran</th>
                                    <th class="px-6 py-3 font-bold text-right">Total Transaksi</th>
                                    <th class="px-6 py-3 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment in payments" :key="payment.id" class="border-b last:border-0 hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        <div class="font-bold text-indigo-700">{{ payment.invoice_number }}</div>
                                        <div class="text-xs">{{ payment.date }}</div>
                                        <div class="text-xs text-gray-400 mt-1">Kasir: {{ payment.user?.name || '-' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <ul class="list-disc pl-4 text-xs space-y-1">
                                            <li v-for="detail in payment.payment_details" :key="detail.id">
                                                {{ detail.billing?.category?.name }} - {{ formatRupiah(detail.amount) }}
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-900">{{ formatRupiah(payment.total_amount) }}</td>
                                    <td class="px-6 py-3 text-center flex justify-center space-x-2">
                                        <a :href="route('payments.invoice.pdf', payment.id)" target="_blank" class="inline-flex items-center justify-center text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Cetak Kuitansi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                        <button @click="deletePayment(payment.id)" class="inline-flex items-center justify-center text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Batalkan Transaksi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="payments?.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50">Belum ada riwayat transaksi.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
