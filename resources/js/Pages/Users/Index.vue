<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    users: Object,
    filters: Object,
});

const search_name = ref(props.filters.search_name || '');
const search_email = ref(props.filters.search_email || '');
const per_page = ref(props.filters.per_page || 10);
const loading = ref(false);

router.on('start', () => (loading.value = true));
router.on('finish', () => (loading.value = false));

watch([search_name, search_email, per_page], debounce(() => {
    router.get(route('users.index'), {
        search_name: search_name.value,
        search_email: search_email.value,
        per_page: per_page.value
    }, {
        preserveState: true,
        replace: true
    });
}, 300));
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Management</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Rentals</th>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2">
                                            <TextInput
                                                v-model="search_name"
                                                placeholder="Filter name..."
                                                class="w-full text-xs"
                                            />
                                        </th>
                                        <th class="px-4 py-2">
                                            <TextInput
                                                v-model="search_email"
                                                placeholder="Filter email..."
                                                class="w-full text-xs"
                                            />
                                        </th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 relative">
                                    <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                    </div>
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition" :class="{'opacity-50': loading}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="route('users.show', user.id)" class="text-sm font-medium text-blue-600 hover:underline">
                                                {{ user.name }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded text-xs font-bold" :class="user.active_rentals_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-500'">
                                                {{ user.active_rentals_count }} active
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-400 text-sm">
                                            No users found matching your criteria.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-700">
                                Showing {{ users.from }} to {{ users.to }} of {{ users.total }} entries
                            </div>

                            <div class="flex items-center space-x-4">
                                <Pagination :links="users.links" />

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
