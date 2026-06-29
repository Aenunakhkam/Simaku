<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    majors: Array,
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
        <Head title="Statistik Jurusan" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Statistik Pembayaran per Jurusan</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Statistik Tagihan Berdasarkan Jurusan</h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih jurusan di bawah ini untuk melihat detail masing-masing siswa.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <div 
                        v-for="major in majors" 
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
                        <div class="mt-auto pt-4 border-t border-gray-100 flex justify-end">
                            <Link :href="route('reports.major.detail', major.id)" class="text-center py-2 px-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-sm font-bold transition-colors w-full">
                                Detail Siswa
                            </Link>
                        </div>
                    </div>

                    <div v-if="!majors || majors.length === 0" class="col-span-full bg-white rounded-2xl p-8 text-center border border-gray-100">
                        <p class="text-gray-500">Belum ada data jurusan.</p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
