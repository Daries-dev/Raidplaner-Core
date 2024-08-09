/**
 * An abstract action, to handle event actions.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

export abstract class AbstractEventAction {
  protected readonly button: HTMLElement;
  protected readonly event: HTMLElement;
  protected readonly eventId: number;

  public constructor(button: HTMLElement, event: HTMLElement) {
    this.button = button;
    this.event = event;
    this.eventId = parseInt(event.dataset.eventId!);
  }
}

export default AbstractEventAction;
