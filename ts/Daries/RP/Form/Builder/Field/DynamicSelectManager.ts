/**
 * A class for dynamically managing the visibility and activation of options
 * in a target select element based on the selection of a current select element.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

export class DynamicSelectManager {
  readonly #triggerSelect: HTMLSelectElement;
  readonly #filteredSelect: HTMLSelectElement;
  readonly #optionsMapping: SelectOptionsMapping;

  constructor(triggerSelect: string, filteredSelect: string, optionsMapping: SelectOptionsMapping) {
    this.#triggerSelect = document.getElementById(triggerSelect) as HTMLSelectElement;
    this.#filteredSelect = document.getElementById(filteredSelect) as HTMLSelectElement;
    this.#optionsMapping = optionsMapping;

    this.#triggerSelect.addEventListener("change", () => this.#handleSelectChange());
    this.#handleSelectChange();
  }

  #handleSelectChange(): void {
    const triggerValue = this.#triggerSelect.value;
    const allowedOptions = this.#optionsMapping[triggerValue] || [];

    Array.from(this.#filteredSelect.options).forEach((option: HTMLOptionElement) => {
      const optionValue = option.value;

      if (allowedOptions.includes(optionValue) || !optionValue) {
        option.style.display = "block";
        option.disabled = false;
      } else {  
        option.style.display = "none";
        option.disabled = true;
      }
    });
  }
}

interface SelectOptionsMapping {
  [key: string]: string[];
}
