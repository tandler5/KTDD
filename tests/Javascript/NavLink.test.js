import { mount } from "@vue/test-utils";
import NavLink from "@/Components/NavLink.vue";
import { describe, it, expect } from "vitest";

describe("NavLink.vue", () => {
    it("renders a link element", () => {
        const wrapper = mount(NavLink, {
            props: { href: "/books" },
            slots: { default: "Books" },
        });

        expect(wrapper.find("a").exists()).toBe(true);
        expect(wrapper.find("a").attributes("href")).toBe("/books");
    });

    it("displays slot content", () => {
        const wrapper = mount(NavLink, {
            props: { href: "/books" },
            slots: { default: "Books" },
        });

        expect(wrapper.text()).toBe("Books");
    });

    it("applies active classes when active prop is true", () => {
        const wrapper = mount(NavLink, {
            props: { href: "/books", active: true },
        });

        const link = wrapper.find("a");
        expect(link.classes()).toContain("border-indigo-400");
        expect(link.classes()).toContain("text-gray-900");
    });

    it("applies inactive classes when active prop is false", () => {
        const wrapper = mount(NavLink, {
            props: { href: "/books", active: false },
        });

        const link = wrapper.find("a");
        expect(link.classes()).toContain("border-transparent");
        expect(link.classes()).toContain("text-gray-500");
    });

    it("applies inactive classes by default", () => {
        const wrapper = mount(NavLink, {
            props: { href: "/books" },
        });

        const link = wrapper.find("a");
        expect(link.classes()).toContain("border-transparent");
    });
});
