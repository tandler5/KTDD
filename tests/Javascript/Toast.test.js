import { mount } from '@vue/test-utils';
import Toast from '@/Components/Toast.vue';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { ref } from 'vue';

// Mock Inertia
const mockPage = ref({
    props: {
        flash: {
            success: null,
            error: null,
        },
    },
});

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => mockPage.value,
}));

describe('Toast.vue', () => {
    beforeEach(() => {
        vi.clearAllTimers();
        vi.useFakeTimers();
        mockPage.value.props.flash = { success: null, error: null };
    });

    it('renders a transition component', () => {
        const wrapper = mount(Toast);
        
        expect(wrapper.findComponent({ name: 'Transition' }).exists()).toBe(true);
    });

    it('does not display toast by default', () => {
        const wrapper = mount(Toast);
        
        expect(wrapper.vm.visible).toBe(false);
    });

    it('displays success toast when show method is called', async () => {
        const wrapper = mount(Toast);
        
        wrapper.vm.show('Success message', 'success');
        await wrapper.vm.$nextTick();
        
        expect(wrapper.vm.visible).toBe(true);
        expect(wrapper.vm.message).toBe('Success message');
        expect(wrapper.vm.type).toBe('success');
    });

    it('displays error toast', async () => {
        const wrapper = mount(Toast);
        
        wrapper.vm.show('Error message', 'error');
        await wrapper.vm.$nextTick();
        
        expect(wrapper.vm.message).toBe('Error message');
        expect(wrapper.vm.type).toBe('error');
    });

    it('hides toast after 3 seconds', async () => {
        const wrapper = mount(Toast);
        
        wrapper.vm.show('Test message');
        expect(wrapper.vm.visible).toBe(true);
        
        vi.advanceTimersByTime(3000);
        await wrapper.vm.$nextTick();
        
        expect(wrapper.vm.visible).toBe(false);
    });

    it('displays success icon when type is success', async () => {
        const wrapper = mount(Toast);
        
        wrapper.vm.show('Success', 'success');
        await wrapper.vm.$nextTick();
        
        const svgs = wrapper.findAll('svg');
        expect(svgs.length).toBeGreaterThan(0);
    });

    it('displays error icon when type is error', async () => {
        const wrapper = mount(Toast);
        
        wrapper.vm.show('Error', 'error');
        await wrapper.vm.$nextTick();
        
        const svgs = wrapper.findAll('svg');
        expect(svgs.length).toBeGreaterThan(0);
    });
});
