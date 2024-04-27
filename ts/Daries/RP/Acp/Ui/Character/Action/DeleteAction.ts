/**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */

import AbstractCharacterAction from "./Abstract";
import Delete from "./Handler/Delete";

export class DeleteAction extends AbstractCharacterAction {
  executeAction(): void {
    if (typeof this.button.dataset.characterName !== "string") {
      throw new Error("The button does not provide a characterName.");
    }

    this.button.addEventListener("click", (event) => {
      event.preventDefault();

      const deleteHandler = new Delete(
        [this.characterId],
        () => {
          this.characterDataElement.remove();
        },
        this.button.dataset.characterName!,
      );
      void deleteHandler.delete();
    });
  }
}

export default DeleteAction;
