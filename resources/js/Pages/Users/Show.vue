<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    user: Object
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};
</script>

<template>
    <Head :title="user.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    User Detail: {{ user.name }}
                </h2>
                <Link :href="route('users.index')" class="text-blue-500 hover:underline">
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- User Info -->
                    <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-fit">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Profile</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs uppercase text-gray-400 font-bold block">Name</span>
                                <p class="font-semibold">{{ user.name }}</p>
                            </div>
                            <div>
                                <span class="text-xs uppercase text-gray-400 font-bold block">Email</span>
                                <p class="text-gray-600">{{ user.email }}</p>
                            </div>
                        </div>

                        <div v-if="user.active_rentals.length > 0" class="mt-8">
                            <h4 class="font-bold text-sm uppercase text-gray-500 mb-3">Currently Borrowed</h4>
                            <div v-for="rental in user.active_rentals" :key="rental.id" class="mb-3 p-3 bg-blue-50 rounded border border-blue-100">
                                <p class="font-bold text-blue-800 text-sm">{{ rental.book_title }}</p>
                                <div class="flex justify-between text-[10px] mt-1 text-blue-600">
                                    <span>Since: {{ formatDate(rental.rented_at) }}</span>
                                    <span class="font-bold">Due: {{ formatDate(rental.due_date) }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="mt-8 p-4 bg-gray-50 rounded text-center text-gray-400 text-sm italic">
                            No active rentals.
                        </div>
                    </div>

                    <!-- Rental History -->
                    <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Rental History</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rented</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Returned</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="history in user.history" :key="history.id" class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm font-medium">{{ history.book_title }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(history.rented_at) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span v-if="history.returned_at" class="text-gray-500">{{ formatDate(history.returned_at) }}</span>
                                            <span v-else class="text-blue-500 font-bold italic">Active</span>
                                        </td>
                                    </tr>
                                    <tr v-if="user.history.length === 0">
                                        <td colspan="3" class="px-4 py-10 text-center text-gray-400">This user has no history.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
