<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    books: Object,
    filters: Object,
    canCreate: Boolean,
});

const search_title = ref(props.filters.search_title || '');
const search_author = ref(props.filters.search_author || '');
const search_isbn = ref(props.filters.search_isbn || '');
const search_status = ref(props.filters.search_status || '');
const per_page = ref(props.filters.per_page || 10);
const loading = ref(false);

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

router.on('start', () => (loading.value = true));
router.on('finish', () => (loading.value = false));

watch([search_title, search_author, search_isbn, search_status, per_page], debounce(() => {
    router.get(route('books.index'), {
        search_title: search_title.value,
        search_author: search_author.value,
        search_isbn: search_isbn.value,
        search_status: search_status.value,
        per_page: per_page.value
    }, {
        preserveState: true,
        replace: true
    });
}, 300));
</script>

<template>
    <Head title="Books" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Book Management</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="canCreate" class="mb-4 flex justify-end">
                            <Link :href="route('books.create')">
                                <PrimaryButton>Add New Book</PrimaryButton>
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBN</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2">
                                            <TextInput
                                                v-model="search_title"
                                                placeholder="Filter title..."
                                                class="w-full text-xs"
                                            />
                                        </th>
                                        <th class="px-4 py-2">
                                            <TextInput
                                                v-model="search_author"
                                                placeholder="Filter author..."
                                                class="w-full text-xs"
                                            />
                                        </th>
                                        <th class="px-4 py-2">
                                            <TextInput
                                                v-model="search_isbn"
                                                placeholder="Filter ISBN..."
                                                class="w-full text-xs"
                                            />
                                        </th>
                                        <th class="px-4 py-2">
                                            <select
                                                v-model="search_status"
                                                class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1"
                                            >
                                                <option value="">All Statuses</option>
                                                <option value="available">Available</option>
                                                <option value="rented">Rented</option>
                                            </select>
                                        </th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 relative">
                                    <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                    </div>
                                    <tr v-for="book in books.data" :key="book.id" class="hover:bg-gray-50 transition" :class="{'opacity-50': loading}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="route('books.show', book.id)" class="text-sm font-medium text-blue-600 hover:underline">
                                                {{ book.title }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ book.author }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">{{ book.isbn }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span v-if="book.is_available" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Available
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Rented
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <PrimaryButton
                                                v-if="book.is_available"
                                                @click="rentBook(book.id)"
                                                class="text-xs"
                                            >
                                                Rent
                                            </PrimaryButton>
                                            <DangerButton
                                                v-else
                                                @click="returnBook(book.id)"
                                                class="text-xs"
                                            >
                                                Return
                                            </DangerButton>
                                        </td>
                                    </tr>
                                    <tr v-if="books.data.length === 0">
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                                            No books found matching your criteria.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-700">
                                Showing {{ books.from }} to {{ books.to }} of {{ books.total }} entries
                            </div>

                            <div class="flex items-center space-x-4">
                                <Pagination :links="books.links" />

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
