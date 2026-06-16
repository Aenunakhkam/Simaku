<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

const page = usePage();

const showNotification = () => {
    const flash = page.props.flash;
    if (flash?.success) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: flash.success,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    }
    if (flash?.status) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: flash.status,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    }
};

onMounted(() => {
    showNotification();
});

watch(() => page.props.flash, () => {
    showNotification();
}, { deep: true });
</script>

<template>
    <div class="min-h-screen flex font-sans bg-[#F8F9FA] relative overflow-hidden text-slate-800">
        <!-- Abstract Background Shapes mimicking Coretax -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -left-[20%] top-[-10%] w-[60%] h-[60%] rounded-full bg-gradient-to-br from-blue-100/40 to-transparent blur-3xl"></div>
            <div class="absolute -right-[10%] bottom-[-10%] w-[50%] h-[50%] rounded-full bg-gradient-to-tl from-indigo-100/50 to-transparent blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row relative z-10 w-full h-full min-h-screen">
            
            <!-- Left Side: Branding / Imagery -->
            <div class="w-full lg:w-[55%] flex flex-col justify-between pr-0 lg:pr-12 pt-4">
                
                <!-- Logo Top Left -->
                <div class="flex items-center space-x-3 mb-16 lg:mb-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-800 to-indigo-900 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <span class="text-2xl font-extrabold text-[#1a237e] tracking-tight">SIMAKU</span>
                </div>

                <!-- Main Hero Text -->
                <div class="mt-8 lg:mt-auto lg:mb-auto">
                    <h1 class="text-[2.5rem] lg:text-[3.5rem] font-extrabold leading-[1.1] text-[#1a237e] tracking-tight mb-4">
                        Sistem Manajemen <br/> Administrasi Keuangan
                    </h1>
                    <div class="w-16 h-1.5 bg-yellow-400 rounded-full mb-10"></div>
                    
                    <!-- Promotional Banner Placeholder -->
                    <div class="hidden lg:block w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-100/50 p-6 relative overflow-hidden mt-8 group hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform"></div>
                        <h3 class="font-bold text-xl text-blue-900 flex items-center gap-2 mb-4">
                            Update <span class="text-blue-500">Tampilan</span>
                            <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full transform -rotate-12">NEW</span>
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 leading-tight">Fully responsive</p>
                                    <p class="text-xs text-gray-500">semua perangkat</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 leading-tight">User-friendly</p>
                                    <p class="text-xs text-gray-500">tampilan lebih segar</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 leading-tight">Dashboard Interaktif</p>
                                    <p class="text-xs text-gray-500">analitik realtime</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 leading-tight">Automasi BKU</p>
                                    <p class="text-xs text-gray-500">direct posting</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="inline-block bg-yellow-400 text-[#1a237e] text-xs font-bold px-3 py-1 rounded-full">
                                Penyempurnaan layanan dalam SIMAKU dilakukan secara bertahap.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:block mt-8 text-sm text-gray-400 font-medium pb-8">
                    &copy; {{ new Date().getFullYear() }} SIMAKU - Sistem Manajemen Keuangan Sekolah
                </div>
            </div>

            <!-- Right Side: Form Area -->
            <div class="w-full lg:w-[45%] flex flex-col justify-center items-center py-10 lg:py-0">
                <div class="w-full max-w-[480px]">
                    <div class="bg-white rounded-3xl shadow-[0_10px_40px_rgb(0,0,0,0.06)] border border-gray-100 p-8 sm:p-10 w-full relative">
                        <slot />
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
