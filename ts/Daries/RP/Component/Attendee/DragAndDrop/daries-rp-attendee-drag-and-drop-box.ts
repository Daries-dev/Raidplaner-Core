/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { Autobind } from "../../../Ui/Event/Raid/Participant/DragAndDrop/Autobind";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { updateAttendeeStatus } from "../../../Api/Attendees/UpdateAttendeeStatus";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";

export class DariesRPAttendeeDragAndDropBoxElement extends HTMLElement {
  connectedCallback() {
    this.addEventListener("dragover", (event) => {
      this.dragOverHandler(event);
    });

    this.addEventListener("drop", (event) => {
      void this.dropHandler(event);
    });

    this.addEventListener("dragleave", (event) => {
      this.dragLeaveHandler(event);
    });
  }

  @Autobind
  dragOverHandler(event: DragEvent): void {
    if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return;
    event.preventDefault();

    const droppable = this.droppable;
    const droppableTo = event.dataTransfer.getData("droppableTo");
    if (!droppableTo.includes(droppable)) return;

    this.classList.add("selected");
  }

  @Autobind
  async dropHandler(event: DragEvent): Promise<void> {
    if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return;
    event.preventDefault();

    const droppable = this.droppable;
    const droppableTo = event.dataTransfer.getData("droppableTo");
    if (!droppableTo.includes(droppable)) return;

    const distributionId = this.distributionId;
    const status = this.status;

    if (
      status === event.dataTransfer.getData("currentStatus") &&
      distributionId === parseInt(event.dataTransfer.getData("distributionId"))
    ) {
      return;
    }

    const attendeeId = parseInt(event.dataTransfer.getData("attendeeId"));

    const response = await updateAttendeeStatus(attendeeId, this.distributionId, this.status);
    if (!response.ok) {
      const validationError = response.error.getValidationError();
      if (validationError === undefined) {
        throw response.error;
      }
      dialogFactory().fromHtml(`<p>${validationError.message}</p>`).asAlert();
      return;
    }

    const attendeeList = this.querySelector<HTMLElement>(".attendeeList");
    const attendee = document.getElementById(event.dataTransfer.getData("id"))!;
    attendee.setAttribute("distribution-id", this.distributionId.toString());
    attendeeList?.insertAdjacentElement("beforeend", attendee);

    showNotification();
  }

  @Autobind
  dragLeaveHandler(event: DragEvent): void {
    if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return;
    event.preventDefault();

    this.classList.remove("selected");
  }

  get distributionId(): number {
    return parseInt(this.getAttribute("distribution-id")!);
  }

  get droppable(): string {
    return this.getAttribute("droppable")!;
  }

  get status(): string {
    return this.getAttribute("status")!;
  }
}

window.customElements.define("daries-rp-attendee-drag-and-drop-box", DariesRPAttendeeDragAndDropBoxElement);

export default DariesRPAttendeeDragAndDropBoxElement;
