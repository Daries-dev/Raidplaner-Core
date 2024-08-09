/**
 * Handles a event restore button.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import AbstractEventAction from "./Abstract";
import { dboAction } from "WoltLabSuite/Core/Ajax";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";
import { fire as fireHandler } from "WoltLabSuite/Core/Event/Handler";

export class RestoreAction extends AbstractEventAction {
  public constructor(button: HTMLElement, event: HTMLElement) {
    super(button, event);

    this.button.addEventListener("click", (event) => void this.#click(event));
  }

  /**
   * Handles the click event for the button.
   */
  async #click(event: Event): Promise<void> {
    event.preventDefault();

    const title = this.event.dataset.title!;
    const result = await confirmationFactory().restore(title);

    if (result) {
      await dboAction("restore", "rp\\data\\event\\EventAction").objectIds([this.eventId]).dispatch();

      this.event.dataset.deleted = "false";

      showNotification();

      fireHandler("dev.daries.rp.event", "refresh", {
        action: "restore",
        eventId: this.eventId,
      });
    }
  }
}
