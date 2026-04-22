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
const search_rented = ref(props.filters.search_rented || '');
const search_due = ref(props.filters.search_due || '');
const search_status = ref(props.filters.search_status || '');
const per_page = ref(props.filters.per_page || 10);

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

watch([search_book, search_user, search_rented, search_due, search_status, per_page], debounce(() => {
    router.get(route('rentals.index'), {
        search_book: search_book.value,
        search_user: search_user.value,
        search_rented: search_rented.value,
        search_due: search_due.value,
        search_status: search_status.value,
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
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rented</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
                                        <th class="px-4 py-2">
                                            <input
                                                type="date"
                                                v-model="search_rented"
                                                class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-2 py-1"
                                            />
                                        </th>
                                        <th class="px-4 py-2">
                                            <input
                                                type="date"
                                                v-model="search_due"
                                                class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-2 py-1"
                                            />
                                        </th>
                                        <th class="px-4 py-2">
                                            <select
                                                v-model="search_status"
                                                class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1"
                                            >
                                                <option value="">All Statuses</option>
                                                <option value="active">Active</option>
                                                <option value="returned">Returned</option>
                                                <option value="overdue">Overdue</option>
                                            </select>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="rental in rentals.data" :key="rental.id" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-sm">{{ rental.book_title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ rental.user_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(rental.rented_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" :class="{'text-red-600 font-bold': rental.is_overdue}">
                                            {{ formatDate(rental.due_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="rental.returned_at" class="text-green-600 text-xs font-semibold">
                                                Returned ({{ formatDate(rental.returned_at) }})
                                            </span>
                                            <span v-else-if="rental.is_overdue" class="text-red-600 text-xs font-bold animate-pulse">
                                                OVERDUE
                                            </span>
                                            <span v-else class="text-blue-600 text-xs font-bold italic">
                                                Active
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="rentals.data.length === 0">
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                                            No rentals found matching your criteria.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
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
