<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    categories: Array,
});

const form = useForm({
    expense_category_id: '',
    date: new Date().toISOString().split('T')[0],
    amount: '',
    note: '',
    proof_file: null,
});

const submit = () => {
    form.post(route('expenses.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Catat Pengeluaran" />

        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Catat Pengeluaran Baru</h2>
                <Link :href="route('expenses.index')" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <div class="bg-gradient-to-b from-indigo-50/50 to-white px-8 py-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-[#1a237e] flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Formulir Pengeluaran
                        </h3>
                        <p class="text-sm text-gray-500 mt-1 ml-8">Isi data pengeluaran dengan lengkap dan benar.</p>
                    </div>

                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="date" value="Tanggal Transaksi" class="font-semibold text-gray-700" />
                                    <TextInput
                                        id="date"
                                        type="date"
                                        class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors"
                                        v-model="form.date"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.date" />
                                </div>

                                <div>
                                    <InputLabel for="expense_category_id" value="Kategori Pengeluaran" class="font-semibold text-gray-700" />
                                    <select
                                        id="expense_category_id"
                                        class="mt-1 block w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors"
                                        v-model="form.expense_category_id"
                                        required
                                    >
                                        <option value="" disabled>Pilih Kategori...</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.expense_category_id" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="amount" value="Nominal (Rp)" class="font-semibold text-gray-700" />
                                <div class="relative mt-1 rounded-xl shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                        <span class="text-gray-500 sm:text-sm font-medium">Rp</span>
                                    </div>
                                    <TextInput
                                        id="amount"
                                        type="number"
                                        min="0"
                                        class="block w-full pl-12 rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors"
                                        v-model="form.amount"
                                        required
                                        placeholder="Contoh: 150000"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.amount" />
                            </div>

                            <div>
                                <InputLabel for="note" value="Keterangan (Opsional)" class="font-semibold text-gray-700" />
                                <textarea
                                    id="note"
                                    class="mt-1 block w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 focus:bg-white transition-colors"
                                    v-model="form.note"
                                    rows="3"
                                    placeholder="Tulis rincian pengeluaran..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.note" />
                            </div>

                            <div>
                                <InputLabel for="proof_file" value="Bukti Transaksi (Struk/Nota/Invoice)" class="font-semibold text-gray-700" />
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer relative" @click="$refs.fileInput.click()">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="relative cursor-pointer rounded-md bg-transparent font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500">
                                                <span>Unggah file</span>
                                                <input
                                                    id="proof_file"
                                                    ref="fileInput"
                                                    type="file"
                                                    class="sr-only"
                                                    @change="form.proof_file = $event.target.files[0]"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                />
                                            </span>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, PDF up to 2MB</p>
                                        <p v-if="form.proof_file" class="text-sm font-semibold text-indigo-600 mt-2 truncate max-w-xs">{{ form.proof_file.name }}</p>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.proof_file" />
                            </div>

                            <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="shadow-md hover:shadow-lg transition-all rounded-xl px-8 py-3 text-base">
                                    Simpan Transaksi
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
