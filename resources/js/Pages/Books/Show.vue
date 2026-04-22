<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    book: Object,
    rentals: Object,
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <Head :title="book.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Book Detail: {{ book.title }}
                </h2>
                <Link :href="route('books.index')" class="text-blue-500 hover:underline">
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Info Card -->
                    <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4">Information</h3>
                        <div class="space-y-2">
                            <p><span class="font-semibold">Author:</span> {{ book.author }}</p>
                            <p><span class="font-semibold">ISBN:</span> {{ book.isbn }}</p>
                            <p>
                                <span class="font-semibold">Status:</span>
                                <span v-if="book.is_available" class="ml-2 text-green-600 font-bold">Available</span>
                                <span v-else class="ml-2 text-red-600 font-bold">Rented</span>
                            </p>
                        </div>

                        <div v-if="book.current_rental" class="mt-6 p-4 bg-gray-50 rounded">
                            <h4 class="font-bold text-sm uppercase text-gray-500 mb-2">Current Borrower</h4>
                            <p class="font-semibold">{{ book.current_rental.user_name }}</p>
                            <p class="text-sm text-gray-600">Since: {{ formatDate(book.current_rental.rented_at) }}</p>
                            <p class="text-sm text-gray-600 font-semibold mt-1">Due: {{ formatDate(book.current_rental.due_date) }}</p>
                        </div>
                    </div>

                    <!-- History Card -->
                    <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4">Rental History</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rented</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Returned</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="rental in rentals.data" :key="rental.id" class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm">
                                            <Link :href="route('users.show', rental.user_id)" class="text-blue-600 hover:underline font-medium">
                                                {{ rental.user_name }}
                                            </Link>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(rental.rented_at) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span v-if="rental.returned_at" class="text-gray-500">{{ formatDate(rental.returned_at) }}</span>
                                            <span v-else class="text-blue-500 font-bold italic">Active</span>
                                        </td>
                                    </tr>
                                    <tr v-if="rentals.data.length === 0">
                                        <td colspan="3" class="px-4 py-10 text-center text-gray-400">No history yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination :links="rentals.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
