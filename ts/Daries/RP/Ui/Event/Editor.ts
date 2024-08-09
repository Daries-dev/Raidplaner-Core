/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import { add as addHandler } from "WoltLabSuite/Core/Event/Handler";
import { DisableAction } from "./Action/DisableAction";
import { stringToBool } from "WoltLabSuite/Core/Core";
import { getPhrase } from "WoltLabSuite/Core/Language";

export class UiEventEditor {
  readonly #event: HTMLElement;
  readonly #eventId: number;

  constructor() {
    this.#event = document.querySelector<HTMLElement>(".event")!;
    if (!stringToBool(this.#event.dataset.canEdit!)) return;

    this.#eventId = parseInt(this.#event.dataset.eventId!);

    this.#initEvent();

    addHandler("dev.daries.rp.event", "refresh", (data: RefreshEventData) => this.#refreshEvent(data));
  }

  #initEvent(): void {
    const dropdownMenu = UiDropdownSimple.getDropdownMenu("eventDropdown");

    const editLink = dropdownMenu!.querySelector<HTMLAnchorElement>(".jsEditLink");
    if (editLink) {
      const toggleButton = document.querySelector<HTMLAnchorElement>(".eventDropdown > .dropdownToggle");
      toggleButton?.addEventListener("dblclick", (event) => {
        event.preventDefault();

        editLink.click();
      });
    }

    const enableEvent = dropdownMenu!.querySelector<HTMLAnchorElement>(".jsEnable");

    if (enableEvent) {
      new DisableAction(enableEvent, this.#event);
    }
  }

  #refreshEvent(data: RefreshEventData): void {
    if (this.#eventId != data.eventId) return;

    const eventIcons = document.querySelector<HTMLHeadingElement>(".rpEventHeader .contentHeaderTitle .contentTitle");

    if (data.action === "disabled") {
      const isDisabled = !stringToBool(this.#event.dataset.enabled!);
      let iconIsDisabled = document.querySelector<HTMLElement>(".rpEventHeader .jsIsDisabled");
      if (isDisabled && iconIsDisabled === null) {
        iconIsDisabled = document.createElement("span");
        iconIsDisabled.classList.add("badge", "label", "green", "jsIsDisabled");
        iconIsDisabled.innerHTML = getPhrase("wcf.message.status.disabled");
        eventIcons?.appendChild(iconIsDisabled);
      } else if (!isDisabled && iconIsDisabled !== null) {
        iconIsDisabled.remove();
      }
    }
  }
}

export default UiEventEditor;

interface RefreshEventData {
  action: string;
  eventId: number;
}
