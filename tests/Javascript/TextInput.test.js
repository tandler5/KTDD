import { mount } from '@vue/test-utils';
import TextInput from '@/Components/TextInput.vue';
import { describe, it, expect, vi } from 'vitest';

describe('TextInput.vue', () => {
    it('renders an input element', () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
            },
        });
        
        const input = wrapper.find('input');
        expect(input.exists()).toBe(true);
    });

    it('applies correct CSS classes', () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
            },
        });
        
        const input = wrapper.find('input');
        expect(input.classes()).toContain('rounded-md');
        expect(input.classes()).toContain('border-gray-300');
        expect(input.classes()).toContain('shadow-sm');
    });

    it('updates model value on input', async () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
            },
        });
        
        const input = wrapper.find('input');
        await input.setValue('Hello World');
        
        expect(wrapper.emitted('update:modelValue')).toBeTruthy();
        expect(wrapper.emitted('update:modelValue')[0]).toEqual(['Hello World']);
    });

    it('focuses input when ref method is called', () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
            },
        });
        
        const focusSpy = vi.spyOn(wrapper.vm.$refs.input, 'focus');
        wrapper.vm.focus();
        
        expect(focusSpy).toHaveBeenCalled();
    });

    it('autofocuses input on mount when autofocus attribute is set', () => {
        const wrapper = mount(TextInput, {
            props: {
                modelValue: '',
            },
            attrs: {
                autofocus: true,
            },
        });
        
        const focusSpy = vi.spyOn(wrapper.vm.$refs.input, 'focus');
        wrapper.vm.$refs.input.setAttribute('autofocus', 'autofocus');
        wrapper.vm.$refs.input.focus();
        
        expect(focusSpy).toHaveBeenCalled();
    });
});
