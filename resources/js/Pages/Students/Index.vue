<script setup>
import { ref } from 'vue';
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
const editingStudent = ref(null);

const form = useForm({
    nisn: '',
    nis: '',
    name: '',
    classroom_id: '',
    status: 'active',
});

const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const openEditModal = (student) => {
    editingStudent.value = student;
    form.nisn = student.nisn;
    form.nis = student.nis;
    form.name = student.name;
    form.classroom_id = student.classroom_id;
    form.status = student.status;
    isEditModalOpen.value = true;
};

const closeModals = () => {
    isCreateModalOpen.value = false;
    isEditModalOpen.value = false;
    form.reset();
    form.clearErrors();
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

const deleteStudent = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) {
        useForm({}).delete(route('students.destroy', id));
    }
};

const searchForm = useForm({ search: props.filters?.search || '' });
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                    
                    <div class="flex justify-between items-center mb-6">
                        <div class="w-1/3 relative">
                            <TextInput 
                                v-model="searchForm.search" 
                                @keyup.enter="onSearch"
                                type="text" 
                                class="w-full pl-4 pr-10 py-2 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                                placeholder="Cari nama, NISN, atau NIS..." 
                            />
                        </div>
                        <PrimaryButton @click="openCreateModal" class="shadow-sm hover:shadow transition-shadow">
                            + Tambah Siswa
                        </PrimaryButton>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">NISN / NIS</th>
                                    <th class="px-6 py-4 font-semibold">Nama Siswa</th>
                                    <th class="px-6 py-4 font-semibold">Kelas & Jurusan</th>
                                    <th class="px-6 py-4 font-semibold">Status</th>
                                    <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="student in students.data" :key="student.id" class="bg-white border-b last:border-0 hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ student.nisn }}</div>
                                        <div class="text-xs text-gray-500">{{ student.nis || '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-medium">{{ student.name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                                            {{ student.classroom?.name }} ({{ student.classroom?.major?.code }})
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span v-if="student.status === 'active'" class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">Aktif</span>
                                        <span v-else-if="student.status === 'graduated'" class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">Lulus</span>
                                        <span v-else class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">Keluar</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3">
                                        <button @click="openEditModal(student)" class="text-indigo-600 hover:text-indigo-900 font-medium transition-colors">Edit</button>
                                        <button @click="deleteStudent(student.id)" class="text-red-600 hover:text-red-900 font-medium transition-colors">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="students.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50">Belum ada data siswa.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex justify-between items-center" v-if="students.links && students.data.length > 0">
                        <span class="text-sm text-gray-500">Menampilkan {{ students.from }} sampai {{ students.to }} dari {{ students.total }} data</span>
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
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-5">
                    {{ isEditModalOpen ? 'Edit Siswa' : 'Tambah Siswa Baru' }}
                </h2>

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
                        <InputLabel for="classroom_id" value="Kelas" />
                        <select 
                            id="classroom_id" 
                            v-model="form.classroom_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        >
                            <option value="" disabled>Pilih Kelas...</option>
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
    </AuthenticatedLayout>
</template>
