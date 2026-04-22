import { mount } from '@vue/test-utils';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { describe, it, expect, vi, beforeEach } from 'vitest';

vi.mock('@inertiajs/vue3', async () => {
    return {
        Head: { render: () => null },
        Link: { template: '<a><slot /></a>' },
        useForm: () => ({}),
        usePage: () => ({
            props: {
                auth: {
                    user: {
                        name: 'Test User',
                        email: 'test@example.com'
                    }
                }
            }
        }),
    };
});

describe('AuthenticatedLayout.vue Navigation', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('highlights Books link when $page.component starts with Books/', () => {
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
                    $page: {
                        component: 'Books/Show',
                        props: {
                            auth: {
                                user: {
                                    name: 'Test User',
                                    email: 'test@example.com'
                                }
                            }
                        }
                    }
                }
            }
        });

        const activeLinks = wrapper.findAll('.active');
        const booksLink = activeLinks.find(n => n.text().includes('Books'));

        expect(booksLink).toBeDefined();
        expect(booksLink.text()).toContain('Books');
    });
});
