<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    user: Object,
    rentals: Object,
    filters: Object,
});

const search_book = ref(props.filters.search_book || '');
const rented_from = ref(props.filters.rented_from || '');
const rented_to = ref(props.filters.rented_to || '');
const returned_from = ref(props.filters.returned_from || '');
const returned_to = ref(props.filters.returned_to || '');
const per_page = ref(props.filters.per_page || 10);
const loading = ref(false);

const roleForm = useForm({
    role: props.user.role,
});

const page = usePage();
const isCurrentUser = computed(() => props.user.id === page.props.auth.user.id);

const updateRole = () => {
    if (isCurrentUser.value) {
        return;
    }

    roleForm.patch(route('users.update-role', props.user.id), {
        preserveScroll: true,
    });
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

router.on('start', () => (loading.value = true));
router.on('finish', () => (loading.value = false));

watch([search_book, rented_from, rented_to, returned_from, returned_to, per_page], debounce(() => {
    router.get(route('users.show', props.user.id), {
        search_book: search_book.value,
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
                            <div class="pt-2 border-t mt-4">
                                <span class="text-xs uppercase text-gray-400 font-bold block mb-1">Role Management</span>
                                <div v-if="page.props.auth.user.role === 'administrator'" class="flex items-center space-x-2">
                                    <select
                                        data-testid="role-select"
                                        v-model="roleForm.role"
                                        @change="updateRole"
                                        :disabled="roleForm.processing || isCurrentUser"
                                        class="text-sm border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1"
                                    >
                                        <option value="customer">Customer</option>
                                        <option value="administrator">Administrator</option>
                                    </select>
                                    <span v-if="roleForm.recentlySuccessful" class="text-xs text-green-600 font-bold">Saved!</span>
                                    <span v-if="isCurrentUser" class="text-xs text-gray-500">You cannot change your own role.</span>
                                </div>
                                <div v-else>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-700">
                                        {{ user.role }}
                                    </span>
                                </div>
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
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-1">
                                            <TextInput
                                                v-model="search_book"
                                                placeholder="Filter book..."
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
                                        <th class="px-4 py-1"></th>
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
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ rental.returned_at ? formatDate(rental.returned_at) : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span v-if="rental.returned_at" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Returned
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Active
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="rentals.data.length === 0">
                                        <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">No history found matching your criteria.</td>
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
