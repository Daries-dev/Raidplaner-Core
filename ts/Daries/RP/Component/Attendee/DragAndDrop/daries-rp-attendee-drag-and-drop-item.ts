/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { Autobind } from "../../../Ui/Event/Raid/Participant/DragAndDrop/Autobind";

export class DariesRPAttendeeDragAndDropItemElement extends HTMLElement {
  connectedCallback() {
    this.addEventListener("dragstart", (event) => {
      this.dragStartHandler(event);
    });

    this.addEventListener("dragend", (event) => {
      this.dragEndHandler(event);
    });
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

  get attendeeId(): number {
    return parseInt(this.getAttribute("attendee-id")!);
  }

  get droppableTo(): string {
    return this.getAttribute("droppable-to")!;
  }
}

window.customElements.define("daries-rp-attendee-drag-and-drop-item", DariesRPAttendeeDragAndDropItemElement);

export default DariesRPAttendeeDragAndDropItemElement;
