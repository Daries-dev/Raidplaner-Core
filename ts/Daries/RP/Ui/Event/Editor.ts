/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import { stringToBool } from "WoltLabSuite/Core/Core";

export class UiEventEditor {
  readonly #event: HTMLElement;

  constructor() {
    this.#event = document.querySelector<HTMLElement>(".event")!;
    if (!stringToBool(this.#event.dataset.canEdit!)) return;

    const dropdownMenu = UiDropdownSimple.getDropdownMenu("eventDropdown");

    const editLink = dropdownMenu!.querySelector<HTMLAnchorElement>(".jsEditLink");
    if (editLink) {
      const toggleButton = document.querySelector<HTMLAnchorElement>(".eventDropdown > .dropdownToggle");
      toggleButton?.addEventListener("dblclick", (event) => {
        event.preventDefault();

        editLink.click();
      });
    }
  }
}

export default UiEventEditor;
