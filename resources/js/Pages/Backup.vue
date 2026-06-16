<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const isDownloading = ref(false);

const downloadBackup = () => {
    isDownloading.value = true;
    window.location.href = route('backup.download');
    setTimeout(() => {
        isDownloading.value = false;
    }, 3000);
};
</script>

<template>
    <Head title="Backup Data" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-xl md:text-2xl text-[#1a237e] leading-tight">Backup Database</h2>
        </template>

        <div class="max-w-4xl mx-auto py-8">
            <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100 p-8 text-center">
                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Amankan Data Anda secara Berkala</h3>
                <p class="text-gray-500 mb-8 max-w-lg mx-auto">Klik tombol di bawah ini untuk mengunduh salinan lengkap database sistem keuangan sekolah Anda. Simpan file hasil unduhan di tempat yang aman (Flashdisk/Google Drive).</p>

                <div v-if="$page.props.flash.error" class="mb-4 bg-red-50 text-red-700 p-4 rounded-xl text-sm font-medium border border-red-100 max-w-lg mx-auto">
                    {{ $page.props.flash.error }}
                </div>

                <button 
                    @click="downloadBackup" 
                    :disabled="isDownloading"
                    class="inline-flex items-center px-8 py-3.5 bg-[#1a237e] text-white font-bold rounded-xl shadow-lg hover:bg-[#152847] hover:shadow-xl transition-all disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    <svg v-if="isDownloading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <svg v-else class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    {{ isDownloading ? 'Memproses Backup...' : 'Mulai Proses Backup (Unduh)' }}
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
