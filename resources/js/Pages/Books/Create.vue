<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    author: '',
    isbn: '',
});

const submit = () => {
    form.post(route('books.store'));
};
</script>

<template>
    <Head title="Add Book" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add New Book</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 max-w-xl">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="title" value="Title" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.title"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="author" value="Author" />
                                <TextInput
                                    id="author"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.author"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.author" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="isbn" value="ISBN" />
                                <TextInput
                                    id="isbn"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.isbn"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.isbn" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Save Book
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
