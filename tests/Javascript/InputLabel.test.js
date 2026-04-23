import { mount } from "@vue/test-utils";
import InputLabel from "@/Components/InputLabel.vue";
import { describe, it, expect } from "vitest";

describe("InputLabel.vue", () => {
    it("renders a label element", () => {
        const wrapper = mount(InputLabel, {
            props: {
                value: "Email",
            },
        });

        const label = wrapper.find("label");
        expect(label.exists()).toBe(true);
    });

    it("displays label value when prop is provided", () => {
        const wrapper = mount(InputLabel, {
            props: {
                value: "Username",
            },
        });

        const span = wrapper.find("span");
        expect(span.exists()).toBe(true);
        expect(span.text()).toBe("Username");
    });

    it("displays slot content when value prop is not provided", () => {
        const wrapper = mount(InputLabel, {
            slots: {
                default: "Password Label",
            },
        });

        expect(wrapper.text()).toContain("Password Label");
    });

    it("applies correct CSS classes", () => {
        const wrapper = mount(InputLabel, {
            props: {
                value: "Test",
            },
        });

        const label = wrapper.find("label");
        expect(label.classes()).toContain("block");
        expect(label.classes()).toContain("text-sm");
        expect(label.classes()).toContain("font-medium");
        expect(label.classes()).toContain("text-gray-700");
    });
});
