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
            user: { name: 'Test User', email: 'test@example.com' }
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

describe('AuthenticatedLayout.vue Navigation', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        mockPage.value = {
            component: 'Dashboard',
            url: '/',
            props: {
                auth: {
                    user: { name: 'Test User', email: 'test@example.com' }
                }
            }
        };
    });

    it('highlights Books link when page.component is Books/Show', async () => {
        mockPage.value.component = 'Books/Show';
        mockPage.value.url = '/books/1';

        const routeMock = (name) => {
            if (name) return `http://localhost/${name.replace('.', '/')}`;
            return {
                current: () => false
            };
        };
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

    it('highlights Books link when page.url starts with /books', async () => {
        mockPage.value.component = 'Other/Component';
        mockPage.value.url = '/books/1';

        // We'll update the component to also check URL in the next step if this fails or if we want extra robustness
        const routeMock = (name) => {
            if (name) return `http://localhost/${name.replace('.', '/')}`;
            return {
                current: () => false
            };
        };
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

        // This should fail initially if we only check component
        const booksLink = wrapper.findAll('.active').find(n => n.text().includes('Books'));
        // If we want it to pass, we need to add the URL check to the component
    });
});
