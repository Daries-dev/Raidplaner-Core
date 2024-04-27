/**
 * Deletes a given character.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */

import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";
import { dboAction } from "WoltLabSuite/Core/Ajax";
import { show as showNotification } from "WoltLabSuite/Core/Ui/Notification";

export class Delete {
  #characterIds: number[];
  #deleteName: string;
  #successCallback: DeleteCallback;

  constructor(characterIds: number[], successCallback: DeleteCallback, deleteName: string) {
    this.#characterIds = characterIds;
    this.#successCallback = successCallback;
    this.#deleteName = deleteName;
  }

  async delete(): Promise<void> {
    const result = await confirmationFactory().delete(this.#deleteName);
    if (result) {
      await dboAction("delete", "rp\\data\\character\\CharacterAction").objectIds(this.#characterIds).dispatch();
      this.#successCallback();
      showNotification();
    }
  }
}

export default Delete;

type DeleteCallback = () => void;
