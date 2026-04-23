import { mount } from "@vue/test-utils";
import Checkbox from "@/Components/Checkbox.vue";
import { describe, it, expect } from "vitest";

describe("Checkbox.vue", () => {
    it("renders a checkbox input", () => {
        const wrapper = mount(Checkbox, {
            props: {
                checked: false,
            },
        });

        const input = wrapper.find('input[type="checkbox"]');
        expect(input.exists()).toBe(true);
    });

    it("applies correct CSS classes", () => {
        const wrapper = mount(Checkbox, {
            props: {
                checked: false,
            },
        });

        const input = wrapper.find("input");
        expect(input.classes()).toContain("rounded");
        expect(input.classes()).toContain("border-gray-300");
        expect(input.classes()).toContain("text-indigo-600");
    });

    it("updates checked state when toggled", async () => {
        const wrapper = mount(Checkbox, {
            props: {
                checked: false,
            },
        });

        const input = wrapper.find("input");
        input.setValue(true);

        expect(wrapper.emitted("update:checked")).toBeTruthy();
        expect(wrapper.emitted("update:checked")[0]).toEqual([true]);
    });

    it("accepts a value prop", () => {
        const wrapper = mount(Checkbox, {
            props: {
                checked: false,
                value: "test-value",
            },
        });

        const input = wrapper.find("input");
        expect(input.attributes("value")).toBe("test-value");
    });

    it("works with array of checked values", async () => {
        const wrapper = mount(Checkbox, {
            props: {
                checked: ["option1"],
                value: "option2",
            },
        });

        const input = wrapper.find("input");
        input.setValue(["option1", "option2"]);

        expect(wrapper.emitted("update:checked")[0]).toEqual([
            ["option1", "option2"],
        ]);
    });
});
