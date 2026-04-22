import { mount } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import Login from '@/Pages/Auth/Login.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { render: () => null },
    Link: {
        props: ['href'],
        template: '<a :href="href"><slot /></a>',
    },
    useForm: () => ({
        email: '',
        password: '',
        remember: false,
        processing: false,
        errors: {},
        post: vi.fn(),
        reset: vi.fn(),
    }),
}));

describe('Login.vue', () => {
    const baseProps = {
        canResetPassword: true,
        status: null,
    };

    it('zobrazi odkaz na registraci, kdyz je povoleny', () => {
        const routeMock = (name) => {
            if (name === 'register') return '/register';
            if (name === 'password.request') return '/forgot-password';
            if (name === 'login') return '/login';
            return '/';
        };

        const wrapper = mount(Login, {
            props: {
                ...baseProps,
                canRegister: true,
            },
            global: {
                stubs: {
                    GuestLayout: { template: '<div><slot /></div>' },
                    Checkbox: true,
                    InputError: true,
                    InputLabel: true,
                    PrimaryButton: { template: '<button><slot /></button>' },
                    TextInput: true,
                },
                mocks: {
                    route: routeMock,
                },
            },
        });

        const registerLink = wrapper.find('a[href="/register"]');

        expect(registerLink.exists()).toBe(true);
        expect(registerLink.text()).toContain('Create account');
    });

    it('skryje odkaz na registraci, kdyz neni povoleny', () => {
        const routeMock = (name) => {
            if (name === 'password.request') return '/forgot-password';
            if (name === 'login') return '/login';
            return '/';
        };

        const wrapper = mount(Login, {
            props: {
                ...baseProps,
                canRegister: false,
            },
            global: {
                stubs: {
                    GuestLayout: { template: '<div><slot /></div>' },
                    Checkbox: true,
                    InputError: true,
                    InputLabel: true,
                    PrimaryButton: { template: '<button><slot /></button>' },
                    TextInput: true,
                },
                mocks: {
                    route: routeMock,
                },
            },
        });

        expect(wrapper.text()).not.toContain('Create account');
        expect(wrapper.find('a[href="/register"]').exists()).toBe(false);
    });
});
