import { mount } from '@vue/test-utils';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { describe, it, expect } from 'vitest';

describe('PrimaryButton.vue', () => {
    it('renders a button element', () => {
        const wrapper = mount(PrimaryButton, {
            slots: {
                default: 'Submit',
            },
        });
        
        const button = wrapper.find('button');
        expect(button.exists()).toBe(true);
    });

    it('displays slot content', () => {
        const wrapper = mount(PrimaryButton, {
            slots: {
                default: 'Click Me',
            },
        });
        
        expect(wrapper.text()).toBe('Click Me');
    });

    it('applies correct primary button CSS classes', () => {
        const wrapper = mount(PrimaryButton);
        
        const button = wrapper.find('button');
        expect(button.classes()).toContain('bg-gray-800');
        expect(button.classes()).toContain('hover:bg-gray-700');
        expect(button.classes()).toContain('text-white');
    });

    it('has focus and active state styling', () => {
        const wrapper = mount(PrimaryButton);
        
        const button = wrapper.find('button');
        expect(button.classes()).toContain('focus:ring-2');
        expect(button.classes()).toContain('focus:ring-indigo-500');
        expect(button.classes()).toContain('active:bg-gray-900');
    });
});
