import { mount } from "@vue/test-utils";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { describe, it, expect } from "vitest";

describe("ResponsiveNavLink.vue", () => {
    it("renders a link element", () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: "/books" },
            slots: { default: "Books" },
        });

        expect(wrapper.find("a").exists()).toBe(true);
        expect(wrapper.find("a").attributes("href")).toBe("/books");
    });

    it("displays slot content", () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: "/books" },
            slots: { default: "Books" },
        });

        expect(wrapper.text()).toBe("Books");
    });

    it("applies active classes when active prop is true", () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: "/books", active: true },
        });

        const link = wrapper.find("a");
        expect(link.classes()).toContain("border-indigo-400");
        expect(link.classes()).toContain("text-indigo-700");
        expect(link.classes()).toContain("bg-indigo-50");
    });

    it("applies inactive classes when active prop is false", () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: "/books", active: false },
        });

        const link = wrapper.find("a");
        expect(link.classes()).toContain("border-transparent");
        expect(link.classes()).toContain("text-gray-600");
    });

    it("applies correct base CSS classes", () => {
        const wrapper = mount(ResponsiveNavLink, {
            props: { href: "/books" },
        });

        const link = wrapper.find("a");
        expect(link.classes()).toContain("block");
        expect(link.classes()).toContain("w-full");
        expect(link.classes()).toContain("ps-3");
        expect(link.classes()).toContain("pe-4");
    });
});
