import { mount } from '@vue/test-utils';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { describe, it, expect } from 'vitest';

describe('ApplicationLogo.vue', () => {
    it('renders an SVG element', () => {
        const wrapper = mount(ApplicationLogo);
        
        const svg = wrapper.find('svg');
        expect(svg.exists()).toBe(true);
    });

    it('has correct viewBox attribute', () => {
        const wrapper = mount(ApplicationLogo);
        
        const svg = wrapper.find('svg');
        expect(svg.attributes('viewBox')).toBe('0 0 316 316');
    });

    it('has correct xmlns attribute', () => {
        const wrapper = mount(ApplicationLogo);
        
        const svg = wrapper.find('svg');
        expect(svg.attributes('xmlns')).toBe('http://www.w3.org/2000/svg');
    });

    it('contains a path element', () => {
        const wrapper = mount(ApplicationLogo);
        
        const path = wrapper.find('path');
        expect(path.exists()).toBe(true);
    });

    it('path element has a d attribute', () => {
        const wrapper = mount(ApplicationLogo);
        
        const path = wrapper.find('path');
        expect(path.attributes('d')).toBeTruthy();
    });
});
