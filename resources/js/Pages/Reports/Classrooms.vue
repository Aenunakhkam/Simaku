<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    classrooms: Array,
    majorStats: Array,
});

// Urutkan kelas berdasarkan tingkat kelunasan (tertinggi ke terendah) atau level
const sortedClassrooms = computed(() => {
    return [...props.classrooms].sort((a, b) => b.payment_percentage - a.payment_percentage);
});

const getProgressColor = (percentage) => {
    if (percentage >= 80) return 'bg-green-500';
    if (percentage >= 50) return 'bg-yellow-400';
    return 'bg-red-500';
};

const getProgressTextClass = (percentage) => {
    if (percentage >= 80) return 'text-green-700';
    if (percentage >= 50) return 'text-yellow-700';
    return 'text-red-700';
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Statistik Kelas" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Statistik Pembayaran per Kelas</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Statistik Tagihan Berdasarkan Jurusan</h3>
                        <p class="text-sm text-gray-500 mt-1">Ringkasan pembayaran seluruh siswa (tagihan aktif) per jurusan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <div 
                        v-for="major in majorStats" 
                        :key="major.id"
                        class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl p-6 shadow-sm border border-indigo-100 flex flex-col"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-xl font-black text-indigo-900">{{ major.code }}</h4>
                                <p class="text-sm font-medium text-indigo-700 mt-0.5">{{ major.name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold" :class="getProgressTextClass(major.percentage)">{{ major.percentage }}%</span>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Lunas</p>
                            </div>
                        </div>

                        <div class="w-full bg-indigo-100 rounded-full h-2.5 mb-5 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all duration-1000 ease-out" 
                                 :class="getProgressColor(major.percentage)" 
                                 :style="`width: ${major.percentage}%`">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 text-center mt-auto">
                            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-50">
                                <p class="text-xs text-gray-500 font-bold mb-1">Lunas</p>
                                <p class="text-lg font-black text-green-600">{{ major.lunas }}</p>
                            </div>
                            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-50">
                                <p class="text-xs text-gray-500 font-bold mb-1">Belum Lunas</p>
                                <p class="text-lg font-black text-yellow-500">{{ major.belum_lunas }}</p>
                            </div>
                            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-50">
                                <p class="text-xs text-gray-500 font-bold mb-1">Belum Bayar</p>
                                <p class="text-lg font-black text-red-500">{{ major.belum_bayar }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="!majorStats || majorStats.length === 0" class="col-span-full bg-white rounded-2xl p-8 text-center border border-gray-100">
                        <p class="text-gray-500">Belum ada data jurusan.</p>
                    </div>
                </div>

                <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Ringkasan Kelunasan Tagihan per Kelas</h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih kelas di bawah ini untuk melihat detail masing-masing siswa.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div 
                        v-for="classroom in sortedClassrooms" 
                        :key="classroom.id"
                        class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-lg transition-all flex flex-col"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-lg font-bold text-[#1a237e]">
                                    {{ classroom.level }} {{ classroom.name }}
                                </h4>
                                <p class="text-sm font-medium text-gray-500 mt-0.5">
                                    {{ classroom.major?.name || 'Umum' }}
                                </p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500 font-medium"><span class="font-bold text-gray-800">{{ classroom.students_count }}</span> Siswa</span>
                            <span :class="['font-bold', getProgressTextClass(classroom.payment_percentage)]">
                                {{ classroom.payment_percentage }}% Lunas
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-100 rounded-full h-2.5 mb-3 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all duration-1000 ease-out" 
                                 :class="getProgressColor(classroom.payment_percentage)" 
                                 :style="`width: ${classroom.payment_percentage}%`">
                            </div>
                        </div>

                        <div class="flex justify-between text-xs text-gray-400 font-medium mb-5">
                            <span>Lunas: {{ classroom.paid_billings }}</span>
                            <span>Total Tagihan: {{ classroom.total_billings }}</span>
                        </div>

                        <div class="mt-auto flex items-center gap-2 pt-4 border-t border-gray-100">
                            <Link :href="route('reports.classroom.detail', classroom.id)" class="flex-1 text-center py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-sm font-bold transition-colors">
                                Detail Siswa
                            </Link>
                            <a :href="route('reports.classroom.pdf', classroom.id)" target="_blank" class="flex items-center justify-center py-2 px-3 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-bold transition-colors" title="Cetak Rekap Kelas (PDF)">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </a>
                        </div>
                    </div>

                    <div v-if="classrooms.length === 0" class="col-span-full bg-white rounded-2xl p-12 text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <h4 class="text-lg font-bold text-gray-800">Belum Ada Data Kelas</h4>
                        <p class="text-sm text-gray-500 mt-1">Tambahkan data kelas dan siswa terlebih dahulu.</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
