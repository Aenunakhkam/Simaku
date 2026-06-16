<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    classroom: Object,
    students: Array,
    stats: Object,
});

const search = ref('');
const expandedStudent = ref(null);

const toggleStudent = (id) => {
    if (expandedStudent.value === id) {
        expandedStudent.value = null;
    } else {
        expandedStudent.value = id;
    }
};

const filteredStudents = computed(() => {
    if (!search.value) return props.students;
    const q = search.value.toLowerCase();
    return props.students.filter(s => s.name.toLowerCase().includes(q) || s.nisn.includes(q));
});

const getProgressColor = (percentage) => {
    if (percentage >= 80) return 'bg-green-500';
    if (percentage >= 50) return 'bg-yellow-400';
    return 'bg-red-500';
};

const getProgressTextClass = (percentage) => {
    if (percentage >= 80) return 'text-green-700 bg-green-50 border-green-200';
    if (percentage >= 50) return 'text-yellow-700 bg-yellow-50 border-yellow-200';
    return 'text-red-700 bg-red-50 border-red-200';
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Detail Kelas ${classroom.level} ${classroom.name}`" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <span class="text-gray-400 font-normal">Statistik /</span> Kelas {{ classroom.level }} {{ classroom.name }}
                </h2>
                <div class="flex items-center gap-3">
                    <a :href="route('reports.classroom.pdf', classroom.id)" target="_blank" class="text-sm font-bold bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Cetak PDF Kelas
                    </a>
                    <Link :href="route('reports.classrooms')" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Siswa</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_students }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Tagihan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_billings }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-500 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Lunas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.paid_billings }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 flex items-center gap-4 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10" :class="getProgressColor(stats.percentage)"></div>
                        <div class="relative w-12 h-12 rounded-full flex items-center justify-center shrink-0 border-2" :class="getProgressTextClass(stats.percentage)">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <div class="relative">
                            <p class="text-sm font-medium text-gray-500">Rata-rata Kelunasan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.percentage }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Main Table -->
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <div class="p-6 md:px-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                        <h3 class="text-lg font-bold text-gray-800">Daftar Anggota Kelas</h3>
                        <div class="relative w-full md:w-64">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Cari Siswa..." 
                                class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors text-sm"
                            >
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold text-center w-12">No</th>
                                    <th scope="col" class="px-6 py-4 font-bold w-1/3">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Status Tagihan</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Progress Kelunasan</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Aksi</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(student, index) in filteredStudents" :key="student.id">
                                    <tr class="bg-white border-b hover:bg-indigo-50/30 transition-colors cursor-pointer" @click="toggleStudent(student.id)">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 font-medium">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ student.name }}</div>
                                            <div class="text-xs text-gray-500">NISN: {{ student.nisn }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border" :class="getProgressTextClass(student.payment_percentage)">
                                                {{ student.paid_billings }} / {{ student.total_billings }} Lunas
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 w-1/3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden flex-1">
                                                    <div class="h-2 rounded-full transition-all duration-1000 ease-out" 
                                                        :class="getProgressColor(student.payment_percentage)" 
                                                        :style="`width: ${student.payment_percentage}%`">
                                                    </div>
                                                </div>
                                                <span class="text-xs font-bold w-10 text-right" :class="getProgressTextClass(student.payment_percentage).split(' ')[0]">
                                                    {{ student.payment_percentage }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a :href="route('reports.student.pdf', student.id)" @click.stop target="_blank" class="inline-flex text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Cetak PDF Siswa">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button class="text-gray-400 hover:text-indigo-600 transition-colors p-2">
                                                <svg class="w-5 h-5 transform transition-transform" :class="{'rotate-180': expandedStudent === student.id}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="expandedStudent === student.id" class="bg-gray-50/80 border-b">
                                        <td colspan="5" class="px-6 py-4">
                                            <div class="border rounded-xl bg-white overflow-hidden shadow-sm">
                                                <table class="w-full text-xs text-left">
                                                    <thead class="bg-gray-100 text-gray-600 uppercase">
                                                        <tr>
                                                            <th class="px-4 py-2 font-bold">Kategori Tagihan</th>
                                                            <th class="px-4 py-2 font-bold">Tahun Ajaran</th>
                                                            <th class="px-4 py-2 font-bold text-right">Nominal (Rp)</th>
                                                            <th class="px-4 py-2 font-bold text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="billing in student.billings" :key="billing.id" class="border-t">
                                                            <td class="px-4 py-2 font-semibold">{{ billing.category?.name }}</td>
                                                            <td class="px-4 py-2 text-gray-500">{{ billing.academic_year?.name }}</td>
                                                            <td class="px-4 py-2 text-right">{{ new Intl.NumberFormat('id-ID').format(billing.amount) }}</td>
                                                            <td class="px-4 py-2 text-center">
                                                                <span v-if="billing.is_paid" class="px-2 py-0.5 rounded bg-green-100 text-green-700 font-bold border border-green-200">Lunas</span>
                                                                <span v-else class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-bold border border-red-200">Belum Lunas</span>
                                                            </td>
                                                        </tr>
                                                        <tr v-if="student.billings?.length === 0">
                                                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Siswa ini tidak memiliki data tagihan.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-if="filteredStudents.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        Siswa yang dicari tidak ditemukan dalam kelas ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
