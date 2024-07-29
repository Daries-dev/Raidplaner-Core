/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import DomUtil from "WoltLabSuite/Core/Dom/Util";
import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { promiseMutex } from "WoltLabSuite/Core/Helper/PromiseMutex";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";

async function addParticipant(button: HTMLElement): Promise<void> {
  const { ok, result } = await dialogFactory().usingFormBuilder().fromEndpoint<Test>(button.dataset.addParticipant!);

  if (ok) {
    document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
      if (
        result.distributionId === ~~attendeeBox.dataset.objectId! &&
        result.status === ~~attendeeBox.dataset.status!
      ) {
        const attendeeList = attendeeBox.querySelector<HTMLElement>(".attendeeList")!;
        DomUtil.insertHtml(result.template, attendeeList, "append");

        DomUtil.hide(button);
        showNotification();
      }
    });
  }
}

export function setup(button: HTMLElement): void {
  button.addEventListener(
    "click",
    promiseMutex(() => addParticipant(button)),
  );
}

interface Test {
  attendeeId: number;
  distributionId: number;
  status: number;
  template: string;
}
