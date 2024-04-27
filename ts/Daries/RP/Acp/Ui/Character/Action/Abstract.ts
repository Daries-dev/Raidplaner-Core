/**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */

export abstract class AbstractCharacterAction {
  protected readonly button: HTMLElement;
  protected readonly characterDataElement: HTMLElement;
  protected readonly characterId: number;

  constructor(button: HTMLElement, characterId: number, characterDataElement: HTMLElement) {
    this.button = button;
    this.characterDataElement = characterDataElement;
    this.characterId = characterId;

    this.executeAction();
  }

  abstract executeAction(): void;
}

export default AbstractCharacterAction;
