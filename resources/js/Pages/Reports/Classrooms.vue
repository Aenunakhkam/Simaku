<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    classrooms: Array,
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
                        <h3 class="text-xl font-bold text-gray-800">Ringkasan Kelunasan Tagihan</h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih kelas di bawah ini untuk melihat detail masing-masing siswa.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <Link 
                        v-for="classroom in sortedClassrooms" 
                        :key="classroom.id"
                        :href="route('reports.classroom.detail', classroom.id)"
                        class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all group block"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-lg font-bold text-[#1a237e] group-hover:text-indigo-600 transition-colors">
                                    {{ classroom.level }} {{ classroom.name }}
                                </h4>
                                <p class="text-sm font-medium text-gray-500 mt-0.5">
                                    {{ classroom.major?.name || 'Umum' }}
                                </p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
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

                        <div class="flex justify-between text-xs text-gray-400 font-medium">
                            <span>Lunas: {{ classroom.paid_billings }}</span>
                            <span>Total Tagihan: {{ classroom.total_billings }}</span>
                        </div>
                    </Link>

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
