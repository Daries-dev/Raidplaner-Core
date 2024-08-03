/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { promiseMutex } from "WoltLabSuite/Core/Helper/PromiseMutex";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { renderAttendee } from "../../../../Api/Attendees/RenderAttendee";
import DariesRPAttendeeDragAndDropBoxElement from "../../../../Component/Attendee/DragAndDrop/daries-rp-attendee-drag-and-drop-box";

async function addParticipant(button: HTMLElement): Promise<void> {
  const { ok, result } = await dialogFactory().usingFormBuilder().fromEndpoint<Participant>(button.dataset.addParticipant!);

  if (ok) {
    const response = await renderAttendee(result.attendeeId);
    if (!response.ok) {
      const validationError = response.error.getValidationError();
      if (validationError === undefined) {
        throw response.error;
      }
      return;
    }

    const box = document.querySelector<DariesRPAttendeeDragAndDropBoxElement>(
      `daries-rp-attendee-drag-and-drop-box[distribution-id="${response.value.distributionId}"][status="${result.status}"]`,
    );
    const attendeeList = box?.querySelector<HTMLElement>(".attendeeList");
    attendeeList?.insertAdjacentHTML("beforeend", response.value.template);

    button.hidden = true;
    showNotification();
  }
}

export function setup(button: HTMLElement): void {
  button.addEventListener(
    "click",
    promiseMutex(() => addParticipant(button)),
  );
}

interface Participant {
  attendeeId: number;
  distributionId: number;
  status: number;
}
