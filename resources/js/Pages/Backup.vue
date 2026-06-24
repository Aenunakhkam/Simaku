<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    backups: Array
});

const isProcessing = ref(false);

const processBackup = () => {
    isProcessing.value = true;
    router.post(route('backup.create'), {}, {
        onFinish: () => isProcessing.value = false,
        preserveScroll: true
    });
};

const downloadBackup = (fileName) => {
    window.location.href = route('backup.download', fileName);
};

const deleteBackup = (fileName) => {
    if (confirm('Apakah Anda yakin ingin menghapus backup ini?')) {
        router.delete(route('backup.delete', fileName), {
            preserveScroll: true
        });
    }
};

const restoreForm = useForm({
    backup_file: null,
});

const handleFileChange = (e) => {
    restoreForm.backup_file = e.target.files[0];
};

const processRestore = () => {
    if (!restoreForm.backup_file) return;
    restoreForm.post(route('backup.restore'), {
        preserveScroll: true,
        onSuccess: () => restoreForm.reset(),
    });
};
</script>

<template>
    <Head title="Backup Database" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengaturan Database</h2>
        </template>

        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div v-if="$page.props.flash.error" class="bg-red-50 text-red-700 p-4 rounded-lg border border-red-100 text-sm mb-4">
                {{ $page.props.flash.error }}
            </div>
            <div v-if="$page.props.flash.success" class="bg-green-50 text-green-700 p-4 rounded-lg border border-green-100 text-sm mb-4">
                {{ $page.props.flash.success }}
            </div>

            <!-- Card Backup Database -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="text-lg font-semibold text-gray-700">Backup Database</h3>
                    <button 
                        @click="processBackup" 
                        :disabled="isProcessing"
                        class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 focus:bg-indigo-600 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                    >
                        <svg v-if="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        {{ isProcessing ? 'Memproses...' : 'Proses Backup' }}
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Waktu Backup
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ukuran Berkas
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="!backups || backups.length === 0">
                                <td colspan="3" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Tidak ada data untuk ditampilkan
                                </td>
                            </tr>
                            <tr v-for="backup in backups" :key="backup.name" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ backup.date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ backup.size }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <button @click="downloadBackup(backup.name)" class="text-indigo-600 hover:text-indigo-900 mr-4">Unduh</button>
                                    <button @click="deleteBackup(backup.name)" class="text-red-600 hover:text-red-900">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card Restore Database -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">Restore Database</h3>
                </div>
                <div class="p-6 bg-gray-50">
                    <form @submit.prevent="processRestore" class="flex flex-col md:flex-row gap-4 items-center">
                        <div class="relative flex-1 w-full flex items-center">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            <input 
                                type="file" 
                                accept=".zip,.sql"
                                @change="handleFileChange"
                                class="pl-10 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 h-10"
                            />
                        </div>
                        <button 
                            type="submit" 
                            :disabled="restoreForm.processing || !restoreForm.backup_file"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 h-10 whitespace-nowrap"
                        >
                            {{ restoreForm.processing ? 'Memproses...' : 'Restore' }}
                        </button>
                    </form>
                    <div v-if="restoreForm.errors.backup_file" class="text-red-600 text-sm mt-2">
                        {{ restoreForm.errors.backup_file }}
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
