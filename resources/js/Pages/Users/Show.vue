<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    user: Object,
    rentals: Object,
    filters: Object,
});

const search_book = ref(props.filters.search_book || '');
const per_page = ref(props.filters.per_page || 10);
const loading = ref(false);

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

router.on('start', () => (loading.value = true));
router.on('finish', () => (loading.value = false));

watch([search_book, per_page], debounce(() => {
    router.get(route('users.show', props.user.id), {
        search_book: search_book.value,
        per_page: per_page.value
    }, {
        preserveState: true,
        replace: true
    });
}, 300));
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
                                <Link :href="route('books.show', rental.book_id || 1)" class="font-bold text-blue-800 text-sm hover:underline">
                                    {{ rental.book_title }}
                                </Link>
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

                        <div class="overflow-x-auto relative">
                            <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            </div>

                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rented</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Returned</th>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-1">
                                            <TextInput
                                                v-model="search_book"
                                                placeholder="Filter book..."
                                                class="w-full text-xs"
                                            />
                                        </th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="rental in rentals.data" :key="rental.id" class="hover:bg-gray-50 transition" :class="{'opacity-50': loading}">
                                        <td class="px-4 py-3 text-sm font-medium">
                                            <Link :href="route('books.show', rental.book_id)" class="text-blue-600 hover:underline font-medium">
                                                {{ rental.book_title }}
                                            </Link>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(rental.rented_at) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span v-if="rental.returned_at" class="text-gray-500">{{ formatDate(rental.returned_at) }}</span>
                                            <span v-else class="text-blue-500 font-bold italic">Active</span>
                                        </td>
                                    </tr>
                                    <tr v-if="rentals.data.length === 0">
                                        <td colspan="3" class="px-4 py-10 text-center text-gray-400 text-sm">No history found matching your criteria.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <Pagination :links="rentals.links" />

                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500 uppercase font-semibold">Per page</span>
                                <select
                                    v-model="per_page"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs py-1"
                                >
                                    <option :value="5">5</option>
                                    <option :value="10">10</option>
                                    <option :value="25">25</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
