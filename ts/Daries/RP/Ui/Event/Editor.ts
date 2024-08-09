/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import { stringToBool } from "WoltLabSuite/Core/Core";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { trashEvent } from "../../Api/Events/TrashEvent";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { restoreEvent } from "../../Api/Events/RestoreEvent";
import { enableDisableEvent } from "../../Api/Events/EnableDisableEvent";
import { deleteEvent } from "../../Api/Events/DeleteEvent";

export class UiEventEditor {
  #deleteButton: HTMLAnchorElement | null = null;
  readonly #event: HTMLElement;
  readonly #eventIcons: HTMLHeadingElement;
  readonly #eventId: number;
  #restoreButton: HTMLAnchorElement | null = null;
  #trashButton: HTMLAnchorElement | null = null;

  constructor() {
    this.#event = document.querySelector<HTMLElement>(".event")!;
    if (!stringToBool(this.#event.dataset.canEdit!)) return;

    this.#eventId = parseInt(this.#event.dataset.eventId!);
    this.#eventIcons = document.querySelector<HTMLHeadingElement>(".rpEventHeader .contentHeaderTitle .contentTitle")!;

    this.#initEvent();
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

    this.#initDeleteButton(dropdownMenu!);
    this.#initEnableButton(dropdownMenu!);
    this.#initRestoreButton(dropdownMenu!);
    this.#initTrashButton(dropdownMenu!);
  }

  #initDeleteButton(dropdownMenu: HTMLElement): void {
    if (stringToBool(this.#event.dataset.canDelete!)) {
      this.#deleteButton = dropdownMenu.querySelector<HTMLAnchorElement>(".jsDelete");
      if (this.#deleteButton) {
        this.#deleteButton.addEventListener("click", async () => {
          const title = this.#event.dataset.title!;
          const result = await confirmationFactory().delete(title);

          if (result) {
            const response = await deleteEvent(this.#eventId);
            if (!response.ok) {
              const validationError = response.error.getValidationError();
              if (validationError === undefined) {
                throw response.error;
              }
              dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
              return;
            }

            showNotification();

            window.location.href = `${window.RP_API_URL}index.php?calendar`;
          }
        });

        if (stringToBool(this.#event.dataset.deleted!)) {
          this.#deleteButton.parentElement!.hidden = false;
        }
      }
    }
  }

  #initEnableButton(dropdownMenu: HTMLElement): void {
    const enableEvent = dropdownMenu.querySelector<HTMLAnchorElement>(".jsEnable");
    if (enableEvent) {
      enableEvent.addEventListener("click", async () => {
        const isEnabled = stringToBool(this.#event.dataset.enabled!);

        const response = await enableDisableEvent(this.#eventId, isEnabled);
        if (!response.ok) {
          const validationError = response.error.getValidationError();
          if (validationError === undefined) {
            throw response.error;
          }
          dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
          return;
        }

        this.#event.dataset.enabled = isEnabled ? "false" : "true";

        if (isEnabled) {
          enableEvent.textContent = enableEvent.dataset.enableMessage!;
        } else {
          enableEvent.textContent = enableEvent.dataset.disableMessage!;
        }

        const isDisabled = !stringToBool(this.#event.dataset.enabled);
        let iconIsDisabled = document.querySelector<HTMLElement>(".rpEventHeader .jsIsDisabled");
        if (isDisabled && iconIsDisabled === null) {
          iconIsDisabled = document.createElement("span");
          iconIsDisabled.classList.add("badge", "label", "green", "jsIsDisabled");
          iconIsDisabled.innerHTML = getPhrase("wcf.message.status.disabled");
          this.#eventIcons.appendChild(iconIsDisabled);
        } else if (!isDisabled && iconIsDisabled !== null) {
          iconIsDisabled.remove();
        }

        showNotification();
      });
    }
  }

  #initRestoreButton(dropdownMenu: HTMLElement): void {
    if (stringToBool(this.#event.dataset.canRestore!)) {
      this.#restoreButton = dropdownMenu.querySelector<HTMLAnchorElement>(".jsRestore");
      if (this.#restoreButton) {
        this.#restoreButton.addEventListener("click", async () => {
          const title = this.#event.dataset.title!;
          const result = await confirmationFactory().restore(title);

          if (result) {
            const response = await restoreEvent(this.#eventId);
            if (!response.ok) {
              const validationError = response.error.getValidationError();
              if (validationError === undefined) {
                throw response.error;
              }
              dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
              return;
            }

            const iconIsDeleted = document.querySelector<HTMLElement>(".rpEventHeader .jsIsDeleted");
            if (iconIsDeleted !== null) {
              iconIsDeleted.remove();
            }

            this.#event.dataset.deleted = "false";
            this.#deleteButton!.parentElement!.hidden = true;
            this.#restoreButton!.parentElement!.hidden = true;
            this.#trashButton!.parentElement!.hidden = false;

            showNotification();
          }
        });

        if (stringToBool(this.#event.dataset.deleted!)) {
          this.#restoreButton.parentElement!.hidden = false;
        }
      }
    }
  }

  #initTrashButton(dropdownMenu: HTMLElement): void {
    if (stringToBool(this.#event.dataset.canTrash!)) {
      this.#trashButton = dropdownMenu.querySelector<HTMLAnchorElement>(".jsTrash");
      if (this.#trashButton) {
        this.#trashButton.addEventListener("click", async () => {
          const title = this.#event.dataset.title!;
          const { result } = await confirmationFactory().softDelete(title);

          if (result) {
            const response = await trashEvent(this.#eventId);
            if (!response.ok) {
              const validationError = response.error.getValidationError();
              if (validationError === undefined) {
                throw response.error;
              }
              dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
              return;
            }

            this.#event.dataset.deleted = "true";

            let iconIsDeleted = document.querySelector<HTMLElement>(".rpEventHeader .jsIsDeleted");
            if (iconIsDeleted === null) {
              iconIsDeleted = document.createElement("span");
              iconIsDeleted.classList.add("badge", "label", "red", "jsIsDeleted");
              iconIsDeleted.innerHTML = getPhrase("wcf.message.status.deleted");
              this.#eventIcons.appendChild(iconIsDeleted);
            }

            this.#deleteButton!.parentElement!.hidden = false;
            this.#restoreButton!.parentElement!.hidden = false;
            this.#trashButton!.parentElement!.hidden = true;

            showNotification();
          }
        });

        if (!stringToBool(this.#event.dataset.deleted!)) {
          this.#trashButton.parentElement!.hidden = false;
        }
      }
    }
  }
}

export default UiEventEditor;
