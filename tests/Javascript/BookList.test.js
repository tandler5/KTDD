import { mount } from '@vue/test-utils';
import BookList from '@/Pages/Books/Index.vue';
import { describe, it, expect, vi } from 'vitest';

// Mock Inertia Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual('@inertiajs/vue3');
    return {
        ...actual,
        Head: { render: () => null },
        useForm: () => ({
            book_id: null,
            post: vi.fn(),
        }),
    };
});

// Mock Ziggy route helper
global.route = vi.fn();

// Stub AuthenticatedLayout to avoid complexity
const AuthenticatedLayoutStub = {
    template: '<div><slot name="header"></slot><slot></slot></div>',
};

describe('BookList.vue', () => {
    it('renders a list of books', () => {
        const books = [
            { id: 1, title: 'Book 1', author: 'Author 1', is_available: true },
            { id: 2, title: 'Book 2', author: 'Author 2', is_available: false },
        ];
        const wrapper = mount(BookList, {
            props: { books },
            global: {
                stubs: {
                    AuthenticatedLayout: AuthenticatedLayoutStub,
                }
            }
        });

        expect(wrapper.text()).toContain('Book 1');
        expect(wrapper.text()).toContain('Book 2');
    });

    it('shows rent button only if book is available', () => {
        const books = [
            { id: 1, title: 'Available Book', author: 'Author', is_available: true },
            { id: 2, title: 'Rented Book', author: 'Author', is_available: false },
        ];
        const wrapper = mount(BookList, {
            props: { books },
            global: {
                stubs: {
                    AuthenticatedLayout: AuthenticatedLayoutStub,
                }
            }
        });

        const buttons = wrapper.findAll('button');
        const rentButtons = buttons.filter(b => b.text().includes('Rent'));

        expect(rentButtons.length).toBe(1);
        expect(wrapper.text()).toContain('Already Rented');
    });
});
