/**
 * Handles a event trash button.
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

export class TrashAction extends AbstractEventAction {
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
    const { result } = await confirmationFactory().softDelete(title);

    if (result) {
      await dboAction("trash", "rp\\data\\event\\EventAction").objectIds([this.eventId]).dispatch();

      this.event.dataset.deleted = "true";

      showNotification();

      fireHandler("dev.daries.rp.event", "refresh", {
        action: "trash",
        eventId: this.eventId,
      });
    }
  }
}
