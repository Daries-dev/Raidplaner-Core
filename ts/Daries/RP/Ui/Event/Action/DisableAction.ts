/**
 * Handles a event disable/enable button.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { dboAction } from "WoltLabSuite/Core/Ajax";
import AbstractEventAction from "./Abstract";
import { fire as fireHandler } from "WoltLabSuite/Core/Event/Handler";
import { stringToBool } from "WoltLabSuite/Core/Core";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";

export class DisableAction extends AbstractEventAction {
  public constructor(button: HTMLElement, event: HTMLElement) {
    super(button, event);

    this.button.addEventListener("click", (event) => void this.#click(event));
  }

  async #click(event: Event): Promise<void> {
    event.preventDefault();

    const isEnabled = stringToBool(this.event.dataset.enabled!);

    await dboAction(isEnabled ? "disable" : "enable", "rp\\data\\event\\EventAction")
      .objectIds([this.eventId])
      .dispatch();

    this.event.dataset.enabled = isEnabled ? "false" : "true";

    if (isEnabled) {
      this.button.textContent = this.button.dataset.enableMessage!;
    } else {
      this.button.textContent = this.button.dataset.disableMessage!;
    }

    showNotification();

    fireHandler("dev.daries.rp.event", "refresh", {
      action: "disabled",
      eventId: this.eventId,
    });
  }
}
