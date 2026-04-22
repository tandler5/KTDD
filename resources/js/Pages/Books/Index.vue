<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

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

const returnBook = (id) => {
    form.book_id = id;
    form.post(route('rentals.return'), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Books" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book Management</h2>
                <Link :href="route('books.create')">
                    <PrimaryButton>Add New Book</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="books.length === 0" class="bg-white p-10 text-center text-gray-500 rounded-lg shadow">
                    No books found in the library.
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <div v-for="book in books" :key="book.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col hover:shadow-md transition">
                        <div class="p-6 flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <span v-if="book.is_available" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Available
                                </span>
                                <span v-else class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Rented
                                </span>
                            </div>
                            <Link :href="route('books.show', book.id)" class="block">
                                <h3 class="text-lg font-bold text-gray-900 hover:text-blue-600 truncate">{{ book.title }}</h3>
                            </Link>
                            <p class="text-sm text-gray-600 italic mb-4">{{ book.author }}</p>
                        </div>

                        <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                            <Link :href="route('books.show', book.id)" class="text-sm text-blue-500 hover:underline">
                                View History
                            </Link>

                            <PrimaryButton
                                v-if="book.is_available"
                                @click="rentBook(book.id)"
                                class="text-xs"
                            >
                                Rent Now
                            </PrimaryButton>
                            <DangerButton
                                v-else
                                @click="returnBook(book.id)"
                                class="text-xs"
                            >
                                Return
                            </DangerButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
