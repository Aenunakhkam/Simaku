<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    status: {
        type: String,
    },
    academicYears: {
        type: Array,
        default: () => [],
    }
});

const form = useForm({
    email: '',
    password: '',
    academic_year_id: '',
    remember: false,
});

const showPassword = ref(false);
const isVerifying = ref(false);
const isVerified = ref(false);
const showCaptchaError = ref(false);

onMounted(() => {
    // Cari tahun ajaran aktif, jika ada set sbg default
    const active = props.academicYears?.find(y => y.is_active);
    if (active) {
        form.academic_year_id = active.id;
    } else if (props.academicYears?.length > 0) {
        form.academic_year_id = props.academicYears[0].id;
    }
});

const handleVerify = (e) => {
    if (isVerified.value) {
        e.preventDefault();
        return;
    }
    e.preventDefault();
    isVerifying.value = true;
    setTimeout(() => {
        isVerifying.value = false;
        isVerified.value = true;
        showCaptchaError.value = false;
    }, 1500);
};

const submit = () => {
    form.clearErrors();
    showCaptchaError.value = false;
    
    if (!isVerified.value) {
        showCaptchaError.value = true;
        return;
    }
    
    if (!form.email || !form.password) {
        if (!form.email) form.setError('email', 'Email atau sandi harus di isi');
        if (!form.password) form.setError('password', 'Email atau sandi harus di isi');
        return;
    }

    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk ke SIMAKU" />

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-[#1a237e] tracking-tight mb-2">Selamat Datang!</h2>
            <p class="text-[15px] text-gray-500 font-medium">Masuk untuk mengakses Layanan SIMAKU Sekolah</p>
        </div>

        <!-- Warning Banner for Unchecked Verification -->
        <div v-if="showCaptchaError" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
            <svg class="h-5 w-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <h4 class="text-sm font-bold text-red-800">Login Gagal</h4>
                <p class="text-xs font-semibold text-red-600 mt-0.5">Invalid captcha</p>
                <p class="text-xs text-red-700 mt-1">Periksa kembali data yang Anda masukkan. Jika masalah berlanjut, hubungi tim dukungan.</p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="academic_year_id" class="block font-bold text-[#1a237e] mb-2 text-sm">Pilih Tahun Ajaran / Sesi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <select
                        id="academic_year_id"
                        v-model="form.academic_year_id"
                        class="block w-full pl-11 pr-10 py-3.5 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 rounded-xl text-gray-900 transition-all text-[15px] font-bold"
                    >
                        <option value="" disabled>-- Pilih Tahun Ajaran --</option>
                        <option v-for="year in academicYears" :key="year.id" :value="year.id">
                            {{ year.name }} {{ year.is_active ? '(Aktif)' : '' }}
                        </option>
                    </select>
                </div>
            </div>

            <div>
                <label for="email" class="block font-bold text-[#1a237e] mb-2 text-sm">Alamat Email</label>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input
                        id="email"
                        type="email"
                        class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 rounded-xl text-gray-900 transition-all text-[15px]"
                        v-model="form.email"
                        autofocus
                        autocomplete="username"
                        placeholder="Email khusus pengelola..."
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="block font-bold text-[#1a237e] mb-2 text-sm">Kata Sandi</label>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 rounded-xl text-gray-900 transition-all text-[15px]"
                        v-model="form.password"
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi Anda"
                    />
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" @click="showPassword = !showPassword">
                        <svg v-if="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg v-else class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    </div>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Fake CAPTCHA for look & feel -->
            <div>
                <InputLabel value="Verifikasi" class="font-bold text-[#1a237e] mb-2 text-sm" />
                <div class="border border-gray-200 rounded-xl p-3 flex items-center gap-3 bg-gray-50/50 max-w-[280px]">
                    <div v-if="isVerifying" class="w-5 h-5 border-2 border-[#1a237e] border-t-transparent rounded-full animate-spin ml-1"></div>
                    <input v-else type="checkbox" id="captcha-checkbox" :checked="isVerified" class="w-5 h-5 text-[#1a237e] rounded border-gray-300 focus:ring-[#1a237e] ml-1 pointer-events-none">
                    <label for="captcha-checkbox" class="text-sm font-bold text-[#1a237e] cursor-pointer flex-1" @click.prevent="handleVerify">Saya bukan robot</label>
                    <div class="ml-auto">
                        <!-- Captcha Logo Placeholder -->
                        <div class="w-8 h-8 opacity-40 bg-[url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%23000\'%3E%3Cpath d=\'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z\'/%3E%3C/svg%3E')]"></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center pt-2">
                <Link
                    :href="route('password.request')"
                    class="text-[14px] font-bold text-[#1a237e] hover:text-blue-700 transition-colors"
                >
                    Lupa Kata Sandi?
                </Link>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl shadow-lg text-[15px] font-bold text-white bg-gradient-to-r from-[#1a237e] to-[#0d47a1] hover:from-[#152847] hover:to-[#1a237e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a237e] transition-all duration-300 active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Masuk
                </button>
            </div>
            
            <div class="relative py-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white text-gray-400 font-bold tracking-widest">ATAU</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <Link :href="route('register')" class="flex items-center p-3 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-colors group cursor-pointer">
                    <div class="bg-gray-100 group-hover:bg-white p-2 rounded-lg mr-3 shrink-0">
                        <svg class="w-5 h-5 text-[#1a237e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-[#1a237e] leading-tight">Pengguna Baru?</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Daftar di sini</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto text-gray-400 group-hover:text-[#1a237e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
