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
import { TrashAction } from "./Action/TrashAction";

export class UiEventEditor {
  readonly #event: HTMLElement;
  readonly #eventId: number;
  #trashButton: HTMLAnchorElement | null = null;

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

    if (stringToBool(this.#event.dataset.canDelete!)) {
      this.#trashButton = dropdownMenu!.querySelector<HTMLAnchorElement>(".jsTrash");
      if (this.#trashButton) {
        new TrashAction(this.#trashButton, this.#event);

        if (!stringToBool(this.#event.dataset.deleted!)) {
          this.#trashButton.parentElement!.hidden = false;
        }
      }
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
    } else if (data.action == "trash") {
      let iconIsDeleted = document.querySelector<HTMLElement>(".rpEventHeader .jsIsDeleted");
      if (iconIsDeleted === null) {
        iconIsDeleted = document.createElement("span");
        iconIsDeleted.classList.add("badge", "label", "red", "jsIsDeleted");
        iconIsDeleted.innerHTML = getPhrase("wcf.message.status.deleted");
        eventIcons?.appendChild(iconIsDeleted);
      }
    }
  }
}

export default UiEventEditor;

interface RefreshEventData {
  action: string;
  eventId: number;
}
