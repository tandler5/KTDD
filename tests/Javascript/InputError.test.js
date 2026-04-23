import { mount } from '@vue/test-utils';
import InputError from '@/Components/InputError.vue';
import { describe, it, expect } from 'vitest';

describe('InputError.vue', () => {
    it('renders nothing when message is not provided', () => {
        const wrapper = mount(InputError);
        
        expect(wrapper.find('div').exists()).toBe(false);
    });

    it('displays error message when provided', () => {
        const wrapper = mount(InputError, {
            props: {
                message: 'This field is required',
            },
        });
        
        expect(wrapper.text()).toContain('This field is required');
    });

    it('applies correct CSS classes for error styling', () => {
        const wrapper = mount(InputError, {
            props: {
                message: 'Error message',
            },
        });
        
        const paragraph = wrapper.find('p');
        expect(paragraph.classes()).toContain('text-sm');
        expect(paragraph.classes()).toContain('text-red-600');
    });

    it('hides when message becomes empty', async () => {
        const wrapper = mount(InputError, {
            props: {
                message: 'Error',
            },
        });
        
        expect(wrapper.find('div').exists()).toBe(true);
        
        await wrapper.setProps({ message: '' });
        
        expect(wrapper.find('div').exists()).toBe(false);
    });
});
