import { mount } from "@vue/test-utils";
import Dropdown from "@/Components/Dropdown.vue";
import { describe, it, expect, vi, beforeEach } from "vitest";

describe("Dropdown.vue", () => {
    beforeEach(() => {
        document.addEventListener = vi.fn();
        document.removeEventListener = vi.fn();
    });

    it("renders a dropdown container", () => {
        const wrapper = mount(Dropdown);

        expect(wrapper.find(".relative").exists()).toBe(true);
    });

    it("opens dropdown when trigger is clicked", async () => {
        const wrapper = mount(Dropdown, {
            slots: {
                trigger: "Menu",
                content: "Item 1",
            },
        });

        await wrapper.find('[class*="relative"] > div').trigger("click");

        expect(wrapper.vm.open).toBe(true);
    });

    it("closes dropdown when clicking on overlay", async () => {
        const wrapper = mount(Dropdown, {
            slots: {
                trigger: "Menu",
                content: "Item 1",
            },
        });

        wrapper.vm.open = true;
        await wrapper.vm.$nextTick();

        const overlay = wrapper.findAll(".fixed")[0];
        await overlay.trigger("click");

        expect(wrapper.vm.open).toBe(false);
    });

    it("closes dropdown when Escape key is pressed", async () => {
        const wrapper = mount(Dropdown, {
            slots: {
                trigger: "Menu",
                content: "Item 1",
            },
        });

        wrapper.vm.open = true;
        await wrapper.vm.$nextTick();

        const event = new KeyboardEvent("keydown", { key: "Escape" });
        document.dispatchEvent(event);
        wrapper.vm.closeOnEscape(event);

        expect(wrapper.vm.open).toBe(false);
    });

    it("applies correct width class based on prop", () => {
        const wrapper = mount(Dropdown, {
            props: {
                width: "48",
            },
        });

        expect(wrapper.vm.widthClass).toBe("w-48");
    });

    it("applies correct alignment classes for left align", () => {
        const wrapper = mount(Dropdown, {
            props: {
                align: "left",
            },
        });

        expect(wrapper.vm.alignmentClasses).toContain("origin-top-left");
        expect(wrapper.vm.alignmentClasses).toContain("start-0");
    });

    it("applies correct alignment classes for right align", () => {
        const wrapper = mount(Dropdown, {
            props: {
                align: "right",
            },
        });

        expect(wrapper.vm.alignmentClasses).toContain("origin-top-right");
        expect(wrapper.vm.alignmentClasses).toContain("end-0");
    });
});
