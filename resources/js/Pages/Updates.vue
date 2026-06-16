<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    updates: Array
});
</script>

<template>
    <Head title="Cek Pembaruan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-xl md:text-2xl text-[#1a237e] leading-tight">Riwayat Pembaruan Aplikasi</h2>
        </template>

        <div class="max-w-4xl mx-auto py-8">
            <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Tentang Sistem</h3>
                    <p class="text-sm text-gray-600 mt-1">Sistem Manajemen Administrasi Keuangan (SIMAKU) dikembangkan oleh <strong>Aenunakhkam</strong>. <br/>Anda bisa mengecek kode sumber melalui Github resmi.</p>
                    <a href="https://github.com/Aenunakhkam/Simaku" target="_blank" class="inline-flex items-center mt-4 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-gray-800 transition">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path></svg>
                        Lihat Repository Github
                    </a>
                </div>

                <div class="p-6 md:p-8 space-y-8">
                    <div v-for="(update, index) in updates" :key="index" class="relative pl-6 sm:pl-8">
                        <div class="absolute left-0 top-1.5 w-3 h-3 bg-[#1a237e] rounded-full border-4 border-indigo-100/50"></div>
                        <div class="absolute left-1.5 top-5 w-px h-full bg-gray-200" v-if="index !== updates.length - 1"></div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 mb-3">
                            <h4 class="text-xl font-black text-gray-900">{{ update.version }}</h4>
                            <span class="text-sm font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">{{ update.date }}</span>
                        </div>
                        
                        <ul class="space-y-2 mt-4 text-gray-700">
                            <li v-for="(change, cIndex) in update.changes" :key="cIndex" class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-[15px]">{{ change }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
