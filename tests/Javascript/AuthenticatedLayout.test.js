import { mount } from '@vue/test-utils';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { ref } from 'vue';

// Setup default mock values
const mockPage = ref({
    component: 'Dashboard',
    url: '/',
    props: {
        auth: {
            user: { name: 'Test User', email: 'test@example.com', role: 'customer' }
        }
    }
});

vi.mock('@inertiajs/vue3', async () => {
    return {
        Head: { render: () => null },
        Link: { template: '<a><slot /></a>' },
        useForm: () => ({}),
        usePage: () => mockPage.value,
    };
});

const slotStub = {
    template: '<div><slot /></div>',
};

describe('AuthenticatedLayout.vue Navigation', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        mockPage.value = {
            component: 'Dashboard',
            url: '/',
            props: {
                auth: {
                    user: { name: 'Test User', email: 'test@example.com', role: 'customer' }
                }
            }
        };
    });

    it('highlights Books link when page.component is Books/Show', async () => {
        mockPage.value.component = 'Books/Show';
        mockPage.value.url = '/books/1';

        const routeMock = vi.fn((name) => {
            if (!name) {
                return {
                    current: (routeName) => false
                };
            }
            return `http://localhost/${name.replace('.', '/')}`;
        });
        global.route = routeMock;

        const wrapper = mount(AuthenticatedLayout, {
            global: {
                stubs: {
                    ApplicationLogo: true,
                    Dropdown: true,
                    DropdownLink: true,
                    Toast: true,
                    ResponsiveNavLink: true,
                    NavLink: {
                        props: ['active'],
                        template: '<div :class="{ active: active }"><slot /></div>'
                    }
                },
                mocks: {
                    route: routeMock,
                    $page: mockPage.value
                }
            }
        });

        const booksLink = wrapper.findAll('.active').find(n => n.text().includes('Books'));
        expect(booksLink).toBeDefined();
    });

    it('displays the user role in the navigation', () => {
        mockPage.value.props.auth.user.role = 'administrator';

        const routeMock = vi.fn((name) => {
            if (!name) {
                return { current: (routeName) => false };
            }
            return `http://localhost/${name.replace('.', '/')}`;
        });
        global.route = routeMock;

        const wrapper = mount(AuthenticatedLayout, {
            global: {
                stubs: {
                    ApplicationLogo: true,
                    Dropdown: true,
                    DropdownLink: true,
                    Toast: true,
                    ResponsiveNavLink: true,
                    NavLink: true
                },
                mocks: {
                    route: routeMock,
                    $page: mockPage.value
                }
            }
        });

        expect(wrapper.text()).toContain('administrator');
    });

    it('hides Users and Rentals links for customer role', () => {
        const routeMock = vi.fn((name) => {
            if (!name) {
                return { current: () => false };
            }
            return `http://localhost/${name.replace('.', '/')}`;
        });
        global.route = routeMock;

        const wrapper = mount(AuthenticatedLayout, {
            global: {
                stubs: {
                    ApplicationLogo: true,
                    Dropdown: slotStub,
                    DropdownLink: slotStub,
                    Toast: true,
                    ResponsiveNavLink: slotStub,
                    NavLink: slotStub,
                },
                mocks: {
                    route: routeMock,
                    $page: mockPage.value,
                },
            },
        });

        expect(wrapper.text()).toContain('Books');
        expect(wrapper.text()).not.toContain('Users');
        expect(wrapper.text()).not.toContain('Rentals');
    });

    it('shows Users and Rentals links for administrator role', () => {
        mockPage.value.props.auth.user.role = 'administrator';

        const routeMock = vi.fn((name) => {
            if (!name) {
                return { current: () => false };
            }
            return `http://localhost/${name.replace('.', '/')}`;
        });
        global.route = routeMock;

        const wrapper = mount(AuthenticatedLayout, {
            global: {
                stubs: {
                    ApplicationLogo: true,
                    Dropdown: slotStub,
                    DropdownLink: slotStub,
                    Toast: true,
                    ResponsiveNavLink: slotStub,
                    NavLink: slotStub,
                },
                mocks: {
                    route: routeMock,
                    $page: mockPage.value,
                },
            },
        });

        expect(wrapper.text()).toContain('Users');
        expect(wrapper.text()).toContain('Rentals');
    });
});
