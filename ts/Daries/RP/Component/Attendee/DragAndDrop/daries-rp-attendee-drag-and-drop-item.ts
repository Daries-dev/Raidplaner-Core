/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import DariesRPAttendeeDragAndDropBoxElement from "./daries-rp-attendee-drag-and-drop-box";
import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import WoltlabCoreDialogElement from "WoltLabSuite/Core/Element/woltlab-core-dialog";
import { Autobind } from "../../../Ui/Event/Raid/Participant/DragAndDrop/Autobind";
import { availableCharacters } from "../../../Api/Events/AvailableCharacters";
import { createAttendee } from "../../../Api/Attendees/CreateAttendee";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { deleteAttendee } from "../../../Api/Attendees/DeleteAttendee";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { renderAttendee } from "../../../Api/Attendees/RenderAttendee";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { updateAttendeeStatus } from "../../../Api/Attendees/UpdateAttendeeStatus";

export class DariesRPAttendeeDragAndDropItemElement extends HTMLElement {
  #dialog: WoltlabCoreDialogElement;
  #statusDialog: string;

  connectedCallback() {
    this.addEventListener("dragstart", (event) => {
      this.dragStartHandler(event);
    });

    this.addEventListener("dragend", (event) => {
      this.dragEndHandler(event);
    });

    if (this.menu) {
      this.#statusDialog = `
        <div class="section">
            <dl>
                <dt>${getPhrase("rp.event.raid.status")}</dt>
                <dd>
                    <select name="status">
                        <option value="0">${getPhrase("rp.event.raid.container.login")}</option>
                        <option value="3">${getPhrase("rp.event.raid.container.reserve")}</option>
                        <option value="2">${getPhrase("rp.event.raid.container.logout")}</option>
                    </select>
                </dd>
            </dl>
        </div>
        `;

      const updateStatusButton = this.menu.querySelector<HTMLElement>(".attendee__option--update-status");
      updateStatusButton?.addEventListener("click", (event) => {
        event.preventDefault();
        this.#updateStatus();
      });

      const switchCharacterButton = this.menu.querySelector<HTMLElement>(".attendee__option--character-switch");
      switchCharacterButton?.addEventListener("click", (event) => {
        event.preventDefault();
        void this.#switchCharacter();
      });
    }
  }

  @Autobind
  dragEndHandler(_: DragEvent): void {
    document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
      attendeeBox.classList.remove("droppable");
      attendeeBox.classList.remove("selected");
    });
  }

  @Autobind
  dragStartHandler(event: DragEvent): void {
    event.dataTransfer!.setData("id", this.id);
    event.dataTransfer!.setData("attendeeId", this.attendeeId.toString());
    event.dataTransfer!.setData("droppableTo", this.droppableTo);
    event.dataTransfer!.effectAllowed = "move";

    const currentBox = this.closest<HTMLElement>(".attendeeBox");
    event.dataTransfer!.setData("currentStatus", currentBox!.getAttribute("status")!);
    event.dataTransfer!.setData("distributionId", currentBox!.getAttribute("distribution-id")!);

    document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
      const droppable = attendeeBox.getAttribute("droppable")!;
      const droppableTo = this.droppableTo;
      if (!droppableTo.includes(droppable)) return;

      attendeeBox.classList.add("droppable");
    });
  }

  async #loadSwitchCharacter(attendeeId: number): Promise<void> {
    const response = await renderAttendee(attendeeId);
    if (!response.ok) {
      const validationError = response.error.getValidationError();
      if (validationError === undefined) {
        throw response.error;
      }

      this.remove();
      return;
    }

    const box = document.querySelector<DariesRPAttendeeDragAndDropBoxElement>(
      `daries-rp-attendee-drag-and-drop-box[distribution-id="${response.value.distributionId}"][status="${this.status}"]`,
    );
    const attendeeList = box?.querySelector<HTMLElement>(".attendeeList");
    attendeeList?.insertAdjacentHTML("beforeend", response.value.template);

    showNotification();
    this.remove();
  }

  async #switchCharacter(): Promise<void> {
    const { template } = (await availableCharacters(this.eventId)).unwrap();
    console.log(template);
    this.#showSwitchDialog(template);
  }

  #showSwitchDialog(template: string): void {
    const dialog = dialogFactory().fromHtml(template).asPrompt();
    const characterId = dialog.content.querySelector<HTMLSelectElement>('select[name="characterID"]');
    const roleId = dialog.content.querySelector<HTMLSelectElement>('select[name="roleId"]');
    dialog.addEventListener("primary", async () => {
      (await deleteAttendee(this.attendeeId)).unwrap();
      this.dispatchEvent(new CustomEvent("delete"));

      const response = await createAttendee(this.eventId, characterId!.value, parseInt(roleId!.value), this.status);
      if (!response.ok) {
        const validationError = response.error.getValidationError();
        if (validationError === undefined) {
          throw response.error;
        }

        this.remove();
        return;
      }

      void this.#loadSwitchCharacter(response.value.attendeeId);
    });

    dialog.show(getPhrase("rp.character.selection"));
  }

  #updateStatus(): void {
    if (!this.#dialog) {
      this.#dialog = dialogFactory().fromHtml(this.#statusDialog).asPrompt();
      const status = this.#dialog.content.querySelector<HTMLSelectElement>('select[name="status"]');
      this.#dialog.addEventListener("primary", async () => {
        const response = await updateAttendeeStatus(this.attendeeId, this.distributionId, status!.value);
        if (!response.ok) {
          const validationError = response.error.getValidationError();
          if (validationError === undefined) {
            throw response.error;
          }
          dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
          return;
        }

        const dragAndDropBox = document.querySelector(
          `daries-rp-attendee-drag-and-drop-box[status="${status!.value}"][distribution-id="${this.distributionId}"]`,
        );
        const attendeeList = dragAndDropBox?.querySelector<HTMLElement>(".attendeeList");
        attendeeList?.insertAdjacentElement("beforeend", this);
      });
    }

    this.#dialog.show(getPhrase("rp.event.raid.updateStatus"));
  }

  get attendeeId(): number {
    return parseInt(this.getAttribute("attendee-id")!);
  }

  get box(): DariesRPAttendeeDragAndDropBoxElement {
    return this.closest<DariesRPAttendeeDragAndDropBoxElement>("daries-rp-attendee-drag-and-drop-box")!;
  }

  get distributionId(): number {
    return parseInt(this.getAttribute("distribution-id")!);
  }

  get droppableTo(): string {
    return this.getAttribute("droppable-to")!;
  }

  get eventId(): number {
    return parseInt(this.getAttribute("event-id")!);
  }

  get menu(): HTMLElement | undefined {
    let menu = UiDropdownSimple.getDropdownMenu(`attendeeOptions${this.attendeeId}`);

    if (menu === undefined) {
      menu = this.querySelector<HTMLElement>(".attendee__menu .dropdownMenu") || undefined;
    }

    return menu;
  }

  get status(): number {
    return parseInt(this.box.getAttribute("status")!);
  }
}

window.customElements.define("daries-rp-attendee-drag-and-drop-item", DariesRPAttendeeDragAndDropItemElement);

export default DariesRPAttendeeDragAndDropItemElement;
