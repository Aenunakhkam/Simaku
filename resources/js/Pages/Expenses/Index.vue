<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    expenses: Object,
    filters: Object,
});

const search = ref(props.filters.search);
const dateStart = ref(props.filters.date_start);
const dateEnd = ref(props.filters.date_end);
const perPage = ref(props.filters.per_page || '10');

watch([search, dateStart, dateEnd, perPage], debounce(function ([value, start, end, per]) {
    router.get(
        route('expenses.index'),
        { search: value, date_start: start, date_end: end, per_page: per },
        { preserveState: true, replace: true }
    );
}, 300));

const form = useForm({});

const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus transaksi pengeluaran ini?')) {
        form.delete(route('expenses.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Transaksi Pengeluaran" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaksi Pengeluaran</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Transaksi Pengeluaran</h2>
                    <Link :href="route('expenses.create')" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#1a237e] to-[#0d47a1] px-5 py-2.5 text-sm font-bold text-white transition-all hover:from-[#152847] hover:to-[#1a237e] hover:shadow-lg shadow-md focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:ring-offset-2 active:scale-[0.98] whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Catat Pengeluaran
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <div class="p-6 md:px-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                        <!-- Filters -->
                        <div class="flex items-center space-x-3 w-full md:w-auto">
                            <span class="text-sm font-medium text-gray-500">Tampilkan</span>
                            <select v-model="perPage" class="border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="all">Semua</option>
                            </select>
                            <span class="text-sm font-medium text-gray-500">data</span>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                            <div class="relative w-full sm:w-64">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input 
                                    v-model="search" 
                                    type="text" 
                                    placeholder="Cari No. Voucher atau Keterangan..." 
                                    class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors text-sm"
                                >
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <span class="text-sm text-gray-500">Dari:</span>
                                <input v-model="dateStart" type="date" class="border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white">
                                <span class="text-sm text-gray-500">S/d:</span>
                                <input v-model="dateEnd" type="date" class="border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white">
                            </div>
                            <button @click="dateStart=''; dateEnd=''" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors" title="Reset Tanggal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold text-center w-12">No</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Tanggal</th>
                                    <th scope="col" class="px-6 py-4 font-bold">No. Voucher</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Kategori</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Keterangan</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-right">Nominal</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(expense, index) in expenses.data" :key="expense.id" class="bg-white border-b last:border-0 hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                            {{ (expenses.current_page - 1) * expenses.per_page + index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(expense.date).toLocaleDateString('id-ID') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ expense.voucher_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                                {{ expense.category?.name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs">
                                            {{ expense.note || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                            {{ formatCurrency(expense.amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center gap-2">
                                                <Link :href="route('expenses.show', expense.id)" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded" title="Lihat/Cetak">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </Link>
                                                <button @click="destroy(expense.id)" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="expenses.data.length === 0">
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada data pengeluaran yang ditemukan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    <!-- Pagination -->
                    <div class="p-6 md:px-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4" v-if="expenses.links && expenses.data.length > 0">
                        <span class="text-sm text-gray-500">Menampilkan <span class="font-bold text-gray-900">{{ expenses.from }}</span> sampai <span class="font-bold text-gray-900">{{ expenses.to }}</span> dari <span class="font-bold text-gray-900">{{ expenses.total }}</span> data</span>
                        <div class="flex space-x-1">
                            <template v-for="(link, p) in expenses.links" :key="p">
                                <a 
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1.5 border rounded-md text-sm transition-colors"
                                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'"
                                    v-html="link.label"
                                ></a>
                                <span v-else class="px-3 py-1.5 border rounded-md text-sm text-gray-400 bg-gray-50 border-gray-200" v-html="link.label"></span>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
