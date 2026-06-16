<script setup>
import { ref } from 'vue';
import { useForm, Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    billings: Object,
    classrooms: Array,
    students: Array,
    categories: Array,
    academicYears: Array,
    filters: Object,
});

const page = usePage();
const isCreateModalOpen = ref(false);

const form = useForm({
    target_type: 'classroom',
    target_id: '',
    payment_category_ids: [],
    academic_year_id: page.props.active_academic_year ? page.props.active_academic_year.id : '',
    month: '',
});

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
};

const getMonthName = (month) => {
    if (!month) return '-';
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return months[month - 1];
};


const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const closeModals = () => {
    isCreateModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const storeBilling = () => {
    form.post(route('billings.store'), {
        onSuccess: () => closeModals(),
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
            useForm({}).delete(route('billings.destroy', id));
        }
    });
};

const searchForm = useForm({ 
    search: props.filters?.search || '',
    per_page: props.filters?.per_page || '10'
});

const onSearch = () => {
    searchForm.get(route('billings.index'), { preserveState: true });
};
</script>

<template>
    <Head title="Atur Tagihan Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Atur Tagihan Siswa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Data Table Container -->
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <div class="p-6 md:px-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center space-x-3 w-full md:w-auto">
                            <span class="text-sm font-medium text-gray-500">Tampilkan</span>
                            <select v-model="searchForm.per_page" @change="onSearch" class="border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-sm font-medium text-gray-500">data</span>
                        </div>
                        
                        <div class="flex items-center space-x-3 w-full md:w-auto">
                            <div class="relative w-full md:w-64">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <TextInput 
                                    v-model="searchForm.search" 
                                    @keyup.enter="onSearch"
                                    type="text" 
                                    class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors text-sm" 
                                    placeholder="Cari nama / NISN..." 
                                />
                            </div>
                            <PrimaryButton @click="openCreateModal" class="shadow-md hover:shadow-lg transition-all rounded-xl whitespace-nowrap">
                                + Generate Tagihan
                            </PrimaryButton>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Nama & Kelas</th>
                                    <th class="px-6 py-4 font-bold">Jenis Tagihan</th>
                                    <th class="px-6 py-4 font-bold text-right">Nominal</th>
                                    <th class="px-6 py-4 font-bold text-center">Status</th>
                                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="billing in billings.data" :key="billing.id" class="bg-white border-b last:border-0 hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ billing.student?.name }}</div>
                                        <div class="text-xs text-gray-500">Kelas: {{ billing.student?.classroom?.level }} {{ billing.student?.classroom?.name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-indigo-700">{{ billing.category?.name }}</div>
                                        <div class="text-xs text-gray-500">{{ billing.academic_year?.name }} - {{ getMonthName(billing.month) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900">
                                        {{ formatRupiah(billing.amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="billing.is_paid" class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">Lunas</span>
                                        <span v-else class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">Belum Lunas</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="deleteBilling(billing.id)" class="inline-flex items-center justify-center text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus Tagihan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="billings.data.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        Belum ada data tagihan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-6 md:px-8 border-t border-gray-100 flex justify-between items-center" v-if="billings.links && billings.data.length > 0">
                        <span class="text-sm text-gray-500">Hal {{ billings.current_page }} dari {{ billings.last_page }}</span>
                        <div class="flex space-x-1">
                            <template v-for="(link, p) in billings.links" :key="p">
                                <a v-if="link.url" :href="link.url" class="px-3 py-1.5 border rounded-md text-sm transition-colors" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'" v-html="link.label"></a>
                                <span v-else class="px-3 py-1.5 border rounded-md text-sm text-gray-400 bg-gray-50" v-html="link.label"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="isCreateModalOpen" @close="closeModals" maxWidth="lg">
            <div class="bg-gradient-to-b from-indigo-50/50 to-white px-6 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-[#1a237e] flex items-center">
                    Generate Tagihan Baru
                </h2>
            </div>
            <div class="p-6 sm:p-8">
                <form @submit.prevent="storeBilling">
                    
                    <div class="mb-4">
                        <InputLabel value="Terapkan Tagihan Untuk" />
                        <div class="flex space-x-4 mt-2">
                            <label class="flex items-center text-sm">
                                <input type="radio" v-model="form.target_type" value="classroom" class="text-indigo-600 focus:ring-indigo-500 rounded-full" />
                                <span class="ml-2 font-medium text-gray-700">Satu Kelas Penuh</span>
                            </label>
                            <label class="flex items-center text-sm">
                                <input type="radio" v-model="form.target_type" value="student" class="text-indigo-600 focus:ring-indigo-500 rounded-full" />
                                <span class="ml-2 font-medium text-gray-700">Siswa Tertentu</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4" v-if="form.target_type === 'classroom'">
                        <InputLabel value="Pilih Kelas" />
                        <select v-model="form.target_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Kelas --</option>
                            <option v-for="classroom in classrooms" :key="classroom.id" :value="classroom.id">
                                {{ classroom.level }} {{ classroom.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.target_id" class="mt-2" />
                    </div>

                    <div class="mb-4" v-if="form.target_type === 'student'">
                        <InputLabel value="Pilih Siswa" />
                        <select v-model="form.target_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Siswa --</option>
                            <option v-for="student in students" :key="student.id" :value="student.id">
                                {{ student.nisn }} - {{ student.name }} (Kelas {{ student.classroom?.level }} {{ student.classroom?.name }})
                            </option>
                        </select>
                        <InputError :message="form.errors.target_id" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <InputLabel value="Kategori Tagihan (Bisa Pilih Banyak)" />
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 border border-gray-200 rounded-md bg-gray-50">
                            <label v-for="cat in categories" :key="cat.id" class="flex items-start space-x-3 bg-white p-2 border border-gray-100 rounded shadow-sm hover:border-indigo-300 transition-colors cursor-pointer">
                                <input type="checkbox" v-model="form.payment_category_ids" :value="cat.id" class="text-indigo-600 focus:ring-indigo-500 rounded mt-0.5" />
                                <div class="flex flex-col">
                                    <span class="font-medium text-sm text-gray-800">{{ cat.name }}</span>
                                    <span class="text-[10px] text-gray-500 font-bold bg-gray-100 px-1.5 py-0.5 rounded w-max mt-0.5">{{ cat.type === 'monthly' ? 'Bulanan' : 'Sekali Bayar' }} - {{ formatRupiah(cat.default_amount) }}</span>
                                </div>
                            </label>
                            <div v-if="categories.length === 0" class="col-span-full text-center text-sm text-gray-500 py-4">
                                Belum ada kategori tagihan.
                            </div>
                        </div>
                        <InputError :message="form.errors.payment_category_ids" class="mt-2" />
                    </div>

                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel value="Tahun Ajaran" />
                            <select v-model="form.academic_year_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Tahun Ajaran --</option>
                                <option v-for="ay in academicYears" :key="ay.id" :value="ay.id">{{ ay.name }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Bulan (Opsional jika bukan bulanan)" />
                            <select v-model="form.month" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Bulan --</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>



                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <SecondaryButton @click="closeModals">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Proses Tagihan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
