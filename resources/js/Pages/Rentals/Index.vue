<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    rentals: Object,
    filters: Object,
});

const search_book = ref(props.filters.search_book || '');
const search_user = ref(props.filters.search_user || '');
const per_page = ref(props.filters.per_page || 10);

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

watch([search_book, search_user, per_page], debounce(() => {
    router.get(route('rentals.index'), {
        search_book: search_book.value,
        search_user: search_user.value,
        per_page: per_page.value
    }, {
        preserveState: true,
        replace: true
    });
}, 300));
</script>

<template>
    <Head title="Rentals" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Rentals</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rented</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2">
                                        <TextInput
                                            v-model="search_book"
                                            placeholder="Filter book..."
                                            class="w-full text-xs"
                                        />
                                    </th>
                                    <th class="px-4 py-2">
                                        <TextInput
                                            v-model="search_user"
                                            placeholder="Filter user..."
                                            class="w-full text-xs"
                                        />
                                    </th>
                                    <th class="px-4 py-2"></th>
                                    <th class="px-4 py-2"></th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="rental in rentals.data" :key="rental.id">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ rental.book_title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ rental.user_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(rental.rented_at) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" :class="{'text-red-600 font-bold': rental.is_overdue}">
                                        {{ formatDate(rental.due_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="rental.returned_at" class="text-green-600 text-sm">Returned ({{ formatDate(rental.returned_at) }})</span>
                                        <span v-else-if="rental.is_overdue" class="text-red-600 text-sm font-bold animate-pulse">OVERDUE</span>
                                        <span v-else class="text-blue-600 text-sm font-bold italic">Active</span>
                                    </td>
                                </tr>
                                <tr v-if="rentals.data.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">No rentals found.</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ rentals.from }} to {{ rentals.to }} of {{ rentals.total }} entries
                            </div>

                            <div class="flex items-center space-x-4">
                                <Pagination :links="rentals.links" />

                                <div class="flex items-center space-x-2 border-l pl-4 border-gray-200">
                                    <span class="text-xs text-gray-500 uppercase font-semibold">Per page</span>
                                    <select
                                        v-model="per_page"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs py-1"
                                    >
                                        <option :value="5">5</option>
                                        <option :value="10">10</option>
                                        <option :value="25">25</option>
                                        <option :value="50">50</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
