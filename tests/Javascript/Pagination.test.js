import { mount } from "@vue/test-utils";
import Pagination from "@/Components/Pagination.vue";
import { describe, it, expect } from "vitest";

describe("Pagination.vue", () => {
    it("renders pagination links when provided", () => {
        const links = [
            { url: "/page/1", label: "1", active: true },
            { url: "/page/2", label: "2", active: false },
        ];

        const wrapper = mount(Pagination, {
            props: { links },
        });

        expect(wrapper.findAll("a").length).toBeGreaterThan(0);
    });

    it("shows disabled state for null url links", () => {
        const links = [{ url: null, label: "Previous", active: false }];

        const wrapper = mount(Pagination, {
            props: { links },
        });

        expect(wrapper.text()).toContain("Previous");
    });

    it("applies active styling to active links", () => {
        const links = [{ url: "/page/1", label: "1", active: true }];

        const wrapper = mount(Pagination, {
            props: { links },
        });

        const activeLink = wrapper.find("a");
        expect(activeLink.classes()).toContain("bg-blue-600");
        expect(activeLink.classes()).toContain("text-white");
    });

    it("applies inactive styling to non-active links", () => {
        const links = [{ url: "/page/1", label: "1", active: false }];

        const wrapper = mount(Pagination, {
            props: { links },
        });

        const link = wrapper.find("a");
        expect(link.classes()).toContain("bg-white");
        expect(link.classes()).toContain("text-gray-700");
    });

    it("renders with correct href attributes", () => {
        const links = [
            { url: "/page/1", label: "1", active: false },
            { url: "/page/2", label: "2", active: true },
        ];

        const wrapper = mount(Pagination, {
            props: { links },
        });

        const paginationLinks = wrapper.findAll("a");
        expect(paginationLinks[0].attributes("href")).toBe("/page/1");
        expect(paginationLinks[1].attributes("href")).toBe("/page/2");
    });
});
