import { mount } from '@vue/test-utils';
import DropdownLink from '@/Components/DropdownLink.vue';
import { describe, it, expect } from 'vitest';

describe('DropdownLink.vue', () => {
    it('renders a link element', () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/logout' },
            slots: { default: 'Logout' },
        });
        
        expect(wrapper.find('a').exists()).toBe(true);
        expect(wrapper.find('a').attributes('href')).toBe('/logout');
    });

    it('displays slot content', () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/profile' },
            slots: { default: 'Profile' },
        });
        
        expect(wrapper.text()).toBe('Profile');
    });

    it('applies correct CSS classes for styling', () => {
        const wrapper = mount(DropdownLink, {
            props: { href: '/settings' },
        });
        
        const link = wrapper.find('a');
        expect(link.classes()).toContain('block');
        expect(link.classes()).toContain('w-full');
        expect(link.classes()).toContain('px-4');
        expect(link.classes()).toContain('py-2');
        expect(link.classes()).toContain('text-gray-700');
    });
});
