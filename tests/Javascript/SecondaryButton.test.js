import { mount } from "@vue/test-utils";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { describe, it, expect } from "vitest";

describe("SecondaryButton.vue", () => {
    it("renders a button element", () => {
        const wrapper = mount(SecondaryButton, {
            slots: {
                default: "Cancel",
            },
        });

        const button = wrapper.find("button");
        expect(button.exists()).toBe(true);
    });

    it("displays slot content", () => {
        const wrapper = mount(SecondaryButton, {
            slots: {
                default: "Cancel Operation",
            },
        });

        expect(wrapper.text()).toBe("Cancel Operation");
    });

    it("applies correct secondary button CSS classes", () => {
        const wrapper = mount(SecondaryButton);

        const button = wrapper.find("button");
        expect(button.classes()).toContain("bg-white");
        expect(button.classes()).toContain("border-gray-300");
        expect(button.classes()).toContain("text-gray-700");
    });

    it("supports type prop (button, submit, reset)", async () => {
        const wrapper = mount(SecondaryButton, {
            props: {
                type: "submit",
            },
        });

        const button = wrapper.find("button");
        expect(button.attributes("type")).toBe("submit");
    });

    it("defaults to button type", () => {
        const wrapper = mount(SecondaryButton);

        const button = wrapper.find("button");
        expect(button.attributes("type")).toBe("button");
    });
});
