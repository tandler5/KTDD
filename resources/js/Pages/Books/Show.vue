<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    book: Object,
    rentals: Object,
    filters: Object,
    canViewRentalHistory: Boolean,
});

const search_user = ref(props.filters.search_user || '');
const rented_from = ref(props.filters.rented_from || '');
const rented_to = ref(props.filters.rented_to || '');
const returned_from = ref(props.filters.returned_from || '');
const returned_to = ref(props.filters.returned_to || '');
const per_page = ref(props.filters.per_page || 10);
const loading = ref(false);

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

router.on('start', () => (loading.value = true));
router.on('finish', () => (loading.value = false));

watch([search_user, rented_from, rented_to, returned_from, returned_to, per_page], debounce(() => {
    if (!props.canViewRentalHistory) {
        return;
    }

    router.get(route('books.show', props.book.id), {
        search_user: search_user.value,
        rented_from: rented_from.value,
        rented_to: rented_to.value,
        returned_from: returned_from.value,
        returned_to: returned_to.value,
        per_page: per_page.value
    }, {
        preserveState: true,
        replace: true
    });
}, 300));
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
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Information</h3>
                        <div class="space-y-2">
                            <p><span class="font-semibold text-gray-500 uppercase text-[10px]">Author:</span> {{ book.author }}</p>
                            <p><span class="font-semibold text-gray-500 uppercase text-[10px]">ISBN:</span> {{ book.isbn }}</p>
                            <p>
                                <span class="font-semibold text-gray-500 uppercase text-[10px]">Status:</span>
                                <span v-if="book.is_available" class="ml-2 text-green-600 font-bold">Available</span>
                                <span v-else class="ml-2 text-red-600 font-bold">Rented</span>
                            </p>
                        </div>

                        <div v-if="book.current_rental" class="mt-6 p-4 bg-gray-50 rounded">
                            <h4 class="font-bold text-sm uppercase text-gray-500 mb-2 border-b">Current Borrower</h4>
                            <Link
                                v-if="canViewRentalHistory"
                                :href="route('users.show', book.current_rental.user_id)"
                                class="font-semibold text-blue-600 hover:underline block mb-1"
                            >
                                {{ book.current_rental.user_name }}
                            </Link>
                            <p v-else class="font-semibold text-gray-700 mb-1">
                                {{ book.current_rental.user_name }}
                            </p>
                            <p class="text-[10px] text-gray-600">Since: {{ formatDate(book.current_rental.rented_at) }}</p>
                            <p class="text-[10px] text-gray-600 font-semibold">Due: {{ formatDate(book.current_rental.due_date) }}</p>
                        </div>
                    </div>

                    <!-- History Card -->
                    <div
                        v-if="canViewRentalHistory"
                        class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Rental History</h3>

                        <div class="overflow-x-auto relative">
                            <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            </div>

                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rented</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Returned</th>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-1">
                                            <TextInput
                                                v-model="search_user"
                                                placeholder="Filter user..."
                                                class="w-full text-xs"
                                            />
                                        </th>
                                        <th class="px-4 py-1">
                                            <div class="flex items-center space-x-1">
                                                <div class="flex-1">
                                                    <span class="text-[9px] uppercase text-gray-400 block">From</span>
                                                    <input type="date" v-model="rented_from" class="w-full text-[10px] border-gray-300 rounded shadow-sm p-1" />
                                                </div>
                                                <div class="flex-1">
                                                    <span class="text-[9px] uppercase text-gray-400 block">To</span>
                                                    <input type="date" v-model="rented_to" class="w-full text-[10px] border-gray-300 rounded shadow-sm p-1" />
                                                </div>
                                            </div>
                                        </th>
                                        <th class="px-4 py-1">
                                            <div class="flex items-center space-x-1">
                                                <div class="flex-1">
                                                    <span class="text-[9px] uppercase text-gray-400 block">From</span>
                                                    <input type="date" v-model="returned_from" class="w-full text-[10px] border-gray-300 rounded shadow-sm p-1" />
                                                </div>
                                                <div class="flex-1">
                                                    <span class="text-[9px] uppercase text-gray-400 block">To</span>
                                                    <input type="date" v-model="returned_to" class="w-full text-[10px] border-gray-300 rounded shadow-sm p-1" />
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="rental in rentals.data" :key="rental.id" class="hover:bg-gray-50 transition" :class="{'opacity-50': loading}">
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
