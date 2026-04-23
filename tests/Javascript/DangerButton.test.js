import { mount } from "@vue/test-utils";
import DangerButton from "@/Components/DangerButton.vue";
import { describe, it, expect } from "vitest";

describe("DangerButton.vue", () => {
    it("renders a button element", () => {
        const wrapper = mount(DangerButton, {
            slots: {
                default: "Delete",
            },
        });

        const button = wrapper.find("button");
        expect(button.exists()).toBe(true);
    });

    it("displays slot content", () => {
        const wrapper = mount(DangerButton, {
            slots: {
                default: "Delete Item",
            },
        });

        expect(wrapper.text()).toBe("Delete Item");
    });

    it("applies correct danger button CSS classes", () => {
        const wrapper = mount(DangerButton);

        const button = wrapper.find("button");
        expect(button.classes()).toContain("bg-red-600");
        expect(button.classes()).toContain("hover:bg-red-500");
        expect(button.classes()).toContain("focus:ring-red-500");
        expect(button.classes()).toContain("active:bg-red-700");
    });

    it("has correct text styling", () => {
        const wrapper = mount(DangerButton);

        const button = wrapper.find("button");
        expect(button.classes()).toContain("text-white");
        expect(button.classes()).toContain("uppercase");
    });
});
