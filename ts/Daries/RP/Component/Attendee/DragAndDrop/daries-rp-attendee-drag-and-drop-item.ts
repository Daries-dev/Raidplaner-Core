/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { Autobind } from "../../../Ui/Event/Raid/Participant/DragAndDrop/Autobind";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { getPhrase } from "WoltLabSuite/Core/Language";
import { updateAttendeeStatus } from "../../../Api/Attendee/UpdateAttendeeStatus";
import UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import WoltlabCoreDialogElement from "WoltLabSuite/Core/Element/woltlab-core-dialog";

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
}

window.customElements.define("daries-rp-attendee-drag-and-drop-item", DariesRPAttendeeDragAndDropItemElement);

export default DariesRPAttendeeDragAndDropItemElement;
