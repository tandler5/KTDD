<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    isAdministrator: Boolean,
    statistics: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Admin Dashboard -->
                <div v-if="isAdministrator" class="space-y-6">
                    <h3 class="text-lg font-bold text-gray-800">Administrator Summary</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Total Users -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Users</div>
                            <div class="text-3xl font-bold text-gray-800">{{ statistics.total_users }}</div>
                        </div>

                        <!-- Total Books -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Books</div>
                            <div class="text-3xl font-bold text-gray-800">{{ statistics.total_books }}</div>
                        </div>

                        <!-- Active Rentals -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm font-semibold text-gray-500 uppercase mb-2">Active Rentals</div>
                            <div class="text-3xl font-bold text-blue-600">{{ statistics.total_active_rentals }}</div>
                        </div>

                        <!-- Most Rented Book -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm font-semibold text-gray-500 uppercase mb-2">Most Rented</div>
                            <div v-if="statistics.most_rented_book">
                                <p class="font-bold text-gray-800">{{ statistics.most_rented_book.title }}</p>
                                <p class="text-xs text-gray-600">{{ statistics.most_rented_book.rental_count }} rentals</p>
                            </div>
                            <div v-else class="text-gray-400">No data</div>
                        </div>
                    </div>
                </div>

                <!-- Customer Dashboard -->
                <div v-else class="space-y-6">
                    <h3 class="text-lg font-bold text-gray-800">Your Activity</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Active Rentals -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm font-semibold text-gray-500 uppercase mb-2">Active Rentals</div>
                            <div class="text-3xl font-bold text-blue-600">{{ statistics.active_rentals_count }}</div>
                        </div>

                        <!-- Total Rentals -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Rentals</div>
                            <div class="text-3xl font-bold text-gray-800">{{ statistics.total_rentals_count }}</div>
                        </div>

                        <!-- Most Rented Book -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-sm font-semibold text-gray-500 uppercase mb-2">Your Favorite</div>
                            <div v-if="statistics.most_rented_book">
                                <p class="font-bold text-gray-800">{{ statistics.most_rented_book.title }}</p>
                                <p class="text-xs text-gray-600">{{ statistics.most_rented_book.rental_count }} times</p>
                            </div>
                            <div v-else class="text-gray-400">No rentals yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
