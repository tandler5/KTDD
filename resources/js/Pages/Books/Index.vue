<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    books: Array
});

const form = useForm({
    book_id: null
});

const rentBook = (id) => {
    form.book_id = id;
    form.post(route('rentals.store'), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Books" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book Rental</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div v-for="book in books" :key="book.id" class="border-b py-4 flex justify-between items-center">
                        <div>
                            <div class="text-lg font-bold">{{ book.title }}</div>
                            <div class="text-gray-600">{{ book.author }}</div>
                        </div>
                        <div>
                            <button
                                v-if="book.is_available"
                                @click="rentBook(book.id)"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                            >
                                Rent
                            </button>
                            <span v-else class="text-red-500 font-semibold">
                                Already Rented
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
