import { mount } from '@vue/test-utils';
import Modal from '@/Components/Modal.vue';
import { describe, it, expect, vi, beforeEach } from 'vitest';

describe('Modal.vue', () => {
    beforeEach(() => {
        document.body.style.overflow = '';
    });

    it('renders a dialog element', () => {
        const wrapper = mount(Modal);
        
        expect(wrapper.find('dialog').exists()).toBe(true);
    });

    it('does not display modal by default', () => {
        const wrapper = mount(Modal);
        
        expect(wrapper.vm.showSlot).toBe(false);
    });

    it('shows modal when show prop is true', async () => {
        const wrapper = mount(Modal, {
            props: { show: true },
        });
        
        await wrapper.vm.$nextTick();
        
        expect(wrapper.vm.showSlot).toBe(true);
    });

    it('emits close event when close method is called and closeable is true', async () => {
        const wrapper = mount(Modal, {
            props: { show: true, closeable: true },
        });
        
        wrapper.vm.close();
        
        expect(wrapper.emitted('close')).toBeTruthy();
    });

    it('does not emit close when closeable is false', () => {
        const wrapper = mount(Modal, {
            props: { show: true, closeable: false },
        });
        
        wrapper.vm.close();
        
        expect(wrapper.emitted('close')).toBeFalsy();
    });

    it('applies correct max-width class based on maxWidth prop', () => {
        const wrapper = mount(Modal, {
            props: { maxWidth: 'xl' },
        });
        
        expect(wrapper.vm.maxWidthClass).toBe('sm:max-w-xl');
    });

    it('closes on Escape key press when closeable', () => {
        const wrapper = mount(Modal, {
            props: { show: true, closeable: true },
        });
        
        const event = new KeyboardEvent('keydown', { key: 'Escape' });
        wrapper.vm.closeOnEscape(event);
        
        expect(wrapper.emitted('close')).toBeTruthy();
    });
});
