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

watch([search, dateStart, dateEnd], debounce(function ([value, start, end]) {
    router.get(
        route('expenses.index'),
        { search: value, date_start: start, date_end: end },
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

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Transaksi Pengeluaran</h2>
                    <Link :href="route('expenses.create')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Catat Pengeluaran
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filters -->
                        <div class="flex flex-col sm:flex-row gap-4 mb-6">
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Cari No. Voucher atau Keterangan..." 
                                class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full sm:w-1/3"
                            >
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <span class="text-sm text-gray-500">Dari:</span>
                                <input v-model="dateStart" type="date" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <span class="text-sm text-gray-500">Sampai:</span>
                                <input v-model="dateEnd" type="date" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            </div>
                            <button @click="dateStart=''; dateEnd=''" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm font-medium">Reset Filter</button>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Voucher</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="expense in expenses.data" :key="expense.id" class="hover:bg-gray-50">
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
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada data pengeluaran yang ditemukan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4" v-if="expenses.links.length > 3">
                            <div class="flex flex-wrap -mb-1">
                                <template v-for="(link, p) in expenses.links" :key="p">
                                    <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                                    <Link v-else :class="{ 'bg-blue-600 text-white': link.active, 'bg-white hover:bg-gray-50': !link.active }" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded" :href="link.url" v-html="link.label" />
                                </template>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
