/**
 * Manages the item entered form field.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { getPhrase } from "WoltLabSuite/Core/Language";
import { childrenByTag } from "WoltLabSuite/Core/Dom/Traverse";
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import { searchItem } from "../../../Api/Items/SearchItem";
import { confirmationFactory } from "WoltLabSuite/Core/Component/Confirmation";

class Item {
  #addButton: HTMLAnchorElement;
  #itemCharacter: HTMLSelectElement;
  #form: HTMLFormElement;
  #formFieldId: string;
  #itemList: HTMLOListElement;
  #itemName: HTMLInputElement;
  #itemPointAccount: HTMLSelectElement;
  #itemPoints: HTMLInputElement;

  static #pointsRegExp = new RegExp(/^(?=.)([+-]?([0-9]*)(\.([0-9]+))?)$/);

  constructor(formFieldId: string, existingItems: ItemData[]) {
    this.#formFieldId = formFieldId;

    this.#itemList = document.getElementById(`${this.#formFieldId}_itemList`) as HTMLOListElement;
    if (this.#itemList === null) {
      throw new Error(`Cannot find item list for items field with id '${this.#formFieldId}'.`);
    }

    this.#itemName = document.getElementById(`${this.#formFieldId}_itemName`) as HTMLInputElement;
    if (this.#itemName === null) {
      throw new Error(`Cannot find item name for items field with id '${this.#formFieldId}'.`);
    }

    this.#itemPointAccount = document.getElementById(`${this.#formFieldId}_pointAccount`) as HTMLSelectElement;
    if (this.#itemPointAccount === null) {
      throw new Error(`Cannot find item point account for items field with id '${this.#formFieldId}'.`);
    }

    this.#itemCharacter = document.getElementById(`${this.#formFieldId}_character`) as HTMLSelectElement;
    if (this.#itemCharacter === null) {
      throw new Error(`Cannot find item character for items field with id '${this.#formFieldId}'.`);
    }

    this.#itemPoints = document.getElementById(`${this.#formFieldId}_points`) as HTMLInputElement;
    if (this.#itemPoints === null) {
      throw new Error(`Cannot find item points for items field with id '${this.#formFieldId}'.`);
    }

    this.#addButton = document.getElementById(`${this.#formFieldId}_addButton`) as HTMLAnchorElement;
    if (this.#addButton === null) {
      throw new Error(`Cannot find add button for items field with id '${this.#formFieldId}'.`);
    }
    this.#addButton.addEventListener("click", () => void this.#addItem());

    this.#form = this.#itemList.closest("form")!;
    if (this.#form === null) {
      throw new Error(`Cannot find form element for items field with id '${this.#formFieldId}'.`);
    }
    this.#form.addEventListener("submit", () => this.#submit());

    existingItems.forEach((data) => this.#addItemByData(data));
  }

  /**
   * Adds a set of item.
   *
   * If the item data is invalid, an error message is shown and no item set is added.
   */
  async #addItem(): Promise<void> {
    if (!this.#validateInput()) {
      return;
    }

    const response = await searchItem(this.#itemName.value);
    if (!response.ok) {
      const validationError = response.error.getValidationError();
      if (validationError === undefined) {
        throw response.error;
      }
      return;
    }

    const item: ItemData = {
      characterId: parseInt(this.#itemCharacter.value),
      characterName: this.#itemCharacter.textContent!,
      itemId: response.value.itemID,
      itemName: response.value.itemName,
      pointAccountId: parseInt(this.#itemPointAccount.value),
      pointAccountName: this.#itemPointAccount.textContent!,
      points: parseFloat(this.#itemPoints.value),
    };

    this.#addItemByData(item);

    this.#emptyInput();
    this.#itemName.focus();
  }

  /**
   * Adds a item to the item list using the given item data.
   */
  #addItemByData(itemData: ItemData): void {
    // add item to list
    const listItem = document.createElement("li");
    listItem.dataset.itemId = itemData.itemId.toString();
    listItem.dataset.itemName = itemData.itemName;
    listItem.dataset.itemPointAccountId = itemData.pointAccountId.toString();
    listItem.dataset.itemPointAccountName = itemData.pointAccountName;
    listItem.dataset.itemCharacterId = itemData.characterId.toString();
    listItem.dataset.itemCharacterName = itemData.characterName;
    listItem.dataset.itemPoints = itemData.points.toString();
    listItem.innerHTML = ` ${getPhrase("rp.item.form.field", {
      itemName: itemData.itemName,
      pointAccountName: itemData.pointAccountName,
      characterName: itemData.characterName,
      points: itemData.points,
    })}`;

    // add delete button
    const deleteButton = document.createElement("button");
    deleteButton.type = "button";
    deleteButton.innerHTML = '<fa-icon size="16" name="times"></fa-icon>';
    deleteButton.title = getPhrase("wcf.global.button.delete");
    deleteButton.classList.add("jsTooltip");
    deleteButton.addEventListener("click", (ev) => this.#removeItem(ev));
    listItem.insertAdjacentElement("afterbegin", deleteButton);

    this.#itemList.appendChild(listItem);
  }

  /**
   * Empties the input fields.
   */
  #emptyInput(): void {
    this.#itemName.value = "";
    this.#itemPointAccount.options.selectedIndex = 0;
    this.#itemCharacter.options.selectedIndex = 0;
    this.#itemPoints.value = "";
  }

  async #removeItem(event: Event): Promise<void> {
    const item = (event.currentTarget as HTMLElement).closest("LI");

    const result = await confirmationFactory().delete();
    if (result) {
      item?.remove();
    }
  }

  /**
   * Adds all necessary (hidden) form fields to the form when
   * submitting the form.
   */
  #submit(): void {
    childrenByTag(this.#itemList, "LI").forEach((listItem, index) => {
      const itemID = document.createElement("input");
      itemID.type = "hidden";
      itemID.name = `${this.#formFieldId}[${index}][itemID]`;
      itemID.value = listItem.dataset.itemId!;
      this.#form.appendChild(itemID);

      const itemName = document.createElement("input");
      itemName.type = "hidden";
      itemName.name = `${this.#formFieldId}[${index}][itemName]`;
      itemName.value = listItem.dataset.itemName!;
      this.#form.appendChild(itemName);

      const itemPointAccountID = document.createElement("input");
      itemPointAccountID.type = "hidden";
      itemPointAccountID.name = `${this.#formFieldId}[${index}][pointAccountID]`;
      itemPointAccountID.value = listItem.dataset.itemPointAccountId!;
      this.#form.appendChild(itemPointAccountID);

      const itemPointAccountName = document.createElement("input");
      itemPointAccountName.type = "hidden";
      itemPointAccountName.name = `${this.#formFieldId}[${index}][pointAccountName]`;
      itemPointAccountName.value = listItem.dataset.itemPointAccountName!;
      this.#form.appendChild(itemPointAccountName);

      const itemCharacterID = document.createElement("input");
      itemCharacterID.type = "hidden";
      itemCharacterID.name = `${this.#formFieldId}[${index}][characterID]`;
      itemCharacterID.value = listItem.dataset.itemCharacterId!;
      this.#form.appendChild(itemCharacterID);

      const itemCharacterName = document.createElement("input");
      itemCharacterName.type = "hidden";
      itemCharacterName.name = `${this.#formFieldId}[${index}][characterName]`;
      itemCharacterName.value = listItem.dataset.itemCharacterName!;
      this.#form.appendChild(itemCharacterName);

      const itemPoints = document.createElement("input");
      itemPoints.type = "hidden";
      itemPoints.name = `${this.#formFieldId}[${index}][points]`;
      itemPoints.value = listItem.dataset.itemPoints!;
      this.#form.appendChild(itemPoints);
    });
  }

  /**
   * Returns `true` if the currently entered item data is valid.
   * Otherwise `false` is returned and relevant error messages are
   * shown.
   */
  #validateInput(): boolean {
    return this.#validateItemName() && this.#validateItemPoints();
  }

  /**
   * Returns `true` if the currently entered item name is
   * valid. Otherwise `false` is returned and an error message is
   * shown.
   */
  #validateItemName(): boolean {
    const itemName = this.#itemName.value;

    if (itemName === "") {
      DomUtil.innerError(this.#itemName, getPhrase("wcf.global.form.error.empty"));
      return false;
    }

    // check if item has already been added
    const duplicate = childrenByTag(this.#itemList, "LI").some((listItem) => {
      listItem.dataset.itemName === itemName && listItem.dataset.itemCharacterId === this.#itemCharacter.value;
    });

    if (duplicate) {
      DomUtil.innerError(this.#itemName, getPhrase("rp.item.error.duplicate"));
      return false;
    }

    // remove outdated errors
    DomUtil.innerError(this.#itemName, "");

    return true;
  }

  /**
   * Returns `true` if the currently entered item points is
   * valid. Otherwise `false` is returned and an error message is
   * shown.
   */
  #validateItemPoints(): boolean {
    const itemPoints = this.#itemPoints.value;

    if (itemPoints === "") {
      DomUtil.innerError(this.#itemPoints, getPhrase("wcf.global.form.error.empty"));
      return false;
    }

    if (!Item.#pointsRegExp.test(itemPoints)) {
      DomUtil.innerError(this.#itemPoints, getPhrase("rp.item.points.error.format"));
      return false;
    }

    // remove outdated errors
    DomUtil.innerError(this.#itemPoints, "");

    return true;
  }
}

export = Item;

interface ItemData {
  characterId: number;
  characterName: string;
  itemId: number;
  itemName: string;
  pointAccountId: number;
  pointAccountName: string;
  points: number;
}