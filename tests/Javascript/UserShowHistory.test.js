import { mount } from '@vue/test-utils';
import UserShow from '@/Pages/Users/Show.vue';
import { describe, it, expect, vi } from 'vitest';

const mockPage = {
    props: {
        auth: {
            user: { id: 999, role: 'customer' }
        }
    }
};

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        Head: { render: () => null },
        Link: { template: '<a><slot /></a>' },
        usePage: () => mockPage,
        router: {
            get: vi.fn(),
            on: vi.fn(),
        },
    };
});

global.route = vi.fn(() => '');

describe('UserShow.vue Rental History', () => {
    it('shows Status as a separate column in history table', () => {
        const user = { id: 1, name: 'John Doe', email: 'john@example.com', role: 'customer', active_rentals: [] };
        const rentals = {
            data: [
                { id: 1, book_id: 1, book_title: 'Book 1', rented_at: '2023-01-01', returned_at: '2023-01-10' },
                { id: 2, book_id: 2, book_title: 'Book 2', rented_at: '2023-01-05', returned_at: null }
            ],
            links: []
        };
        const filters = {};

        const wrapper = mount(UserShow, {
            props: { user, rentals, filters },
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
                    $page: {
                        props: {
                            auth: {
                                user: { role: 'customer' }
                            }
                        }
                    }
                }
            }
        });

        const headers = wrapper.findAll('th').map(h => h.text());
        expect(headers).toContain('Status');

        const rows = wrapper.findAll('tbody tr');
        expect(rows.length).toBe(2);

        // Check first row (Returned)
        expect(rows[0].text()).toContain('Returned');

        // Check second row (Active)
        expect(rows[1].text()).toContain('Active');
    });

    it('shows role management for administrators', () => {
        mockPage.props.auth.user = { id: 1000, role: 'administrator' };

        const user = { id: 1, name: 'John Doe', email: 'john@example.com', role: 'customer', active_rentals: [] };
        const rentals = { data: [], links: [] };
        const filters = {};

        const wrapper = mount(UserShow, {
            props: { user, rentals, filters },
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

        expect(wrapper.find('[data-testid="role-select"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="role-select"]').element.value).toBe('customer');
    });

    it('hides role management for customers', () => {
        mockPage.props.auth.user = { id: 1001, role: 'customer' };

        const user = { id: 1, name: 'John Doe', email: 'john@example.com', role: 'customer', active_rentals: [] };
        const rentals = { data: [], links: [] };
        const filters = {};

        const wrapper = mount(UserShow, {
            props: { user, rentals, filters },
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

        expect(wrapper.find('[data-testid="role-select"]').exists()).toBe(false);
        expect(wrapper.text()).toContain('customer');
    });

    it('disables role select when admin views own profile', () => {
        mockPage.props.auth.user = { id: 1, role: 'administrator' };

        const user = { id: 1, name: 'Admin', email: 'admin@example.com', role: 'administrator', active_rentals: [] };
        const rentals = { data: [], links: [] };
        const filters = {};

        const wrapper = mount(UserShow, {
            props: { user, rentals, filters },
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

        const roleSelect = wrapper.find('[data-testid="role-select"]');
        expect(roleSelect.exists()).toBe(true);
        expect(roleSelect.attributes('disabled')).toBeDefined();
        expect(wrapper.text()).toContain('You cannot change your own role.');
    });
});
