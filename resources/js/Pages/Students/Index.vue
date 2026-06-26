<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    students: Object,
    classrooms: Array,
    filters: Object,
});

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isImportModalOpen = ref(false);
const isBulkUpdateModalOpen = ref(false);
const editingStudent = ref(null);
const selectedStudents = ref([]);

const form = useForm({
    nisn: '',
    nis: '',
    name: '',
    classroom_id: '',
    status: 'active',
});

const importForm = useForm({
    file: null,
});

const bulkUpdateForm = useForm({
    student_ids: [],
    classroom_id: '',
});

const isAllSelected = computed({
    get: () => {
        return props.students.data && props.students.data.length > 0 && selectedStudents.value.length === props.students.data.length;
    },
    set: (val) => {
        if (val && props.students.data) {
            selectedStudents.value = props.students.data.map(s => s.id);
        } else {
            selectedStudents.value = [];
        }
    }
});

const openBulkUpdateModal = () => {
    bulkUpdateForm.classroom_id = '';
    isBulkUpdateModalOpen.value = true;
};

const submitBulkUpdate = () => {
    bulkUpdateForm.student_ids = selectedStudents.value;
    bulkUpdateForm.post(route('students.bulk-update-class'), {
        onSuccess: () => {
            closeModals();
            selectedStudents.value = [];
        }
    });
};

const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const openEditModal = (student) => {
    editingStudent.value = student;
    form.nisn = student.nisn;
    form.nis = student.nis;
    form.name = student.name;
    form.classroom_id = student.classroom_id || '';
    form.status = student.status;
    isEditModalOpen.value = true;
};

const closeModals = () => {
    isCreateModalOpen.value = false;
    isEditModalOpen.value = false;
    isImportModalOpen.value = false;
    isBulkUpdateModalOpen.value = false;
    form.reset();
    form.clearErrors();
    importForm.reset();
    importForm.clearErrors();
    bulkUpdateForm.reset();
    bulkUpdateForm.clearErrors();
};

const storeStudent = () => {
    form.post(route('students.store'), {
        onSuccess: () => closeModals(),
    });
};

const updateStudent = () => {
    form.put(route('students.update', editingStudent.value.id), {
        onSuccess: () => closeModals(),
    });
};

const submitImport = () => {
    importForm.post(route('students.import'), {
        onSuccess: () => closeModals(),
    });
};

const deleteStudent = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) {
        useForm({}).delete(route('students.destroy', id));
    }
};

const searchForm = useForm({ 
    search: props.filters?.search || '',
    per_page: props.filters?.per_page || '10'
});

const onSearch = () => {
    searchForm.get(route('students.index'), { preserveState: true });
};
</script>

<template>
    <Head title="Data Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Siswa</h2>
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
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="all">Semua</option>
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
                                    class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors" 
                                    placeholder="Cari siswa..." 
                                />
                            </div>
                            <PrimaryButton v-if="selectedStudents.length > 0" @click="openBulkUpdateModal" class="shadow-md hover:shadow-lg transition-all rounded-xl whitespace-nowrap bg-indigo-600 mr-1">
                                Naik Kelas Massal ({{ selectedStudents.length }})
                            </PrimaryButton>
                            <SecondaryButton @click="isImportModalOpen = true" class="shadow-md hover:shadow-lg transition-all rounded-xl whitespace-nowrap bg-green-50 text-green-700 border-green-200 hover:bg-green-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import Excel
                            </SecondaryButton>
                            <PrimaryButton @click="openCreateModal" class="shadow-md hover:shadow-lg transition-all rounded-xl whitespace-nowrap">
                                + Tambah Siswa
                            </PrimaryButton>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold text-center w-12">
                                        <input type="checkbox" v-model="isAllSelected" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm cursor-pointer" title="Pilih Semua">
                                    </th>
                                    <th scope="col" class="px-6 py-4 font-bold">NISN / NIS</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Kelas & Jurusan</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Status</th>
                                    <th scope="col" class="px-6 py-4 text-right font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(student, index) in students.data" :key="student.id" class="bg-white border-b last:border-0 hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        <input type="checkbox" :value="student.id" v-model="selectedStudents" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm cursor-pointer">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ student.nisn }}</div>
                                        <div class="text-xs text-gray-500">{{ student.nis || '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-medium">{{ student.name }}</td>
                                    <td class="px-6 py-4">
                                        <span v-if="student.classroom" class="px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                                            {{ student.classroom?.name }} ({{ student.classroom?.major?.code }})
                                        </span>
                                        <span v-else class="px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-400">
                                            Tanpa Kelas
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span v-if="student.status === 'active'" class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
                                        <span v-else-if="student.status === 'graduated'" class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">Lulus</span>
                                        <span v-else class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Keluar</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button @click="openEditModal(student)" class="inline-flex items-center justify-center text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="deleteStudent(student.id)" class="inline-flex items-center justify-center text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="students.data.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50">Tidak ada data siswa yang ditemukan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-6 md:px-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4" v-if="students.links && students.data.length > 0">
                        <span class="text-sm text-gray-500">Menampilkan <span class="font-bold text-gray-900">{{ students.from }}</span> sampai <span class="font-bold text-gray-900">{{ students.to }}</span> dari <span class="font-bold text-gray-900">{{ students.total }}</span> data</span>
                        <div class="flex space-x-1">
                            <template v-for="(link, p) in students.links" :key="p">
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

        <Modal :show="isCreateModalOpen || isEditModalOpen" @close="closeModals">
            <div class="bg-gradient-to-b from-indigo-50/50 to-white px-6 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-[#1a237e] flex items-center">
                    <svg v-if="!isEditModalOpen" class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <svg v-else class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    {{ isEditModalOpen ? 'Perbarui Data Siswa' : 'Tambah Siswa Baru' }}
                </h2>
            </div>
            <div class="p-6 sm:p-8">

                <form @submit.prevent="isEditModalOpen ? updateStudent() : storeStudent()">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <InputLabel for="nisn" value="NISN" />
                            <TextInput id="nisn" v-model="form.nisn" type="text" class="mt-1 block w-full" required autofocus placeholder="0012345678" />
                            <InputError :message="form.errors.nisn" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="nis" value="NIS (Opsional)" />
                            <TextInput id="nis" v-model="form.nis" type="text" class="mt-1 block w-full" placeholder="12345" />
                            <InputError :message="form.errors.nis" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <InputLabel for="name" value="Nama Lengkap Siswa" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required placeholder="Nama Siswa..." />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="classroom_id" value="Kelas (Opsional)" />
                        <select 
                            id="classroom_id" 
                            v-model="form.classroom_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">Pilih Kelas (Opsional)</option>
                            <option v-for="classroom in classrooms" :key="classroom.id" :value="classroom.id">
                                {{ classroom.level }} - {{ classroom.name }} ({{ classroom.major?.code }})
                            </option>
                        </select>
                        <InputError :message="form.errors.classroom_id" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <InputLabel for="status" value="Status Siswa" />
                        <select 
                            id="status" 
                            v-model="form.status" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        >
                            <option value="active">Aktif</option>
                            <option value="graduated">Lulus</option>
                            <option value="dropped_out">Keluar / Pindah</option>
                        </select>
                        <InputError :message="form.errors.status" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <SecondaryButton @click="closeModals">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Simpan Data
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Import Modal -->
        <Modal :show="isImportModalOpen" @close="closeModals">
            <div class="bg-gradient-to-b from-green-50/50 to-white px-6 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-green-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Import Data Siswa (Excel)
                </h2>
            </div>
            <div class="p-6 sm:p-8">
                <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-800">
                    <p class="font-bold mb-1">Panduan Import:</p>
                    <ol class="list-decimal pl-5 space-y-1 mb-3">
                        <li>Unduh template Excel kosong terlebih dahulu.</li>
                        <li>Isi data siswa. (NIS, Nama Kelas, dan Status <strong>Boleh Dikosongkan</strong>).</li>
                        <li>Jika mengisi Kelas, pastikan namanya sesuai (contoh: "10 RPL 1").</li>
                        <li>Unggah kembali file Excel yang sudah diisi ke sini.</li>
                    </ol>
                    <a :href="route('students.template')" class="inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-800 underline">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh Template Excel
                    </a>
                </div>

                <form @submit.prevent="submitImport">
                    <div class="mb-6">
                        <InputLabel for="file" value="Pilih File Excel (.xlsx / .xls)" />
                        <input 
                            id="file" 
                            type="file" 
                            accept=".xlsx, .xls"
                            @input="importForm.file = $event.target.files[0]"
                            class="mt-2 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-xl file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100 transition-all cursor-pointer border border-gray-200 rounded-xl" 
                            required
                        />
                        <InputError :message="importForm.errors.file" class="mt-2" />
                        <progress v-if="importForm.progress" :value="importForm.progress.percentage" max="100" class="w-full mt-2 h-2 rounded overflow-hidden">
                            {{ importForm.progress.percentage }}%
                        </progress>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <SecondaryButton @click="closeModals">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': importForm.processing }" :disabled="importForm.processing || !importForm.file" class="bg-green-600 hover:bg-green-700">
                            {{ importForm.processing ? 'Memproses...' : 'Mulai Import' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Bulk Update Modal -->
        <Modal :show="isBulkUpdateModalOpen" @close="closeModals">
            <div class="bg-gradient-to-b from-indigo-50/50 to-white px-6 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-[#1a237e] flex items-center">
                    <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Pindah/Naik Kelas Massal
                </h2>
            </div>
            <div class="p-6 sm:p-8">
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-100 rounded-xl text-sm text-yellow-800">
                    <p class="font-bold mb-1">Perhatian:</p>
                    <p>Anda akan memindahkan <strong>{{ selectedStudents.length }}</strong> siswa yang dipilih secara bersamaan. Silakan pilih kelas tujuan baru di bawah ini.</p>
                </div>

                <form @submit.prevent="submitBulkUpdate">
                    <div class="mb-6">
                        <InputLabel for="bulk_classroom_id" value="Pilih Kelas Tujuan" />
                        <select 
                            id="bulk_classroom_id" 
                            v-model="bulkUpdateForm.classroom_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        >
                            <option value="" disabled>Pilih Kelas Tujuan...</option>
                            <option v-for="classroom in classrooms" :key="classroom.id" :value="classroom.id">
                                {{ classroom.level }} - {{ classroom.name }} ({{ classroom.major?.code }})
                            </option>
                        </select>
                        <InputError :message="bulkUpdateForm.errors.classroom_id" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <SecondaryButton @click="closeModals">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': bulkUpdateForm.processing }" :disabled="bulkUpdateForm.processing" class="bg-indigo-600 hover:bg-indigo-700">
                            {{ bulkUpdateForm.processing ? 'Memproses...' : 'Pindahkan Siswa' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
