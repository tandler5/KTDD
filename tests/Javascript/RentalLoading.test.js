import { mount } from '@vue/test-utils';
import RentalIndex from '@/Pages/Rentals/Index.vue';
import { describe, it, expect, vi } from 'vitest';
import { ref } from 'vue';

const mockPage = ref({
    props: {
        auth: {
            user: { name: 'Admin', role: 'administrator' }
        }
    }
});

// Mock Inertia Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        Head: { render: () => null },
        usePage: () => mockPage.value,
        router: {
            get: vi.fn(),
            on: vi.fn(),
        },
    };
});

// Mock Ziggy
global.route = vi.fn();

describe('RentalIndex.vue Loading State', () => {
    it('does not show spinner by default', () => {
        mockPage.value.props.auth.user.role = 'administrator';

        const rentals = { data: [], links: [] };
        const filters = {};

        const wrapper = mount(RentalIndex, {
            props: { rentals, filters },
            global: {
                stubs: {
                    AuthenticatedLayout: {
                        template: '<div><slot name="header"></slot><slot></slot></div>',
                    },
                    Pagination: true,
                    TextInput: true,
                },
                mocks: {
                    route: () => '',
                }
            }
        });

        expect(wrapper.find('.animate-spin').exists()).toBe(false);
    });
});
