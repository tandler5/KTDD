import { mount } from '@vue/test-utils';
import UserIndex from '@/Pages/Users/Index.vue';
import { describe, it, expect, vi } from 'vitest';
import { ref } from 'vue';

const mockPage = ref({
    props: {
        auth: {
            user: { name: 'Admin', role: 'administrator' }
        }
    }
});

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        Head: { render: () => null },
        Link: { template: '<a><slot /></a>' },
        usePage: () => mockPage.value,
        router: {
            get: vi.fn(),
            on: vi.fn(),
        },
    };
});

global.route = vi.fn(() => '');

describe('UserIndex.vue', () => {
    it('shows Role column and displays roles correctly', () => {
        mockPage.value.props.auth.user.role = 'administrator';

        const users = {
            data: [
                { id: 1, name: 'Admin', email: 'admin@example.com', role: 'administrator', active_rentals_count: 0 },
                { id: 2, name: 'Customer', email: 'customer@example.com', role: 'customer', active_rentals_count: 2 }
            ],
            links: [],
            from: 1,
            to: 2,
            total: 2
        };
        const filters = {};

        const wrapper = mount(UserIndex, {
            props: { users, filters },
            global: {
                stubs: {
                    AuthenticatedLayout: {
                        template: '<div><slot name="header"></slot><slot></slot></div>',
                    },
                    Pagination: true,
                    TextInput: true,
                },
                mocks: {
                    route: vi.fn(() => ''),
                }
            }
        });

        const headers = wrapper.findAll('th').map(h => h.text());
        expect(headers).toContain('Role');

        const roles = wrapper.findAll('tbody tr td span').map(s => s.text());
        expect(roles).toContain('administrator');
        expect(roles).toContain('customer');
    });
});
