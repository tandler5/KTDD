import { mount } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import BookShow from '@/Pages/Books/Show.vue';

vi.mock('@inertiajs/vue3', () => ({
    Head: { render: () => null },
    Link: {
        props: ['href'],
        template: '<a :href="href"><slot /></a>',
    },
    router: {
        on: vi.fn(),
        get: vi.fn(),
    },
}));

describe('BookShow.vue history visibility', () => {
    const baseProps = {
        book: {
            id: 1,
            title: 'Book 1',
            author: 'Author',
            isbn: '123',
            is_available: true,
            current_rental: null,
        },
        rentals: {
            data: [],
            links: [],
        },
        filters: {},
    };

    it('zobrazi historii jen pro admina', () => {
        const routeMock = (name) => {
            if (name === 'books.index') return '/books';
            if (name === 'books.show') return '/books/1';
            if (name === 'users.show') return '/users/1';
            return '/';
        };

        const wrapper = mount(BookShow, {
            props: {
                ...baseProps,
                canViewRentalHistory: true,
            },
            global: {
                stubs: {
                    AuthenticatedLayout: { template: '<div><slot name="header" /><slot /></div>' },
                    Pagination: true,
                    TextInput: true,
                },
                mocks: {
                    route: routeMock,
                },
            },
        });

        expect(wrapper.text()).toContain('Rental History');
    });

    it('skryje historii pro customer roli', () => {
        const routeMock = (name) => {
            if (name === 'books.index') return '/books';
            if (name === 'books.show') return '/books/1';
            return '/';
        };

        const wrapper = mount(BookShow, {
            props: {
                ...baseProps,
                canViewRentalHistory: false,
            },
            global: {
                stubs: {
                    AuthenticatedLayout: { template: '<div><slot name="header" /><slot /></div>' },
                    Pagination: true,
                    TextInput: true,
                },
                mocks: {
                    route: routeMock,
                },
            },
        });

        expect(wrapper.text()).not.toContain('Rental History');
    });
});

