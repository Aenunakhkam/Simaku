<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-extrabold text-xl md:text-2xl text-[#1a237e] leading-tight">Dashboard Utama</h2>
        </template>

        <!-- Welcome Banner & Quick Actions -->
        <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-2xl md:rounded-3xl border border-gray-100/60 relative mb-6">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#1a237e] to-[#0d47a1]"></div>
            <div class="p-5 md:p-8 text-gray-900 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-indigo-50 text-[#1a237e] rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg md:text-xl font-bold text-[#1a237e]">Selamat Datang, {{ $page.props.auth.user.npsn }}</h3>
                            <p class="text-xs md:text-sm text-gray-500 font-medium mt-1">Ringkasan aktivitas keuangan sekolah Anda hari ini.</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button class="flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-[#1a237e] to-[#0d47a1] hover:from-[#152847] hover:to-[#1a237e] shadow-lg transition-all active:scale-[0.98]">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Terima Pembayaran
                    </button>
                    <button class="flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-sm bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 shadow-sm transition-all active:scale-[0.98]">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
                        Catat Pengeluaran
                    </button>
                    <button class="flex items-center justify-center px-5 py-2.5 rounded-xl font-bold text-sm bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 shadow-sm transition-all active:scale-[0.98]">
                        <svg class="w-4 h-4 mr-2 text-[#1a237e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak BKU
                    </button>
                </div>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6">
            <!-- Saldo Kas -->
            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-2xl border border-gray-100/60 p-5 relative group hover:-translate-y-1 transition-all">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-[#0d47a1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Saldo Kas</div>
                <div class="text-3xl font-black text-[#1a237e]">Rp 0</div>
                <div class="mt-3 text-[10px] font-semibold text-blue-700 bg-blue-50/80 border border-blue-100 inline-block px-2 py-1 rounded-md">Buku Kas Umum</div>
            </div>

            <!-- Pemasukan -->
            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-2xl border border-gray-100/60 p-5 relative group hover:-translate-y-1 transition-all">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pemasukan Bulan Ini</div>
                <div class="text-3xl font-black text-green-600">Rp 0</div>
                <div class="mt-3 text-[10px] font-semibold text-green-700 bg-green-50/80 border border-green-100 inline-block px-2 py-1 rounded-md">+0% dari bulan lalu</div>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-2xl border border-gray-100/60 p-5 relative group hover:-translate-y-1 transition-all">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pengeluaran Bulan Ini</div>
                <div class="text-3xl font-black text-orange-600">Rp 0</div>
                <div class="mt-3 text-[10px] font-semibold text-orange-700 bg-orange-50/80 border border-orange-100 inline-block px-2 py-1 rounded-md">Beban Operasional</div>
            </div>

            <!-- Tunggakan -->
            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-2xl border border-gray-100/60 p-5 relative group hover:-translate-y-1 transition-all">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Tunggakan</div>
                <div class="text-3xl font-black text-red-600">Rp 0</div>
                <div class="mt-3 text-[10px] font-semibold text-red-700 bg-red-50/80 border border-red-100 inline-block px-2 py-1 rounded-md">Tagihan Aktif</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Chart Area (Mockup) -->
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-2xl border border-gray-100/60 p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[#1a237e] text-lg">Grafik Arus Kas (6 Bulan)</h3>
                    <select class="text-sm border-gray-200 rounded-lg text-gray-600 focus:ring-[#1a237e] focus:border-[#1a237e]">
                        <option>2026</option>
                        <option>2025</option>
                    </select>
                </div>
                <div class="flex-1 bg-gray-50/50 rounded-xl border border-gray-100 border-dashed flex items-center justify-center min-h-[250px]">
                    <div class="text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        <p class="text-sm font-medium">Grafik akan tampil setelah ada data transaksi.</p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions (Mockup) -->
            <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-2xl border border-gray-100/60 p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[#1a237e] text-lg">Transaksi Terakhir</h3>
                    <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800">Lihat Semua</a>
                </div>
                
                <div class="flex-1 flex flex-col justify-center items-center bg-gray-50/50 rounded-xl border border-gray-100 border-dashed min-h-[250px]">
                    <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <p class="text-sm font-medium text-gray-400">Belum ada transaksi tercatat.</p>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
