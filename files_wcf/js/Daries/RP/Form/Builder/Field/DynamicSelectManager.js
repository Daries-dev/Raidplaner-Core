/**
 * A class for dynamically managing the visibility and activation of options
 * in a target select element based on the selection of a current select element.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DynamicSelectManager = void 0;
    class DynamicSelectManager {
        #triggerSelect;
        #filteredSelect;
        #optionsMapping;
        constructor(triggerSelect, filteredSelect, optionsMapping) {
            this.#triggerSelect = document.getElementById(triggerSelect);
            this.#filteredSelect = document.getElementById(filteredSelect);
            this.#optionsMapping = optionsMapping;
            this.#triggerSelect.addEventListener("change", () => this.#handleSelectChange());
            this.#handleSelectChange();
        }
        #handleSelectChange() {
            const triggerValue = this.#triggerSelect.value;
            const allowedOptions = this.#optionsMapping[triggerValue] || [];
            Array.from(this.#filteredSelect.options).forEach((option) => {
                const optionValue = option.value;
                if (allowedOptions.includes(optionValue) || !optionValue) {
                    option.style.display = "block";
                    option.disabled = false;
                }
                else {
                    option.style.display = "none";
                    option.disabled = true;
                }
            });
        }
    }
    exports.DynamicSelectManager = DynamicSelectManager;
});
