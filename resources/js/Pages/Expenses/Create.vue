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

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Catat Pengeluaran Baru</h2>
                    <Link :href="route('expenses.index')" class="text-gray-600 hover:text-gray-900 font-medium text-sm flex items-center gap-1">
                        &larr; Kembali
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div>
                            <InputLabel for="date" value="Tanggal Transaksi" />
                            <TextInput
                                id="date"
                                type="date"
                                class="mt-1 block w-full"
                                v-model="form.date"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.date" />
                        </div>

                        <div>
                            <InputLabel for="expense_category_id" value="Kategori Pengeluaran" />
                            <select
                                id="expense_category_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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

                        <div>
                            <InputLabel for="amount" value="Nominal (Rp)" />
                            <TextInput
                                id="amount"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                                v-model="form.amount"
                                required
                                placeholder="Contoh: 150000"
                            />
                            <InputError class="mt-2" :message="form.errors.amount" />
                        </div>

                        <div>
                            <InputLabel for="note" value="Keterangan (Opsional)" />
                            <textarea
                                id="note"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                v-model="form.note"
                                rows="3"
                                placeholder="Tulis rincian pengeluaran..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.note" />
                        </div>

                        <div>
                            <InputLabel for="proof_file" value="Bukti Transaksi (Struk/Nota/Invoice)" />
                            <input
                                id="proof_file"
                                type="file"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                @input="form.proof_file = $event.target.files[0]"
                                accept=".jpg,.jpeg,.png,.pdf"
                            />
                            <p class="mt-1 text-xs text-gray-500">Maksimal 2MB. Format: JPG, PNG, PDF.</p>
                            <InputError class="mt-2" :message="form.errors.proof_file" />
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Simpan Transaksi
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
