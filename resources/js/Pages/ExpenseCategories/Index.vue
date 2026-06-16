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
    categories: Object,
    filters: Object,
});

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const editingCategory = ref(null);

const form = useForm({
    name: '',
});

const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const openEditModal = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    isEditModalOpen.value = true;
};

const closeModals = () => {
    isCreateModalOpen.value = false;
    isEditModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const storeCategory = () => {
    form.post(route('expense-categories.store'), {
        onSuccess: () => closeModals(),
    });
};

const updateCategory = () => {
    form.put(route('expense-categories.update', editingCategory.value.id), {
        onSuccess: () => closeModals(),
    });
};

const deleteCategory = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
        useForm({}).delete(route('expense-categories.destroy', id));
    }
};

const searchForm = useForm({ search: props.filters?.search || '' });
const onSearch = () => {
    searchForm.get(route('expense-categories.index'), { preserveState: true });
};
</script>

<template>
    <Head title="Kategori Pengeluaran" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategori Pengeluaran</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                    
                    <div class="flex justify-between items-center mb-6">
                        <div class="w-1/3 relative">
                            <TextInput 
                                v-model="searchForm.search" 
                                @keyup.enter="onSearch"
                                type="text" 
                                class="w-full pl-4 pr-10 py-2 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                                placeholder="Cari nama kategori..." 
                            />
                        </div>
                        <PrimaryButton @click="openCreateModal" class="shadow-sm hover:shadow transition-shadow">
                            + Tambah Kategori
                        </PrimaryButton>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Nama Kategori Pengeluaran</th>
                                    <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="category in categories.data" :key="category.id" class="bg-white border-b last:border-0 hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ category.name }}</td>
                                    <td class="px-6 py-4 text-right space-x-3">
                                        <button @click="openEditModal(category)" class="text-indigo-600 hover:text-indigo-900 font-medium transition-colors">Edit</button>
                                        <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900 font-medium transition-colors">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="categories.data.length === 0">
                                    <td colspan="2" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50">Belum ada data kategori pengeluaran.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between items-center" v-if="categories.links && categories.data.length > 0">
                        <span class="text-sm text-gray-500">Menampilkan {{ categories.from }} sampai {{ categories.to }} dari {{ categories.total }} data</span>
                        <div class="flex space-x-1">
                            <template v-for="(link, p) in categories.links" :key="p">
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
                    {{ isEditModalOpen ? 'Edit Kategori Pengeluaran' : 'Tambah Kategori Pengeluaran' }}
                </h2>

                <form @submit.prevent="isEditModalOpen ? updateCategory() : storeCategory()">
                    <div class="mb-6">
                        <InputLabel for="name" value="Nama Kategori (Contoh: Gaji Guru, Tagihan Listrik)" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus placeholder="Masukkan nama kategori..." />
                        <InputError :message="form.errors.name" class="mt-2" />
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
