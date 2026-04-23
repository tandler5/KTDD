<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div v-if="links.length > 0">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" :key="`disabled-${key}`" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded bg-gray-50" v-html="link.label" />
                <Link v-else :key="`link-${key}`" :href="link.url" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-100 focus:border-indigo-500 focus:text-indigo-500 transition-colors" :class="{ 'bg-blue-600 text-white font-bold': link.active, 'bg-white text-gray-700': !link.active }" v-html="link.label" />
            </template>
        </div>
    </div>
</template>
