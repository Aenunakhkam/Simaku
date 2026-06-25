<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    appUpdates: Array,
    remoteCommits: Array,
    newCommitsCount: Number,
    lastFetch: String,
    filters: Object
});

const activeTab = ref('aplikasi');
const isPulling = ref(false);

const pullForm = useForm({});

const pullUpdates = () => {
    isPulling.value = true;
    pullForm.post(route('updates.pull'), {
        preserveScroll: true,
        onFinish: () => {
            isPulling.value = false;
        }
    });
};

const perPage = ref(props.filters?.per_page || 10);
watch(perPage, (value) => {
    router.get(route('updates'), { per_page: value }, {
        preserveState: true,
        replace: true
    });
});
</script>

<template>
    <Head title="Cek Pembaruan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-xl md:text-2xl text-[#1a237e] leading-tight">Cek Pembaruan Aplikasi</h2>
        </template>

        <div class="max-w-6xl mx-auto py-8">
            <!-- Alert Messages -->
            <div v-if="$page.props.flash.message" class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl text-sm font-medium border border-green-100 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ $page.props.flash.message }}
            </div>
            <div v-if="$page.props.flash.error" class="mb-4 bg-red-50 text-red-700 p-4 rounded-xl text-sm font-medium border border-red-100">
                {{ $page.props.flash.error }}
            </div>

            <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100 overflow-hidden">
                <!-- Header Card -->
                <div class="p-6 md:px-8 md:py-6 flex flex-col md:flex-row md:items-center justify-between border-b border-gray-100">
                    <h3 class="text-xl font-bold text-[#4361ee]">
                        Daftar Perubahan Aplikasi
                    </h3>
                    <div class="mt-4 md:mt-0 text-sm text-gray-400 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Git Pull terakhir: {{ lastFetch }}
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-gray-200 bg-gray-50/50">
                    <button 
                        @click="activeTab = 'aplikasi'"
                        class="flex-1 py-4 px-6 text-center text-sm font-bold border-b-2 transition-colors flex items-center justify-center space-x-2"
                        :class="activeTab === 'aplikasi' ? 'border-[#4361ee] text-[#4361ee] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Aplikasi</span>
                    </button>
                    <button 
                        @click="activeTab = 'github'"
                        class="flex-1 py-4 px-6 text-center text-sm font-bold border-b-2 transition-colors flex items-center justify-center space-x-2"
                        :class="activeTab === 'github' ? 'border-[#4361ee] text-[#4361ee] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path></svg>
                        <span>Github</span>
                        <span v-if="newCommitsCount > 0" class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full ml-2 animate-pulse">{{ newCommitsCount }} Baru</span>
                    </button>
                </div>

                <!-- Tab Content: Aplikasi (Local) -->
                <div v-show="activeTab === 'aplikasi'" class="p-6 md:p-8">
                    <div v-if="appUpdates && appUpdates.length > 0" class="space-y-8">
                        <div v-for="(update, index) in appUpdates" :key="index" class="space-y-4">
                            <h4 class="text-lg font-bold text-gray-800">{{ update.version }}</h4>
                            <ol class="list-decimal pl-5 space-y-2 text-[15px] text-gray-600">
                                <li v-for="(change, cIndex) in update.changes" :key="cIndex">
                                    <span v-if="change.startsWith('[Pembaruan]')" class="text-green-600 font-bold">[Pembaruan]</span>
                                    <span v-else-if="change.startsWith('[Perbaikan]')" class="text-red-500 font-bold">[Perbaikan]</span>
                                    {{ change.replace(/\[Pembaruan\]|\[Perbaikan\]/g, '') }}
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-400 py-12">
                        Belum ada riwayat pembaruan yang tercatat.
                    </div>
                </div>

                <!-- Tab Content: Github (Remote) -->
                <div v-show="activeTab === 'github'" class="overflow-x-auto">
                    <!-- Banner jika ada update -->
                    <div v-if="newCommitsCount > 0" class="bg-indigo-50 border-b border-indigo-100 p-4 px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center text-indigo-800">
                            <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <div>
                                <h4 class="font-bold">Pembaruan Tersedia!</h4>
                                <p class="text-sm opacity-90 mt-0.5">Ada {{ newCommitsCount }} pembaruan baru dari repositori Github yang belum Anda pasang.</p>
                            </div>
                        </div>
                        <button 
                            @click="pullUpdates" 
                            :disabled="isPulling"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-[#4361ee] text-white text-sm font-bold rounded-lg shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all disabled:opacity-75 disabled:cursor-not-allowed whitespace-nowrap"
                        >
                            <svg v-if="isPulling" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            {{ isPulling ? 'Menarik Kode...' : 'Tarik Pembaruan Sekarang' }}
                        </button>
                    </div>

                    <!-- Banner jika sudah up to date -->
                    <div v-if="newCommitsCount === 0" class="bg-green-50 border-b border-green-100 p-4 px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center text-green-800">
                            <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <h4 class="font-bold">Aplikasi Anda Sudah Versi Terbaru!</h4>
                                <p class="text-sm opacity-90 mt-0.5">Tidak ada kode pembaruan baru dari repositori Github saat ini.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 bg-white">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500 font-medium">Tampilkan</span>
                            <select v-model="perPage" class="border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-1.5 pl-3 pr-8">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="all">Semua</option>
                            </select>
                            <span class="text-sm text-gray-500 font-medium">baris</span>
                        </div>
                    </div>
                    
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider w-16">NO</th>
                                <th class="px-6 py-4 font-bold tracking-wider w-48">DATE</th>
                                <th class="px-6 py-4 font-bold tracking-wider">COMMIT</th>
                                <th class="px-6 py-4 font-bold tracking-wider w-48">AUTHOR</th>
                            </tr>
                        </thead>
                        <tbody v-if="remoteCommits.length > 0">
                            <tr v-for="(commit, index) in remoteCommits" :key="commit.hash" class="bg-white border-b hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-gray-900 font-medium">{{ index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ commit.date }}</td>
                                <td class="px-6 py-4 text-gray-900 font-medium">{{ commit.message }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ commit.author }}</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="font-bold text-gray-700">Gagal Memuat Riwayat Github</p>
                                        <p class="text-sm mt-1">Pastikan Anda terhubung dengan internet untuk melihat riwayat dari Github.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
